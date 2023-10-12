<div class="products" ng-show="products.length>0">
	<a href="<?= mcd_single_page_url('mycenterproduct') ?>{{product.custom_slug}}" class="product" ng-repeat="product in products">
		<span class="image">
			<img ng-src="{{product.media.items[0].imageUrl}}" />
		</span>
		<span class="product_title" ng-bind-html="product.title"></span>
		<span class="product_prices">
			<span class="price"><?= MCP_CURRENCY ?>{{product.variants.items[0].price | number:2}}</span>
			<span class="compare_price" ng-show="product.variants.items[0].compareAtPrice>0"><?= MCP_CURRENCY ?>{{product.variants.items[0].compareAtPrice | number:2}}</span>
		</span>
	</a>
</div>
