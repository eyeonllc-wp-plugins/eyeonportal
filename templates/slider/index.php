<?php $unique = uniqid(); ?>

<div class="mycenterdeals-wrapper mcd_slider_wrapper">
	<div class="mcd_slider mcd_slider_<?= $unique ?> owl-carousel owl-theme">
		<?php foreach( $this->mcd_settings['slider_items'] as $slider_item ) : ?>
			<div class="mcd_slider_item">
				<div class="mcd_slider_content">
					<?php
					if( $this->mcd_settings['slider_shortcode_atts']['type'] == 'events' ) {
						include MCD_PLUGIN_PATH.'templates/slider/item/event.php';
					} elseif( $this->mcd_settings['slider_shortcode_atts']['type'] == 'deals' ) {
						include MCD_PLUGIN_PATH.'templates/slider/item/deal.php';
					} elseif( $this->mcd_settings['slider_shortcode_atts']['type'] == 'careers' ) {
						include MCD_PLUGIN_PATH.'templates/slider/item/career.php';
					} elseif( $this->mcd_settings['slider_shortcode_atts']['type'] == 'stores' ) {
						include MCD_PLUGIN_PATH.'templates/slider/item/store.php';
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	var owl_options_<?= $unique ?> = {
		nav: true,
		navText: ['',''],
		dots: <?= $this->mcd_settings['slider_shortcode_atts']['show-dots'] ?>,
		dotsEach: true,
		loop: <?= (count($this->mcd_settings['slider_items']) > $this->mcd_settings['slider_shortcode_atts']['show'] ? 'true' : 'false' ) ?>,
		items: <?= $this->mcd_settings['slider_shortcode_atts']['show'] ?>,
		autoplay: <?= $this->mcd_settings['slider_shortcode_atts']['auto-slide'] ?>,
		autoplayTimeout: <?= $this->mcd_settings['slider_shortcode_atts']['auto-slide-speed'] ?>,
		// autoplayHoverPause: true,
		slideBy: 1,
		touchDrag: true,
		mouseDrag: true,
		center: false,
		margin: 20,
		itemElement: 'mcd_slider_item',
		responsive: {
			0: {
				items: <?= $this->mcd_settings['slider_shortcode_atts']['items-on-mobile'] ?>,
			},
			420: {
				items: 2,
			},
			600: {
				items: 3,
			},
			1024: {
				items: <?= $this->mcd_settings['slider_shortcode_atts']['show'] ?>,
			},
		}
	};
	jQuery('.mcd_slider_<?= $unique ?>').owlCarousel(owl_options_<?= $unique ?>);
});
</script>