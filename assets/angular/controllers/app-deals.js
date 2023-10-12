(function () {
  var app = angular.module("MyCenterPortalApp", ["ngSanitize"]);
  var records_endpoint = jQuery('[ng-controller="DealsCtrl"]').data("url");
  var center_id = jQuery('[ng-controller="DealsCtrl"]').data("center-id");

  app.controller(
    "DealsCtrl",
    function ($scope, $http, $compile, RecordsFactory) {
      $scope.busy = true;
      $scope.data;
      $scope.selectedRow;
      $scope.selectedType = "recent";

      $scope.loadResults = function (callback = null) {
        $scope.busy = true;

        var headers = {
          center_id: center_id,
        };
        RecordsFactory.allRecords(records_endpoint, headers).then(
          function (result) {
            $scope.busy = false;
            $scope.deals = [];
            jQuery.each(result.data.items, function (index, deal) {
              deal.start_date = moment(deal.start_date).format('MMM D, YYYY');
              deal.end_date = moment(deal.end_date).format('MMM D, YYYY');
              $scope.deals.push(deal);
            });

            if (callback != null) callback();
          },
          function (response) { }
        );
      };

      $scope.loadResults();

      $scope.selectRow = function (index = null) {
        $scope.selectedRow = index == null ? null : $scope.data.deals[index];
      };

      $scope.selectType = function (type) {
        $scope.selectedType = type;
        // $scope.data = allData[$scope.selectedType];
      };
    }
  );
})();
