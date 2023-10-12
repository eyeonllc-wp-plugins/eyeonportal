(function () {
  var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
  var records_endpoint = jQuery('[ng-controller="CareersCtrl"]').data('url');
  var center_id = jQuery('[ng-controller="CareersCtrl"]').data("center-id");

  app.controller('CareersCtrl', function ($scope, $http, $compile, RecordsFactory) {
    $scope.busy = true;
    $scope.careers;
    $scope.selectedRow;
    $scope.selectedType = 'recent';

    var allData;

    $scope.loadResults = function (callback = null) {
      $scope.busy = true;

      var headers = {
        center_id: center_id,
      };
      RecordsFactory.allRecords(records_endpoint, headers).then(
        function (result) {
          $scope.busy = false;
          allData = result.data;
          $scope.careers = allData.items;

          if (callback != null) callback();
        },
        function (response) { });
    };

    $scope.loadResults();

    $scope.selectRow = function (index = null) {
      $scope.selectedRow = (index == null ? null : $scope.data.careers[index]);
    };

    $scope.selectType = function (type) {
      $scope.selectedType = type;
      $scope.data = allData[$scope.selectedType];
    };
  });
})();

