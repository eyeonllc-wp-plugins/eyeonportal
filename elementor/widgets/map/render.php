<?php
$settings = $this->get_settings_for_display();
$filtered_settings = array_intersect_key($settings, array_flip([
  'map_height',
]));
$unique_id = uniqid();

$center = eyeon_get_center();

$mapboxProps = array(
  'config' => Array(
    'CENTER_ID' => intval($center['id']),
    'ROLE' => 'WP_SITE',
    'IMAGE_PROXY_URL' => site_url().'/index.php?eyeonmedia=',
  ),
  'webApiURI' => rest_url('eyeon-portal/map'),
);

// $selected_store_slug = get_query_var('mcdmapretailer', '');
// if($selected_store_slug) {
//   $mapboxProps['config']['SELECTED_RETAILER_SLUG'] = $selected_store_slug;
// }

$selected_store_id = (isset($_GET['r']) && !empty(['r'])) ? $_GET['r'] : null;
if($selected_store_id) {
  $mapboxProps['config']['SELECTED_RETAILER_ID'] = intval($selected_store_id);
}

$encodedProps = htmlspecialchars(json_encode($mapboxProps), ENT_QUOTES, 'UTF-8');
?>

<div id="eyeon-map-<?= $unique_id ?>" class="eyeon-map">
  <div class="eyeon-wrapper">
      <div id="root" data-props="<?= $encodedProps ?>"></div>
    </div>
  </div>
</div>
