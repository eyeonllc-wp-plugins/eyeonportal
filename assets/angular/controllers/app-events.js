(function () {
  var app = angular.module('MyCenterPortalApp', ['ngSanitize']);
  var records_endpoint = jQuery('[ng-controller="EventsCtrl"]').data('url');
  var center_id = jQuery('[ng-controller="EventsCtrl"]').data("center-id");
  var mcd_event_url = jQuery('[ng-controller="EventsCtrl"]').data('event-url');

  app.controller('EventsCtrl', function ($scope, $http, $compile, RecordsFactory) {
    $scope.busy = true;

    $scope.selectedType = 'calendar';
    var showFirstGrid = false;

    var calendarEl = document.getElementById('calendar');
    var calendar = null;
    var events = [];

    var nd = new Date();
    var now_date = new Date(nd.getFullYear(), nd.getMonth(), nd.getDate(), 0, 0, 0);
    var timestamp_arr_count = 1;

    $scope.loadResults = function () {
      $scope.busy = true;

      events = [];
      var headers = {
        center_id: center_id,
      };
      RecordsFactory.allRecords(records_endpoint, headers).then(
        function (result) {
          $scope.busy = false;
          $scope.data = result.data;

          jQuery.each(result.data.items, function (index, event) {
            $scope.data.items[index].event_url = (event.event_url == '' ? mcd_event_url + event.slug : event.event_url);
          });

          jQuery.each(result.data.items, function (index, event) {
            event.start_date = moment(event.start_date).format('MMM D, YYYY');
            event.end_date = moment(event.end_date).format('MMM D, YYYY');

            var start_date_str = event.start_date
            var start_time_str = (event.is_all_day_event ? '12:01 am' : event.start_time);
            var start_datetime_str = start_date_str + ' ' + start_time_str;
            var sd = new Date(start_datetime_str);
            var start_date = new Date(sd.getFullYear(), sd.getMonth(), sd.getDate(), sd.getHours(), sd.getMinutes(), sd.getSeconds());

            var end_date_str = event.end_date;
            var end_time_str = (event.is_all_day_event ? '11:59 pm' : event.end_time);
            var end_datetime_str = end_date_str + ' ' + end_time_str;
            var ed = new Date(end_datetime_str);
            var end_date = new Date(ed.getFullYear(), ed.getMonth(), ed.getDate() + (event.is_all_day_event ? 1 : 0), ed.getHours(), ed.getMinutes(), ed.getSeconds());

            var duration_start_date = new Date(start_date_str + ' ' + start_time_str);
            var duration_end_date = new Date(start_date_str + ' ' + end_time_str);
            var event_duration = duration_end_date.getTime() - duration_start_date.getTime()

            var new_event_data = event;
            new_event_data.start = start_date;
            new_event_data.end = end_date;

            var calendar_event = {
              title: event.title,
              description: event.short_description,
              start: start_date,
              end: end_date,
              allDay: event.is_all_day_event,
              color: event.category.color,
              event_data: new_event_data,
              url: mcd_event_url + event.slug,
            }
            if (!event.is_all_day_event) {
              calendar_event.duration = event_duration;
            }
            if (event.is_repeat_event) {
              calendar_event.rrule = event.repeat_rrule;
            }
            console.log('calendar_event', calendar_event);
            events.push(calendar_event);
          });

          if (calendar != null) calendar.destroy();
          if (calendarEl != null) {
            calendar = new FullCalendar.Calendar(calendarEl, {
              // timeZone: 'Honolulu',
              plugins: ['dayGrid', 'rrule'],
              header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
              },
              fixedWeekCount: false,
              displayEventTime: false,
              events: events,
              eventRender: function (info) {
                var event_data = info.event.extendedProps.event_data;

                if (event_data.repeat_on != '') {
                  var start_date = Math.round(info.event.start.getTime() / 1000);
                  var end_date = Math.round(event_data.end.getTime() / 1000);
                  var new_href = (event_data.event_url == '' ? mcd_event_url + event_data.slug + '?rdate=' + start_date : event_data.event_url);
                  if (!event_data.is_all_day_event) {
                    // new_href += ',' + end_date;
                  }
                  info.el.setAttribute('href', new_href);
                }

                return info.el;
              },
            });
            calendar.render();
          }

          if (!showFirstGrid) {
            showFirstGrid = true;
            $scope.selectedType = 'grid';
          }
        },
        function (response) { }
      );
    };
    $scope.loadResults();

    $scope.selectType = function (type) {
      jQuery('.mcd-magic-hide').removeClass('mcd-magic-hide');
      $scope.selectedType = type;
    };

  });
})();
