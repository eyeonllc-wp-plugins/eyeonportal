(function () {
  var app = angular.module("MyCenterPortalApp", ["ngSanitize"]);
  var records_endpoint = jQuery('[ng-controller="StoresCtrl"]').data("url");
  var center_id = jQuery('[ng-controller="StoresCtrl"]').data("center-id");

  app.controller(
    "StoresCtrl",
    function ($scope, $http, $compile, RecordsFactory) {
      $scope.busy = true;
      $scope.data;
      $scope.selectedRow;
      $scope.selectedCategory = 'all';
      $scope.stores = [];

      $scope.loadResults = function () {
        $scope.busy = true;

        var headers = {
          center_id: center_id,
        };
        RecordsFactory.allRecords(records_endpoint, headers).then(
          function (result) {
            $scope.busy = false;
            $scope.data = result.data;
            $scope.stores = result.data.items;
            $scope.categorized = {};

            jQuery.each($scope.stores, function (index, retailer) {
              jQuery.each(retailer.categories, function (index, category) {
                if (!$scope.categorized[category.name]) {
                  $scope.categorized[category.name] = [];
                }
                $scope.categorized[category.name].push(retailer);
              });
            });
          },
          function (response) { }
        );
      };

      $scope.loadResults();

      $scope.selectCategory = function (category) {
        $scope.selectedCategory = category;
        if ($scope.selectedCategory === 'all') {
          $scope.stores = $scope.data.items;
        } else {
          $scope.stores = $scope.categorized[$scope.selectedCategory];
        }
      };

      $scope.selectCategoryFromDropdown = function () {
        $scope.selectCategory($scope.selectedCategory);
      };
    }
  );
})();
