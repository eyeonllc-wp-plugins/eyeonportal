<?php
$retailer_deals = array();
if( $this->mcd_settings['stores_single_deals'] ) {
	$req_url = MCD_API_DEALS.'?limit='.$this->mcd_settings['stores_single_deals_fetch'].'&page=1&retailer_id='.$mycenterstore['id'];
	$retailer_deals = mcd_api_data($req_url);
}
?>

<?php if( $this->mcd_settings['stores_single_deals'] && isset($retailer_deals['items']) && count($retailer_deals['items']) > 0 ) : ?>
	<div class="eyeon-deals">
		<h3 class="title">Deals</h3>
		<div class="deals-list grid<?= $this->mcd_settings['stores_single_deals_per_row'] ?>">
			<?php foreach ($retailer_deals['items'] as $key => $deal) : ?>
			<a href="<?= mcd_single_page_url('mycenterdeal').$deal['slug'] ?>" class="deal">
        <div class="image">
          <img src="<?= $deal['media']['url'] ?>" alt="<?= $deal['title'] ?>" />
        </div>
        <div class="deal-content">
          <div class="details">
            <h3 class="deal-title"><?= $deal['title'] ?></h3>
            <div class="deal-expiry">Valid until <?= eyeon_format_date($deal['end_date']) ?></div>
          </div>
        </div>
      </a>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>	
