<?php
$mcd_center_id = isset( $this->mcd_settings['center_id'] ) ? $this->mcd_settings['center_id'] : 0;
?>

<div ng-app="MyCenterPortalApp" ng-controller="ShareRailsRetailersCtrl" data-url="<?= admin_url('admin-ajax.php') ?>">
	<div class="mycenterdeals-wrapper mcp_sharerails_retailers">
		<div id="mcd-error-msg" ng-show="data.error" ng-cloak>
			<div class="mcd-alert">{{ data.error }}</div>
		</div>

		<div id="mycenterdeals-wrapper" ng-cloak>
			<div id="mycentershopping">
				<div class="mcp-retailer-item {{ record.type }}"
					ng-repeat="record in records"
					ng-class="{featured: $index%10==0 || ($index-7)%10==0}"
					ng-cloak>
					<a href="{{ record.store_url }}?ref=shop" title="{{record.retail_name}}" ng-if="record.type=='retailer'">
						<span class="mcp-retailer-wrapper mcd_shadow_img">
							<img ng-src="{{record.product_image}}" class="retailer-product-image" />
							<span class="retailer-logo">
								<img ng-src="{{record.sharerails_logo_url}}" />
							</span>
						</span>
					</a>
					<a href="{{ record.article_url }}" title="{{record.title}}" ng-if="record.type=='article'">
						<span class="mcp-retailer-wrapper mcd_shadow_img">
							<img ng-src="{{record.image}}" class="retailer-product-image" />
							<span class="retailer-logo">
								<span>{{record.title}}</span>
							</span>
						</span>
					</a>
				</div>
			</div>
		</div>

		<div id="mcd-load-more-div" ng-class="{loading: busy}"></div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	ga_event_tracking('Pages', 'Shop');
});
</script>

