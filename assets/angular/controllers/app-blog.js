(function () {
  var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
  var records_endpoint = jQuery('[ng-controller="BlogPostsCtrl"]').data('url');
  var center_id = jQuery('[ng-controller="BlogPostsCtrl"]').data("center-id");

  app.controller('BlogPostsCtrl', function ($scope, $http, $compile, RecordsFactory) {
    $scope.busy = true;
    $scope.data;
    $scope.blogposts = [];

    $scope.loadResults = function (callback = null) {
      $scope.busy = true;

      var headers = {
        center_id: center_id,
      };
      RecordsFactory.allRecords(records_endpoint, headers).then(
        function (result) {
          $scope.busy = false;
          $scope.data = result.data;
          jQuery.each(result.data.items, function (index, post) {
            post.post_date_day = moment(post.post_date).format('D');
            post.post_date_month = moment(post.post_date).format('MMM');
            $scope.blogposts.push(post);
          });

          if (callback != null) callback();
        },
        function (response) { });
    };

    // $scope.loadResults();
  });

  function seoFriendlyURL(text) {
    var text = text.toString().toLowerCase();
    text = text.split(/\'/).join("");
    text = text.split(/’/).join("");
    text = text.split(/[^a-z0-9\-]/).join("-");
    text = text.split(/-+/).join("-");
    text = text.replace(/-$/, "");
    text = text.replace(/^-/, "");
    return text;
  }

  app.filter('categoryName', ['$sce', function ($sce) {
    return function (text) {
      return seoFriendlyURL(text);
    };
  }]);

  app.filter('categoryClassesFromArr', ['$sce', function ($sce) {
    return function (categories) {
      return categories.map(function (val, index) {
        return seoFriendlyURL(val);
      })
    }
  }]);

})();

