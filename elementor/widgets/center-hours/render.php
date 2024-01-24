<?php
$settings = $this->get_settings_for_display();
$fields = [];
$filtered_settings = array_intersect_key($settings, array_flip($fields));
$unique_id = uniqid();
?>

<div id="eyeon-center-hours-<?= $unique_id ?>" class="eyeon-center-hours eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">
    <div class="center-hours"></div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonCenterHours = $('#eyeon-center-hours-<?= $unique_id ?>');
    const centerHours = eyeonCenterHours.find('.center-hours');

    fetch_center_hours();

    function fetch_center_hours() {
      $.ajax({
        url: "<?= MCD_API_CENTER_HOURS ?>",
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.sets) {
            setup_hours(response);
          }
        }
      });
    }

    function setup_hours(data) {
      eyeonCenterHours.removeClass('eyeon-loader').find('.eyeon-wrapper').removeAttr('style');
      renderHours();
    }

    function renderHours() {
      centerHours.empty();
    }

  });
</script>