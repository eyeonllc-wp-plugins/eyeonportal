<?php
// $selected_retailer_slug = get_query_var('mcdmapretailer', 0);
$aContext = array(
	'ssl' => array(
		'verify_peer' => false,
	),
);
$cxContext = stream_context_create($aContext);
$url = API_BASE_URL.'api/mapit2/view?center='.$this->mcd_settings['center_id'].'&without_layout=1&role=WP_SITE';

$selected_store = get_query_var('mcdmapretailer', '');
if( !empty($selected_store) ) {
	$url .= '&store='.$selected_store;
}

if( wp_is_mobile() ) {
	$url .= '&device=mobile';
} else {
	$url .= '&device=desktop';
}
echo file_get_contents($url, false, $cxContext);

?>

