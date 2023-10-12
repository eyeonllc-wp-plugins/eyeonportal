var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
var wp_ajax_url = jQuery('[ng-controller="ShareRailsRetailersCtrl"]').data('url');

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

var articles_ajax_data = {
	action: 'sharerails_articles_fetch',
	endpoint: 'articles',
	params: {},
};

var retailersLoaded = false;
var articlesLoaded = false;

function shuffleArray(array) {
	for (var i = array.length - 1; i > 0; i--) {

		// Generate random number
		var j = Math.floor(Math.random() * (i + 1));

		var temp = array[i];
		array[i] = array[j];
		array[j] = temp;
	}

	return array;
}

app.controller('ShareRailsRetailersCtrl', function($scope, $http, $compile) {
	$scope.busy = true;

	$scope.dbRetailers = {};
	$scope.records = [];

	$scope.loadRetailersResults = function() {
		$scope.getRecords(retailers_ajax_data, function(response) {
			retailersLoaded = true;

			if( response && response.results ) {
				jQuery.each(response.results, function(index, item) {
					var retailer = {
						retail_name: item.name,
						sharerails_logo_url: item.imageUrl!=''?item.imageUrl:MCP_BLANK_IMAGE_URL,
						product_image: $scope.dbRetailers[item.id]?$scope.dbRetailers[item.id].product_image:MCP_BLANK_IMAGE_URL,
						store_url: $scope.dbRetailers[item.id]?MCP_SINGLE_STORE_URL+$scope.dbRetailers[item.id].slug:'#',
						type: 'retailer',
					};
					$scope.records.push(retailer);
				});
	
				if( response.pagination && response.pagination.limit*response.pagination.page < response.pagination.total ) {
					retailers_ajax_data.params.start = retailers_ajax_data.params.limit*response.pagination.page;
					$scope.loadRetailersResults();
				}
			} else if( response && response.Message ) {
				$scope.data.error = response.Message;
			} else {
				$scope.data.error = 'Something went wrong. Please contact support.';
			}

			$scope.finaliseData();
		});
	};

	$scope.loadArticlesResults = function() {
		$scope.getRecords(articles_ajax_data, function(response) {
			articlesLoaded = true;

			if( response ) {
				jQuery.each(response, function(index, item) {
					var image = MCP_BLANK_IMAGE_URL;
					if( item.previewImage && item.previewImage.imageUrl ) {
						image = item.previewImage.imageUrl;
					} else if( item.images && item.images.length>0 ) {
						image = item.images[0].imageUrl;
					}
					var article = {
						article_id: item.id,
						title: item.title,
						article_url: MCP_SINGLE_ARTICLE_URL+McpFriendlyUrl(item.id + '-' + item.title),
						desc: item.leading,
						image: image,
						type: 'article',
					};
					$scope.records.push(article);
				});
			} else if( response && response.Message ) {
				$scope.data.error = response.Message;
			} else {
				$scope.data.error = 'Something went wrong. Please contact support.';
			}

			$scope.finaliseData();
		});
	};

	$scope.finaliseData = function() {
		if( retailersLoaded && articlesLoaded ) {
			$scope.busy = false;
			$scope.records = shuffleArray($scope.records);
			$scope.$apply();
		}
	}

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

	$scope.getDbRetailers = function() {
		var api_url = MCP_SHARERAILS_RETAILERS+'?center='+MCP_CENTER_ID+'&time='+Date.now();
		jQuery.get(api_url, function(response) {
			jQuery.each(response.retailers, function(index, item) {
				$scope.dbRetailers[item.sharerail_retailer_id] = item;
			});
			$scope.loadRetailersResults();
			$scope.loadArticlesResults();
		});
	}

	$scope.getDbRetailers();
});
