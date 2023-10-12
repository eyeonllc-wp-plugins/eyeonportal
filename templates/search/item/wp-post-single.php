<a class="mcd_search_item mcd_deal" href="<?= get_permalink($post->ID) ?>">
	<?php if( has_post_thumbnail($post->ID) ) : ?>
	<span class="image-section">
		<span class="mcd-image mcd_shadow_img">
			<img src="<?= get_the_post_thumbnail_url($post->ID) ?>" class="mcd_pad0" />
		</span>
	</span>
	<?php endif; ?>

	<span class="mcd_details">
		<h4 class="title"><?= $post->post_title ?></h4>
		<span class="description"><?= make_excerpt($post->post_content) ?></span>
	</span>
</a>