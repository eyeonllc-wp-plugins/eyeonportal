angular.module("MyCenterPortalApp").filter("trusted", [
  "$sce",
  function ($sce) {
    return function (text) {
      return $sce.trustAsHtml(text);
    };
  },
]);

angular.module("MyCenterPortalApp").factory("RecordsFactory", [
  "$http",
  "$q",
  function ($http, $q) {
    return {
      allRecords: allRecords,
    };

    function allRecords(records_endpoint, headers) {
      var deferred = $q.defer();
      var req = {
        method: "GET",
        url: records_endpoint + "&time=" + Date.now(),
        headers: headers,
      };
      $http(req).then(
        function (result) {
          deferred.resolve(result);
        },
        function (response) {
          deferred.reject(response);
        }
      );
      return deferred.promise;
    }
  },
]);
