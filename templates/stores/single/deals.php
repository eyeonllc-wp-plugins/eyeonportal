<?php
$retailer_deals = array();
if( $this->mcd_settings['stores_single_deals'] ) {
	$req_url = MCD_API_DEALS.'?limit='.$this->mcd_settings['stores_single_deals_fetch'].'&page=1&retailer_id='.$mycenterstore['id'];
	$retailer_deals = mcd_api_data($req_url);
}
?>

<?php if( $this->mcd_settings['stores_single_deals'] && isset($retailer_deals['items']) && count($retailer_deals['items']) > 0 ) : ?>
	<div id="mcd-retailer-deals">
		<h3>Deals</h3>
		<div class="mcd-retailer-deals-wrapper grid<?= $this->mcd_settings['stores_single_deals_per_row'] ?>">
			<?php foreach ($retailer_deals['items'] as $key => $deal) : ?>
			<a class="mcd-deal-item" href="<?= mcd_single_page_url('mycenterdeal').$deal['slug'] ?>">
				<span class="mcd-deal-image">
					<img src="<?= $deal['media']['url'] ?>" />
					<span class="mcd-retailer-logo">
						<img src="<?= $deal['retailer']['media']['url'] ?>" />
					</span>
				</span>
				<span class="mcd-deal-content">
					<span class="mcd-deal-details">
						<span class="mcd-retailer-name"><?= $deal['retailer']['name'] ?></span>
						<span class="mcd-sep"></span>
						<span class="mcd-deal-title"><?= $deal['title'] ?></span>
						<span class="mcd-deal-end-date">Valid until <?= date("M d, Y", strtotime($deal['end_date'])) ?></span>
					</span>
				</span>
			</a>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>	
