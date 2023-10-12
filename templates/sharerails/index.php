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
				<a class="mcp-retailer-item"
					ng-repeat="retailer in retailers"
					ng-class="{featured: $index%10==0 || $index%7==0}"
					href="{{ retailer.store_url }}"
					title="{{retailer.retail_name}}"
					ng-cloak>
					<span class="mcp-retailer-wrapper mcd_shadow_img">
						<img ng-src="{{retailer.product_image}}" class="retailer-product-image" />
						<span class="retailer-logo">
							<img ng-src="{{retailer.sharerails_logo_url}}" />
						</span>
					</span>
				</a>
			</div>
		</div>

		<div id="mcd-load-more-div" ng-class="{loading: busy}"></div>
	</div>

</div>

