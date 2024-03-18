<?php
$settings = $this->get_settings_for_display();
$filtered_settings = array_intersect_key($settings, array_flip([
  'map_height',
]));
$unique_id = uniqid();

$mapboxProps = array(
  'config' => Array(
    'CENTER_ID' => $mcd_settings['center_id'],
    'ROLE' => 'WP_SITE',
    // 'KIOSK' => '',
    // 'STYLE' => '2D',
  ),
  'webApiURI' => API_BASE_URL,
);

$selected_store = get_query_var('mcdmapretailer', '');
if($selected_store) {
  $mapboxProps['config']['SELECTED_RETAILER_SLUG'] = $selected_store;
}

eyeon_debug($mapboxProps, false, false);

$encodedProps = htmlspecialchars(json_encode($mapboxProps), ENT_QUOTES, 'UTF-8');
?>

<div id="eyeon-map-<?= $unique_id ?>" class="eyeon-map">
  <div class="eyeon-wrapper">
      <div id="root" data-props="<?= $encodedProps ?>"></div>
    </div>
  </div>
</div>
