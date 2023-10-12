<?php if( !empty($this->mcd_settings['sharerails_api_key']) && $mycenterstore['sharerail_retailer_id'] != null ) : ?>
<div id="mcd-retailer-products" ng-app="MyCenterPortalApp" ng-controller="StoreCtrl" data-url="<?= admin_url('admin-ajax.php') ?>" data-sharerails-retailer-id="<?= $mycenterstore['sharerail_retailer_id'] ?>">
	<div class="products-wrapper" ng-class="{'mcd_loading_div bottom_space': busy, 'mcd_loading_div': reset}" ng-cloak>
		<div class="products-content" ng-show="data!=null">
			<h3>Shop Now!</h3>

			<div class="sharerails-data">
				<div id="sharerails-filters">
					<div id="sharerails_retailer_div" class="sharerails-filter">
						<select>
							<option ng-repeat="retailer in retailers" value="{{retailer.id}}" data-store-url={{retailer.store_url}} ng-selected="retailer.id == '<?= $mycenterstore['sharerail_retailer_id'] ?>'">{{retailer.name}}</option>
						</select>
					</div>

					<div class="sharerails-filter">
						<div id="sharerails-products-search">
							<input type="search" placeholder="Search..." />
							<button><i class="fas fa-search"></i></button>
						</div>
					</div>

					<div class="sharerails-filter collapsible open" ng-show="categories.length>0">
						<div class="filter-title">Categories</div>
						<div class="sharerails-filter-content">
							<div class="sharerails-filter-wrapper">
								<div class="retailerCategories filters">
									<ul>
										<li class="category" ng-repeat="category in categories" ng-class="{active: category.id == data.categoryId}" data-category-id={{category.id}}>
											<a>{{category.name}}</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="sharerails-filter collapsible" ng-show="data.brandFacets.length>0">
						<div class="filter-title">Brands</div>
						<div class="sharerails-filter-content">
							<div class="sharerails-filter-wrapper">
								<div class="brandFacets filters">
									<div class="filter" ng-repeat="brand in data.brandFacets">
										<label class="mcp-checkbox-field">
											<input type="checkbox" name="brands[]" value="{{brand.id}}" ng-checked="brand.selected">
											<i class="fas"></i>
											<span>{{brand.label}}</span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="sharerails-products">
					<?php include(MCD_PLUGIN_PATH.'templates/products/products-grid.php'); ?>

					<div class="align_center" ng-show="show_load_more">
						<div id="load_more_products" class="mcp_btn outline">Load More</div>
					</div>

					<div class="mcd-alert fullwidth" ng-show="products.length==0">{{data.Message ? data.Message : 'No products found.'}}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	var page = 1;
	var retailerId = '<?= $mycenterstore['sharerail_retailer_id'] ?>';
	var keyword = '';
	var selectedCategoryId = '';
	var brandFacets = [];
	var ngStoreCtrl = $('[ng-controller="StoreCtrl"]');

	function fetch_products(callback = function(){}) {
		var params = {
			retailerId: retailerId,
			categoryId: selectedCategoryId,
			start: (page-1)*<?= intval($this->mcd_settings['store_products_fetch_limit']) ?>,
			limit: <?= intval($this->mcd_settings['store_products_fetch_limit']) ?>,
			brandFacetId: brandFacets,
			keyword: keyword
		};
		var ajax_data = {
			action: 'sharerails_products_fetch',
			endpoint: 'products',
			params: params,
		};
		ngStoreCtrl.scope().loadResults(ajax_data, function() {
			reset_load_more();
			callback();
		});
	}
	fetch_products(function() {
		$('.sharerails-filter.collapsible.open').each(function(index, el) {
			var content_elem = $(this).find('.sharerails-filter-content');
			calculate_filter_content_height(content_elem);
		});
	});

	function reset_products(callback = function(){}) {
		ngStoreCtrl.scope().reset = true;
		ngStoreCtrl.scope().$apply();
		page = 1;
		fetch_products(callback);
	}

	$('#load_more_products').click(function(event) {
		if( !$(this).hasClass('disabled') ) {
			loading_more();
			page++;
			fetch_products();
		}
	});

	function loading_more() {
		$('#load_more_products').addClass('disabled');
		$('#load_more_products').html('Loading...');
	}
	function reset_load_more() {
		$('#load_more_products').removeClass('disabled');
		$('#load_more_products').html('Load More');
	}

	$('.sharerails-filter.collapsible .filter-title').click(function() {
		var filter_elem = $(this).parents('.sharerails-filter');
		var content_elem = filter_elem.find('.sharerails-filter-content');

		filter_elem.toggleClass('open');

		calculate_filter_content_height(content_elem);
	});

	function calculate_filter_content_height(elem) {
		var elem_height = elem.find('.sharerails-filter-wrapper').outerHeight();
		elem.css('height', elem_height+'px');
	}

	function reset_open_filters_height() {
		$('.sharerails-filter.collapsible.open .sharerails-filter-content').each(function(index, el) {
			calculate_filter_content_height($(el));
		});
	}

	$(document).on('change', '.brandFacets .filter input', function(event) {
		brandFacets = [];
		$('.brandFacets .filter input:checked').each(function() {
			brandFacets.push($(this).val());
		});
		reset_products();
	});

	$(document).on('click', '.retailerCategories .category a', function(event) {
		var categoryElem = $(this).parents('.category');
		selectedCategoryId = categoryElem.attr('data-category-id');
		$('.retailerCategories .category.active').removeClass('active');
		categoryElem.addClass('active');
		reset_products();
	});

	$('#sharerails-products-search input').on('keyup', function(e) {
		keyword = e.target.value;
		if( e.keyCode == 13 ) {
			reset_products();
		}
	});

	var sharerails_retailers_select2 = null;
	function create_sharerails_retailers_select2() {
		if( sharerails_retailers_select2 != null ) {
			$('#sharerails_retailer_div select').select2('destroy');
		}
		sharerails_retailers_select2 = $('#sharerails_retailer_div select').select2({
			placeholder: 'Choose Retailer ...',
			dropdownAutoWidth: true,
			dropdownParent: $('#sharerails_retailer_div'),
			width: '100%',
		});
	}

	ngStoreCtrl.scope().getDbRetailers(function() {
		create_sharerails_retailers_select2();
	});

	$('#sharerails_retailer_div select').on('change', function(event) {
		var retailer_id = $(this).val();
		var store_url = $(this).find('option[value="'+retailer_id+'"]').data('store-url');
		if( store_url != '#' ) {
			$('body').addClass('mcd_loading_div');
			window.location.href = store_url;
		}
	});

});
</script>
<?php endif; ?>

