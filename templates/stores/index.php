<div ng-app="MyCenterPortalApp" ng-controller="StoresCtrl" data-url="<?= $this->mcd_settings['stores_api_url'] ?>" data-center-id="<?= $this->mcd_settings['center_id'] ?>">
	<div class="mycenterdeals-wrapper mycenterstores">
		<div id="mcd-error-msg" ng-show="data.error" ng-cloak>
			<div class="mcd-alert">{{ data.error }}</div>
		</div>

		<div id="mycenterdeals-wrapper" ng-class="{loading: busy}">
			<div id="mycenterstores" ng-hide="busy || data.error" ng-cloak>

				<div id="mcd_store_search_box" class="clearfix" ng-cloak>
					<?php if( $this->mcd_settings['stores_shortcode_atts']['map'] == 'yes' ) : ?>
						<?php if( !empty($this->mcd_settings['map_page']) || !empty($this->mcd_settings['map_config']['map_url']) ) :
							$new_tab = false;
							$map_url = get_permalink($this->mcd_settings['map_page']);
							if( !empty($this->mcd_settings['map_config']['map_url']) ) {
								$new_tab = true;
								$map_url = $this->mcd_settings['map_config']['map_url'];
							}
							?>
							<a href="<?= $map_url ?>" <?= ($new_tab?'target="blank"':'') ?> class="view_map_link">View Map</a>
						<?php endif; ?>
					<?php endif; ?>

					<div class="mcd_search_categories">
						<?php if( $this->mcd_settings['stores_shortcode_atts']['search'] == 'yes' ) : ?>
							<div class="mcd_store_search">
								<input type="search" id="mcd_store_search_field" placeholder="SEARCH" />
								<button><i class="fa fa-search"></i></button>
							</div>
						<?php endif; ?>
						<?php if( $this->mcd_settings['stores_listing_show_categories'] && $this->mcd_settings['stores_shortcode_atts']['cat'] < 0 ) : ?>
							<div class="mcd-categories-dropdown">
								<select id="mcd-categories-dropdown" ng-change="selectCategoryFromDropdown()" ng-model="selectedCategory">
									<option value="all">ALL CATEGORIES</option>
									<option ng-repeat="(category, retailers) in categorized" value="{{ category }}">{{ category }}</option>
								</select>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="mcd-stores">
					<div class="mcd-retailer-listing alphabetical">
						<div class="mcd-retailers mcd-rows<?= $this->mcd_settings['stores_listing_alphabetical_stores_per_row'] ?>">
							<a class="mcd-retailer <?= ($this->mcd_settings['stores_listing_grayscale_effect']?'grayscale':'') ?>"
								ng-repeat="retailer in stores"
								data-index="{{ $index }}"
								data-search="{{ retailer.name }},{{ retailer.slug }},{{ retailer.tags }}"
								href="<?= mcd_single_page_url('mycenterstore') ?>{{ retailer.slug }}"
								title="{{ retailer.name }}"
								ng-cloak>
								<span class="mcd-retailer-image">
									<img ng-src="{{ retailer.media.url }}" />
								</span>
								<?php if( $this->mcd_settings['stores_listing_show_store_name'] ) : ?>
									<span class="mcd-retailer-title end_dots">{{ retailer.name }}</span>
								<?php endif; ?>
								<span class="mcp-retailer-flags">
									<span class="mcp-retailer-flag-deals" ng-if="retailer.deals">Deal</span>
								</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#mcd_store_search_field').on('keyup', function(event) {
		var value = $(this).val().toLowerCase();
		$(".mcd-retailers .mcd-retailer").filter(function() {
			if( $(this).attr('data-search').toLowerCase().indexOf(value) > -1 ) {
				$(this).removeClass('hide');
			} else {
				$(this).addClass('hide');
			}
		});
	});
});
</script>