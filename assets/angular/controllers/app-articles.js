var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
var wp_ajax_url = jQuery('[ng-controller="ShareRailsArticlesCtrl"]').data('url');

var ajax_data = {
	action: 'sharerails_articles_fetch',
	endpoint: 'articles',
	params: {},
};

app.controller('ShareRailsArticlesCtrl', function($scope, $http, $compile) {
	$scope.busy = true;
	$scope.data;
	$scope.articles = [];

	$scope.loadResults = function(callback = function(){}) {
		$scope.busy = true;

		$scope.getRecords(function(response) {
			$scope.busy = false;
			$scope.data = response;

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
						slug: McpFriendlyUrl(item.id + '-' + item.title),
						desc: item.leading,
						image: image,
					};
					$scope.articles.push(article);
				});
			} else if( response && response.Message ) {
				$scope.data.error = response.Message;
			} else {
				$scope.data.error = 'Something went wrong. Please contact support.';
			}

			$scope.$apply();
			callback();
		});
	};

	$scope.getRecords = function(callback) {
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

	$scope.loadResults();
});
