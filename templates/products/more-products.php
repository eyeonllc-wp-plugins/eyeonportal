<div id="mcd-retailer-products" ng-app="MyCenterPortalApp" ng-controller="StoreCtrl" data-url="<?= admin_url('admin-ajax.php') ?>">
	<div class="products-wrapper" ng-class="{'mcd_loading_div bottom_space': busy}" ng-cloak>
		<div class="products-content" ng-show="products.length > 0">
			<?php if( $mycenterstore ) : ?>
				<h3 class="align_center">More from <a href="<?= mcd_single_page_url('mycenterstore').$mycenterstore['slug'] ?>"><?= $mycenterstore['retail_name'] ?></a></h3>
			<?php else : ?>
				<h3 class="align_center">Related Products</h3>
			<?php endif; ?>
			<?php include(MCD_PLUGIN_PATH.'templates/products/products-grid.php'); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	function fetch_products() {
		var ajax_data = {
			action: 'sharerails_products_fetch',
			endpoint: 'products',
			params: {
				retailerId: '<?= $mycenterproduct['retailerId'] ?>',
				start: 0,
				limit: <?= intval($this->mcd_settings['product_single_fetch_limit']) ?>,
				sort: 'Random',
			}
		};
		jQuery('[ng-controller="StoreCtrl"]').scope().loadResults(ajax_data, function() {
			// start carousel
		});
	}
	fetch_products();
});
</script>
