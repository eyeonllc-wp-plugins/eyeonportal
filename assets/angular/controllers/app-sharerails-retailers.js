var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
var ajax_url = jQuery('[ng-controller="ShareRailsRetailersCtrl"]').data('url');

var ajax_data = {
	action: 'sharerails_shop_retailers_fetch',
	endpoint: 'retailers',
	params: {
		name: '',
		start: 0,
		limit: 100,
		sort: 'NameAsc',
	},
};

app.controller('ShareRailsRetailersCtrl', function($scope, $http, $compile) {
	$scope.busy = true;
	$scope.data;
	$scope.retailers = [];

	$scope.loadResults = function(callback = function(){}) {
		$scope.busy = true;

		$scope.getRecords(function(result) {
			$scope.busy = false;
			$scope.data = result;

			if( result && result.retailers ) {
				jQuery.each(result.retailers, function(index, item) {
					$scope.retailers.push(item);
				});
	
				if( result.pagination && result.pagination.limit*result.pagination.page < result.pagination.total ) {
					ajax_data.params.start = ajax_data.params.limit*result.pagination.page;
					$scope.loadResults();
				}
			}

			$scope.$apply();
			callback();
		});
	};

	$scope.getRecords = function(callback) {
		jQuery.ajax({
			url: ajax_url,
			method: 'POST',
			dataType: 'json',
			data: ajax_data,
			success: function(response) {
				callback(response);
			}
		});
	}

	$scope.loadResults();
});
