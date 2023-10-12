<!-- Global site tag (gtag.js) - Google Analytics - Live Account -->
<!--
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109087003-3"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-109087003-3', {groups: 'eyeon_mcp_wp_site'});
</script>
-->

<script type="text/javascript">
window.ga_event_tracking = function(event_action, label = '', value = 1) {
	if( typeof gtag == 'function' && label != '' ) {
		var event_options = {
			event_category: '<?= $_SERVER['HTTP_HOST'] ?>',
			event_label: label,
			value: value,
			// send_to: 'eyeon_mcp_wp_site',
		};

		<?php if( !is_user_logged_in() && $mcd_settings['mcd_site_mode'] == 'live' ) : ?>
		gtag('event', event_action, event_options);
		<?php endif; ?>
	}
}
</script>

