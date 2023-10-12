<?php
$mycenterproduct = $this->mcd_settings['mycenterproduct'];
$mycenterstore = @$this->mcd_settings['mycenterstore'];
?>

<?php if( is_array($mycenterproduct) ) : ?>

<div class="mycenterdeals-wrapper mycenterproduct">
	<div id="mcp-product">
		<div class="mcp-product-cols">
			<div class="mcp-product-image-col">
				<div class="mcp-product-image">
					<img src="<?= $mycenterproduct['media']['items'][0]['imageUrl'] ?>" />
				</div>

				<?php if( count($mycenterproduct['media']['items']) > 1 ) : ?>
					<div class="mcp-product-images">
						<?php foreach( $mycenterproduct['media']['items'] as $key=>$media ) : ?>
							<img src="<?= $media['imageUrl'] ?>" class="<?= ($key==0?'active':'') ?>" />
						<?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function($) {
						jQuery('#mcp-product .mcp-product-images img').click(function() {
							jQuery('#mcp-product .mcp-product-images img.active').removeClass('active');
							jQuery(this).addClass('active');

							var image_url = $(this).attr('src');
							jQuery('#mcp-product .mcp-product-image img').attr('src', image_url);
						});
					});
					</script>
				<?php endif; ?>
			</div>

			<div class="mcp-product-details">
				<div class="mcp-product-title"><?= $mycenterproduct['title'] ?></div>
				<span class="mcp-product-prices">
					<span class="price"><?= MCP_CURRENCY.number_format($mycenterproduct['variants']['items'][0]['price'], 2) ?></span>
					<?php if( intval($mycenterproduct['variants']['items'][0]['compareAtPrice']) > 0 ) : ?>
						<span class="compare-price"><?= MCP_CURRENCY.number_format($mycenterproduct['variants']['items'][0]['compareAtPrice'],2) ?></span>
					<?php endif; ?>
				</span>
				<div class="mcp-product-description"><?= $mycenterproduct['bodyHtml'] ?></div>
				<div class="actions_btns">
					<a href="<?= $mycenterproduct['affiliateUrl'] ?>" target="_blank" class="mcp_btn rounded buy_online_btn">Buy Online</a>
				</div>
			</div>

			<?php if( $mycenterstore ) : ?>
				<div class="mcp-product-single-store">
					<a href="<?= mcd_single_page_url('mycenterstore').$mycenterstore['slug'] ?>" class="mcp-retailer-image-div">
						<img src="<?= $mycenterstore['image'] ?>" />
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php include(MCD_PLUGIN_PATH.'templates/products/more-products.php') ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	ga_event_tracking('ProductSingle', $('#mcp-product .mcp-product-title').text());

	jQuery('.buy_online_btn').click(function() {
		var price = parseFloat(<?= $mycenterproduct['variants']['items'][0]['price'] ?>);
		var compare_price = parseFloat(<?= $mycenterproduct['variants']['items'][0]['compareAtPrice'] ?>);
		if( compare_price > 0 ) {
			price = compare_price;
		}
		ga_event_tracking('ProductSingleBuyOnlineClicks', $('#mcp-product .mcp-product-title').text(), price);
	});
});
</script>

<?php endif; ?>
