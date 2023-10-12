<a class="mcd_search_item mcd_store" href="<?= mcd_single_page_url('mycenterstore').$single['slug'] ?>">
	<span class="image-section">
		<span class="mcd-image mcd_shadow_img">
			<img src="<?= $single['retailer_logo'] ?>" />
		</span>
	</span>
	<span class="mcd_details">
		<h4 class="title"><?= $single['retailer_name'] ?></h4>
		<span class="description"><?= strip_tags($single['description']) ?></span>
	</span>
</a>
