<?php
$mcd_center_id = isset( $this->mcd_settings['center_id'] ) ? $this->mcd_settings['center_id'] : 0;
?>

<div ng-app="MyCenterPortalApp" ng-controller="CareersCtrl" data-url="<?= MCD_API_CAREERS.'?limit=100&page=1' ?>" data-center-id="<?= $this->mcd_settings['center_id'] ?>">
	<div class="mycenterdeals-wrapper mycentercareers">
		<div id="mcd-filters" ng-hide="busy || data.error" class="hide" ng-cloak>
			<span id="mcd-filters-title">Sort by: </span>
			<div id="mcd-filters-order" class="clearfix">
				<a class="mcd-filter-order" ng-click="selectType('recent')" ng-class="{ active: selectedType=='recent' }">Recently Added</a>
				<a class="mcd-filter-order" ng-click="selectType('expiry')" ng-class="{ active: selectedType=='expiry' }">Ending Soon</a>
			</div>
		</div>

		<div id="mcd-error-msg" ng-show="data.error" ng-cloak>
			<div class="mcd-alert">{{ data.error }}</div>
		</div>

		<div id="mycenterdeals-wrapper" ng-class="{loading: busy}">
			<div id="mycentercareers">
				<a class="mcd-career-item" ng-repeat="career in careers" href="<?= mcd_single_page_url('mycentercareer') ?>{{ career.slug }}" ng-cloak>
					<span class="mcd-retailer-image">
						<img ng-src="{{ career.retailer.media.url }}" />
					</span>
					<span class="mcd-career-content">
						<span class="mcd-career-details">
							<span class="mcd-career-title">{{ career.title }}</span>
						</span>
					</span>
				</a>
			</div>
		</div>
	</div>
</div>

