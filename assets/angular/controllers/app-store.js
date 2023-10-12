var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
var wp_ajax_url = jQuery('[ng-controller="StoreCtrl"]').data('url');
var sharerails_retailer_id = jQuery('[ng-controller="StoreCtrl"]').data('sharerails-retailer-id');

var retailers_ajax_data = {
	action: 'sharerails_retailers_fetch',
	endpoint: 'retailers',
	params: {
		name: '',
		start: 0,
		limit: 100,
		sort: 'NameAsc',
	},
};

app.controller('StoreCtrl', function($scope, $http, $compile) {
	$scope.busy = true;
	$scope.reset = false;
	$scope.data;
	$scope.categories = [];
	$scope.dbRetailers = {};
	$scope.retailers = [];
	$scope.products = [];
	$scope.show_load_more = true;

	$scope.loadResults = function(ajax_data, callback = function(){}) {
		$scope.busy = true;
		if( $scope.reset ) {
			$scope.products = [];
		}

		$scope.getRecords(ajax_data, function(result) {
			$scope.busy = false;
			$scope.reset = false;
			$scope.data = result;

			if( result ) {
				if( result.items ) {
					jQuery.each(result.items, function(index, item) {
						item.custom_slug = McpFriendlyUrl(item.id + '-' + item.title);
						$scope.products.push(item);
					});
		
					if( result.pagination.limit*result.pagination.page >= result.pagination.total ) {
						$scope.show_load_more = false;
					}
				} else if( result.Message ) {
					$scope.show_load_more = false;
				}
			}

			$scope.$apply();
			callback();
		});
	};

	// $scope.loadResults();

	$scope.getRecords = function(ajax_data, callback) {
		jQuery.ajax({
			url: wp_ajax_url,
			method: 'POST',
			dataType: 'json',
			data: ajax_data,
			success: function(response) {
				callback(response);
			}
		});
	}

	$scope.getRetailerCategories = function(callback) {
		var ajax_options = {
			action: 'sharerails_retailer_categories',
			endpoint: 'categories/retailer',
			params: {
				retailerId: sharerails_retailer_id,
			},
		};
		$scope.getRecords(ajax_options, function(response) {
			$scope.categories = response;
			$scope.$apply();
		});
	}
	$scope.getRetailerCategories();

	$scope.getRetailers = function(callback) {
		$scope.getRecords(retailers_ajax_data, function(response) {
			if( response && response.results ) {
				jQuery.each(response.results, function(index, item) {
					var retailer = {
						id: item.id,
						name: item.name,
						store_url: $scope.dbRetailers[item.id]?MCP_SINGLE_STORE_URL+$scope.dbRetailers[item.id].slug:'#',
					};
					$scope.retailers.push(retailer);
				});
				$scope.$apply();
				callback();

				if( response.pagination.limit*response.pagination.page < response.pagination.total ) {
					retailers_ajax_data.params.start = retailers_ajax_data.params.limit*response.pagination.page;
					$scope.getRetailers(callback);
				}
			}
		});
	}

	$scope.getDbRetailers = function(callback) {
		var api_url = MCP_SHARERAILS_RETAILERS+'?center='+MCP_CENTER_ID;
		jQuery.get(api_url, function(response) {
			jQuery.each(response.retailers, function(index, item) {
				$scope.dbRetailers[item.sharerail_retailer_id] = item;
			});
			$scope.getRetailers(callback);
		});
	}
});
