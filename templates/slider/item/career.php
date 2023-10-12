<a class="mcd-career-item" href="<?= mcd_single_page_url('mycentercareer').$slider_item['slug'] ?>">
	<span class="mcd-retailer-image">
		<img src="<?= $slider_item['retailer_logo'] ?>" />
	</span>

	<?php if( $this->mcd_settings['slider_shortcode_atts']['metadata'] == "true" ) : ?>
	<span class="mcd-career-content">
		<span class="mcd-career-details">
			<span class="mcd-career-title"><?= $slider_item['career_title'] ?></span>
		</span>
	</span>
	<?php endif; ?>
</a>