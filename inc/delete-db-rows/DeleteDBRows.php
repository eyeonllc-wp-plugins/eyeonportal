<?php

if (!defined('ABSPATH')) {
    exit;
}

class EyeOn_Delete_DB_Rows {
    
    private $progress_file;
    private $log_file;
    private $storage_dir;
    private $batch_size = 100;
    private $grace_period = 1000;
    
    public function __construct() {
        // Store files in wp-content/uploads/eyeon-transient-cleanup/ to persist across plugin updates
        $upload_dir = wp_upload_dir();
        $this->storage_dir = $upload_dir['basedir'] . '/eyeon-transient-cleanup';
        
        // Create directory if it doesn't exist
        if (!file_exists($this->storage_dir)) {
            wp_mkdir_p($this->storage_dir);
        }
        
        $this->progress_file = $this->storage_dir . '/progress.json';
        $this->log_file = $this->storage_dir . '/deletion_log.txt';
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_eyeon_delete_transients_batch', array($this, 'ajax_delete_batch'));
        add_action('wp_ajax_eyeon_reset_progress', array($this, 'ajax_reset_progress'));
        add_action('wp_ajax_eyeon_get_progress', array($this, 'ajax_get_progress'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',
            'Delete EyeOn Transients',
            'Delete EyeOn Transients',
            'manage_options',
            'eyeon-delete-transients',
            array($this, 'render_admin_page')
        );
    }
    
    public function get_progress() {
        if (file_exists($this->progress_file)) {
            $content = file_get_contents($this->progress_file);
            $data = json_decode($content, true);
            if ($data) {
                return $data;
            }
        }
        return array(
            'offset' => 0,
            'total_deleted' => 0,
            'total_scanned' => 0,
            'status' => 'not_started',
            'last_updated' => null
        );
    }
    
    public function save_progress($data) {
        $data['last_updated'] = current_time('mysql');
        file_put_contents($this->progress_file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function log_message($message) {
        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] {$message}\n";
        file_put_contents($this->log_file, $log_entry, FILE_APPEND);
    }
    
    public function ajax_get_progress() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'), 403);
        }
        
        $progress = $this->get_progress();
        wp_send_json_success($progress);
    }
    
    public function ajax_reset_progress() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'), 403);
        }
        
        // Delete progress and log files
        if (file_exists($this->progress_file)) {
            unlink($this->progress_file);
        }
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
        
        $this->log_message('Progress reset by admin');
        
        wp_send_json_success(array('message' => 'Progress reset successfully'));
    }
    
    public function ajax_delete_batch() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'), 403);
        }
        
        global $wpdb;
        
        // Get batch_size from POST or use default
        $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : $this->batch_size;
        if ($batch_size < 1) $batch_size = $this->batch_size;
        if ($batch_size > 5000) $batch_size = 5000; // Max limit for safety
        
        $progress = $this->get_progress();
        $offset = $progress['offset'];
        
        // Get batch of records
        $query = $wpdb->prepare(
            "SELECT option_id, option_name FROM {$wpdb->options} ORDER BY option_id ASC LIMIT %d, %d",
            $offset,
            $batch_size
        );
        
        $results = $wpdb->get_results($query);
        
        if (empty($results)) {
            // No more records to process
            $progress['status'] = 'completed';
            $this->save_progress($progress);
            $this->log_message('Deletion process completed. Total deleted: ' . $progress['total_deleted'] . ', Total scanned: ' . $progress['total_scanned']);
            
            wp_send_json_success(array(
                'status' => 'completed',
                'message' => 'All records processed',
                'total_deleted' => $progress['total_deleted'],
                'total_scanned' => $progress['total_scanned'],
                'offset' => $offset,
                'select_query' => $query
            ));
            return;
        }
        
        // Filter records that match our pattern
        $ids_to_delete = array();
        $names_to_delete = array();
        
        foreach ($results as $row) {
            if (strpos($row->option_name, '_transient_eyeon_api_session_') !== false || 
                strpos($row->option_name, '_transient_timeout_eyeon_api_session_') !== false) {
                $ids_to_delete[] = $row->option_id;
                $names_to_delete[] = $row->option_name;
            }
        }
        
        $deleted_count = 0;
        $delete_query = null;
        
        if (!empty($ids_to_delete)) {
            // Delete matching records
            $ids_placeholder = implode(',', array_map('intval', $ids_to_delete));
            $delete_query = "DELETE FROM {$wpdb->options} WHERE option_id IN ({$ids_placeholder})";
            $deleted_count = $wpdb->query($delete_query);
            
            // Log deletion
            // $this->log_message("Deleted {$deleted_count} records: " . implode(', ', $names_to_delete));
            $this->log_message("Deleted {$deleted_count} records");
        }
        
        // Update progress
        // Always advance offset - we've processed this batch regardless of deletions
        $progress['offset'] = $offset + $batch_size;
        $progress['total_deleted'] += $deleted_count;
        
        $progress['total_scanned'] += count($results);
        $progress['status'] = 'in_progress';
        $this->save_progress($progress);
        
        wp_send_json_success(array(
            'status' => 'in_progress',
            'message' => "Processed batch at offset {$offset}",
            'batch_size' => count($results),
            'deleted_in_batch' => $deleted_count,
            'deleted_names' => $names_to_delete,
            'total_deleted' => $progress['total_deleted'],
            'total_scanned' => $progress['total_scanned'],
            'next_offset' => $progress['offset'],
            'select_query' => $query,
            'delete_query' => $delete_query
        ));
    }
    
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        
        $progress = $this->get_progress();
        ?>
        <div class="wrap">
            <h1>Delete EyeOn Session Transients</h1>
            
            <div class="card" style="max-width: 100%; padding: 20px;">
                <h2>About</h2>
                <p>This tool deletes old session transients from the <code>wp_options</code> table that match:</p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><code>_transient_eyeon_api_session_*</code></li>
                    <li><code>_transient_timeout_eyeon_api_session_*</code></li>
                </ul>
                <p>The process runs in configurable batches to prevent timeout issues. Adjust the settings below before starting.</p>
            </div>
            
            <div class="card" style="max-width: 100%; padding: 20px; margin-top: 20px;">
                <h2>Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="batch-size">Batch Size</label></th>
                        <td>
                            <input type="number" id="batch-size" value="<?php echo esc_attr($this->batch_size); ?>" min="1" max="5000" style="width: 100px;" />
                            <p class="description">Number of records to fetch per batch (1-5000)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="grace-period">Grace Period (ms)</label></th>
                        <td>
                            <input type="number" id="grace-period" value="<?php echo esc_attr($this->grace_period); ?>" min="1000" max="9000" style="width: 100px;" />
                            <p class="description">Delay between batches in milliseconds (100-9000)</p>
                        </td>
                    </tr>
                </table>
                
                <h2>Controls</h2>
                <p>
                    <button id="btn-start" class="button button-primary">Start / Resume</button>
                    <button id="btn-stop" class="button">Stop</button>
                    <button id="btn-reset" class="button button-secondary">Reset Progress</button>
                </p>
            </div>
            
            <div class="card" style="max-width: 100%; padding: 20px; margin-top: 20px;">
                <h2>Progress</h2>
                <table class="widefat" style="max-width: 400px;">
                    <tr>
                        <th>Status</th>
                        <td id="status"><?php echo esc_html($progress['status']); ?></td>
                    </tr>
                    <tr>
                        <th>Current Offset</th>
                        <td id="offset"><?php echo esc_html($progress['offset']); ?></td>
                    </tr>
                    <tr>
                        <th>Total Scanned</th>
                        <td id="total-scanned"><?php echo esc_html($progress['total_scanned']); ?></td>
                    </tr>
                    <tr>
                        <th>Total Deleted</th>
                        <td id="total-deleted"><?php echo esc_html($progress['total_deleted']); ?></td>
                    </tr>
                    <tr>
                        <th>Last Updated</th>
                        <td id="last-updated"><?php echo esc_html($progress['last_updated'] ?? 'Never'); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="card" style="max-width: 100%; padding: 20px; margin-top: 20px;">
                <h2>Live Log</h2>
                <div id="log-container" style="background: #1d2327; color: #00ff00; padding: 15px; height: calc(100vh - 100px); overflow-y: auto; font-family: monospace; font-size: 12px; border-radius: 4px;">
                    <div id="log-output" style="white-space: nowrap; overflow-x: auto; height: 100%;">Waiting to start...</div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
        (function($) {
            var isRunning = false;
            var shouldStop = false;
            
            function getBatchSize() {
                var val = parseInt($('#batch-size').val()) || <?php echo $this->batch_size; ?>;
                if (val < 1) val = 1;
                if (val > 1000) val = 1000;
                return val;
            }
            
            function getGracePeriod() {
                var val = parseInt($('#grace-period').val()) || <?php echo $this->grace_period; ?>;
                if (val < 100) val = 100;
                if (val > 60000) val = 60000;
                return val;
            }
            
            function log(message, type) {
                var timestamp = new Date().toLocaleTimeString();
                var color = '#00ff00';
                if (type === 'error') color = '#ff4444';
                if (type === 'warning') color = '#ffaa00';
                if (type === 'info') color = '#4499ff';
                if (type === 'success') color = '#44ff44';
                
                var logLine = '<div style="color: ' + color + '">[' + timestamp + '] ' + message + '</div>';
                $('#log-output').append(logLine);
                
                // Auto-scroll to bottom
                // var container = $('#log-container');
                // container.scrollTop(container[0].scrollHeight);
            }
            
            function updateProgress(data) {
                if (data.status) $('#status').text(data.status);
                if (data.next_offset !== undefined) $('#offset').text(data.next_offset);
                if (data.offset !== undefined) $('#offset').text(data.offset);
                if (data.total_scanned !== undefined) $('#total-scanned').text(data.total_scanned);
                if (data.total_deleted !== undefined) $('#total-deleted').text(data.total_deleted);
                $('#last-updated').text(new Date().toLocaleString());
            }
            
            function processBatch() {
                if (shouldStop) {
                    isRunning = false;
                    shouldStop = false;
                    log('Process stopped by user', 'warning');
                    $('#btn-start').prop('disabled', false);
                    $('#btn-stop').prop('disabled', true);
                    $('#batch-size, #grace-period').prop('disabled', false);
                    return;
                }
                
                var batchSize = getBatchSize();
                var gracePeriod = getGracePeriod();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'eyeon_delete_transients_batch',
                        batch_size: batchSize
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            updateProgress(data);
                            
                            // Log the SELECT query
                            if (data.select_query) {
                                log(data.select_query, 'info');
                            }
                            
                            if (data.deleted_in_batch > 0) {
                                // Log the DELETE query
                                if (data.delete_query) {
                                    log(data.delete_query, 'warning');
                                }
                                log('Deleted ' + data.deleted_in_batch + ' records.', 'success');
                            } else {
                                log('Scanned ' + data.batch_size + ' records - no matches found');
                            }
                            
                            if (data.status === 'completed') {
                                isRunning = false;
                                log('=== PROCESS COMPLETED ===', 'success');
                                log('Total scanned: ' + data.total_scanned, 'info');
                                log('Total deleted: ' + data.total_deleted, 'info');
                                $('#btn-start').prop('disabled', false);
                                $('#btn-stop').prop('disabled', true);
                                $('#batch-size, #grace-period').prop('disabled', false);
                            } else {
                                // Continue with next batch after grace period
                                log('Waiting ' + (gracePeriod / 1000) + ' seconds before next batch...', 'warning');
                                setTimeout(processBatch, gracePeriod);
                            }
                        } else {
                            log('Error: ' + (response.data.message || 'Unknown error'), 'error');
                            isRunning = false;
                            $('#btn-start').prop('disabled', false);
                            $('#btn-stop').prop('disabled', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        log('AJAX Error: ' + error, 'error');
                        isRunning = false;
                        $('#btn-start').prop('disabled', false);
                        $('#btn-stop').prop('disabled', true);
                        $('#batch-size, #grace-period').prop('disabled', false);
                    }
                });
            }
            
            $('#btn-start').on('click', function() {
                if (isRunning) return;
                
                isRunning = true;
                shouldStop = false;
                $('#btn-start').prop('disabled', true);
                $('#btn-stop').prop('disabled', false);
                $('#batch-size, #grace-period').prop('disabled', true);
                
                var batchSize = getBatchSize();
                var gracePeriod = getGracePeriod();
                
                log('=== STARTING DELETION PROCESS ===', 'info');
                log('Batch size: ' + batchSize + ' records', 'info');
                log('Grace period: ' + (gracePeriod / 1000) + ' seconds', 'info');
                
                processBatch();
            });
            
            $('#btn-stop').on('click', function() {
                if (!isRunning) return;
                
                shouldStop = true;
                log('Stopping... (will stop after current batch)', 'warning');
            });
            
            $('#btn-reset').on('click', function() {
                if (isRunning) {
                    alert('Please stop the process before resetting.');
                    return;
                }
                
                if (!confirm('Are you sure you want to reset progress? This will start from the beginning.')) {
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'eyeon_reset_progress'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#status').text('not_started');
                            $('#offset').text('0');
                            $('#total-scanned').text('0');
                            $('#total-deleted').text('0');
                            $('#last-updated').text('Never');
                            $('#log-output').html('Progress reset. Ready to start...');
                            log('Progress has been reset', 'info');
                        } else {
                            log('Error resetting progress: ' + response.data.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        log('AJAX Error: ' + error, 'error');
                    }
                });
            });
            
            // Disable stop button initially
            $('#btn-stop').prop('disabled', true);
            
        })(jQuery);
        </script>
        <?php
    }
}

// Initialize the class
new EyeOn_Delete_DB_Rows();

