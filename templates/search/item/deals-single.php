<a class="mcd_search_item mcd_deal" href="<?= mcd_single_page_url('mycenterdeal').$single['slug'] ?>">
	<span class="image-section">
		<span class="mcd-image mcd_shadow_img">
			<img src="<?= $single['deal_image'] ?>" class="mcd_pad0" />
		</span>
	</span>
	<span class="mcd_details">
		<h4 class="title"><?= $single['retailer_name'] ?></h4>
		<span class="description"><?= $single['deal_title'] ?></span>
		<span class="more_details">Valid upto <?= $single['deal_end_date'] ?></span>
	</span>
</a>

