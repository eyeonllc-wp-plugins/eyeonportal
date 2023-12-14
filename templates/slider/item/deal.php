<a class="mcd-deal-item <?= ( $this->mcd_settings['slider_shortcode_atts']['metadata'] == "true" ? '' : 'nometadata' ) ?>" href="<?= mcd_single_page_url('mycenterdeal').$slider_item['slug'] ?>">
	<span class="mcd-deal-image">
		<img src="<?= $slider_item['media']['url'] ?>">

		<span class="mcd-retailer-logo">
			<img src="<?= $slider_item['retailer']['media']['url'] ?>">
		</span>
	</span>

	<?php if( $this->mcd_settings['slider_shortcode_atts']['metadata'] == "true" ) : ?>
	<span class="mcd-deal-content">
		<span class="eyeon-deal-details">
			<span class="mcd-retailer-name"><?= $slider_item['retailer']['name'] ?></span>
			<span class="mcd-sep"></span>
			<span class="mcd-deal-title"><?= $slider_item['title'] ?></span>
			<span class="mcd-deal-end-date">Valid until <?= date("M d, Y", strtotime($slider_item['end_date'])) ?></span>
		</span>
	</span>
	<?php endif; ?>
</a>