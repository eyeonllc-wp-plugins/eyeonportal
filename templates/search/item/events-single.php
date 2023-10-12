<a class="mcd_search_item mcd_event" href="<?= mcd_single_page_url('mycenterevent').$single['slug'] ?>">
	<span class="image-section">
		<span class="mcd-image mcd_shadow_img">
			<img src="<?= $single['event_image'] ?>" class="mcd_pad0" />
		</span>
	</span>
	<span class="mcd_details">
		<h4 class="title"><?= $single['event_title'] ?></h4>
		<span class="description"><?= $single['event_excerpt'] ?></span>
		<span class="more_details">
			<?= $single['event_start_date'] ?> - <?= $single['event_end_date'] ?><br>
			<?php if( !empty($single['event_start_time']) ) : ?>
				<?= $single['event_start_time'] ?> - <?= $single['event_end_time'] ?>
			<?php endif; ?>
		</span>
	</span>
</a>

