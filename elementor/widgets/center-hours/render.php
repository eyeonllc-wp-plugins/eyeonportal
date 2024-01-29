<?php
$settings = $this->get_settings_for_display();
$fields = [
  'day_names',
  'combine_days',
];
$filtered_settings = array_intersect_key($settings, array_flip($fields));
$unique_id = uniqid();
?>

<div id="eyeon-center-hours-<?= $unique_id ?>" class="eyeon-center-hours eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">
    <div class="center-hours"></div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonCenterHours = $('#eyeon-center-hours-<?= $unique_id ?>');
    const centerHours = eyeonCenterHours.find('.center-hours');

    fetch_center_hours();

    function fetch_center_hours() {
      $.ajax({
        url: "<?= MCD_API_CENTER_HOURS ?>",
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.sets) {
            renderHours(response);
          }
        }
      });
    }

    function renderHours(data) {
      eyeonCenterHours.removeClass('eyeon-loader').find('.eyeon-wrapper').removeAttr('style');
      let weeklyOpeningHours = getFormattedOpeningHours(data);
      if( settings.combine_days === 'yes' ) {
        weeklyOpeningHours = combineCenterHoursDaysArray(weeklyOpeningHours);
      }
      console.log('weeklyOpeningHours', weeklyOpeningHours);

      centerHours.empty();

      weeklyOpeningHours.forEach(openingHour => {
        const openingHourItem = $(`
          <div class="center-hour">
            <div class="day">${openingHour.day}</div>
            <div class="values">
              <div class="value">${openingHour.value}</div>
              ${openingHour.title ? `<div class="reason">${openingHour.title}</div>` : '' }
            </div>
          </div>
        `);
        centerHours.append(openingHourItem);
      });
    }

    function calculateWeeklyOpeningHours(data) {
      const weeklyOpeningHours = [];
      
      const currentDate = new Date();
      currentDate.setHours(0, 0, 0, 0);
      const currentWeekStart = new Date(currentDate);
      currentWeekStart.setDate(currentDate.getDate() - (currentDate.getDay() + 6) % 7);
      const currentWeekEnd = new Date(currentWeekStart);
      currentWeekEnd.setDate(currentWeekStart.getDate() + 6);

      for (let i = 0; i < 7; i++) {
        const dayDate = new Date(currentWeekStart);
        dayDate.setDate(currentWeekStart.getDate() + i);

        const formattedDate = `${dayDate.getFullYear()}-${(dayDate.getMonth() + 1).toString().padStart(2, '0')}-${dayDate.getDate().toString().padStart(2, '0')}`;
        const day = {
          date: formattedDate,
          day: getFullDatByDate(formattedDate, settings.day_names)
        };
        weeklyOpeningHours.push(day);
      }

      data.sets.forEach((set) => {
        const openingHours = set.days;
        if( set.is_primary ) {
          Object.keys(openingHours).forEach((dayName) => {
            const dayIndex = getIndexByDay(dayName);
            const day = weeklyOpeningHours[dayIndex];
            if (day) {
              day.start_time = openingHours[dayName].start_time;
              day.end_time = openingHours[dayName].end_time;
            }
          });
        } else {
          const startDate = new Date(set.start_date);
          const endDate = new Date(set.end_date);

          while (startDate <= endDate) {
            const dayName = startDate.toLocaleDateString('en-US', { weekday: 'short' }).toLowerCase();
            const dayIndex = getIndexByDay(dayName);
            const day = weeklyOpeningHours[dayIndex];

            if (day) {
              day.start_time = openingHours[dayName].start_time;
              day.end_time = openingHours[dayName].end_time;
            }
            
            startDate.setDate(startDate.getDate() + 1); // Move to the next day
          }
        }
      });

      data.holidays.forEach((holiday) => {
        const holidayDate = new Date(holiday.start_date);
        if (holidayDate < currentWeekStart || holidayDate > currentWeekEnd) return;
    
        const dayIndex = Math.abs((holidayDate.getDay() + 6) % 7 - (currentDate.getDay() + 6) % 7);
        const day = weeklyOpeningHours[dayIndex];
        if (day) {
          day.holiday = true;
          day.title = holiday.title;
        }
      });

      data.irregular_openings.forEach((irregularOpening) => {
        const openingDate = new Date(irregularOpening.start_date);
        if (openingDate < currentWeekStart || openingDate > currentWeekEnd) return;

        const dayIndex = Math.abs((openingDate.getDay() + 6) % 7 - (currentDate.getDay() + 6) % 7);
        const day = weeklyOpeningHours[dayIndex];
        if (day) {
          day.start_time = irregularOpening.start_time;
          day.end_time = irregularOpening.end_time;
          day.irregular = true;
          day.title = irregularOpening.title;
        }
      });

      return weeklyOpeningHours;
    }

    function getFormattedOpeningHours(data) {
      let opening_hours = calculateWeeklyOpeningHours(data);

      let formatted_hours = opening_hours.map((item) => {
        let newItem = { day: item.day};
        if(item.holiday) {
          newItem.value = 'Closed';
          newItem.title = item.title;
        } else {
          newItem.value = eyeonConvertTo12HourFormat(item.start_time)+' - '+eyeonConvertTo12HourFormat(item.start_time);
          if( item.irregular ) {
            newItem.title = item.title;
          }
        }
        return newItem;
      });
      return formatted_hours;
    }

    function combineCenterHoursDaysArray(data) {
      let combinedArray = [];

      data.forEach((item) => {
        const existingItem = combinedArray.find(
          (combinedItem) => combinedItem.value === item.value
        );

        if (existingItem) {
          if (existingItem.days && !existingItem.days.includes(item.day)) {
            existingItem.days.push(item.day);
          }
        } else {
          combinedArray.push({ ...item, days: [item.day] });
        }
      });

      combinedArray = combinedArray.map((item) => {
        const daysRange = item.days;
        delete item.days;
        if(daysRange.length > 1 ) {
          return {...item, day: daysRange[0]+' - '+daysRange[daysRange.length-1]};  
        }
        return item;
      });

      return combinedArray;
    }


    function getIndexByDay(day) {
      const weekDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
      const lowercaseDay = day.toLowerCase();
      const index = weekDays.indexOf(lowercaseDay);
      return index;
    }
  });
</script>