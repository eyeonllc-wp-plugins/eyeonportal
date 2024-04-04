<?php
$settings = $this->get_settings_for_display();
$fields = [
  'day_name_type',
  'combine_days',
];
$filtered_settings = array_intersect_key($settings, array_flip($fields));
$unique_id = uniqid();
?>

<div id="eyeon-center-hours-<?= $unique_id ?>" class="eyeon-center-hours eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">
    <div class="center-hours-wrapper">
      <?php if( $settings['center_hours_icon']['value'] ) : ?>
        <div class="icon-col">
          <?php \Elementor\Icons_Manager::render_icon( $settings['center_hours_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        </div>
      <?php endif; ?>
      <div class="content-col">
        <div class="center-hours"></div>
        <?php if( !empty($settings['center_hours_extra_text']) ) : ?>
          <div class="extra-text"><?= $settings['center_hours_extra_text'] ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonCenterHours = $('#eyeon-center-hours-<?= $unique_id ?>');
    const centerHours = eyeonCenterHours.find('.center-hours');

    const { addDays, format, isWithinInterval, parseISO, startOfWeek } = dateFns;

    function getTimezoneDate(date = null) {
      const wpTimezone = `<?= wp_timezone_string() ?>`;
      const today = date ? date : new Date();
      return new Date(today.toLocaleString('en-US', { timeZone: wpTimezone }));
    }

    const todayDate = getTimezoneDate();

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
      centerHours.empty();

      const weeklyOpeningHours = getOpeningHoursForNext7Days(data);
      const formattedOpeningHours = formatOpeningHours(weeklyOpeningHours);
      if( settings.combine_days === 'yes' ) {
        formattedOpeningHours = combineCenterHoursDays(formattedOpeningHours);
      }

      <?php if( $settings['view_mode'] === 'week' ) : ?>
        formattedOpeningHours.forEach(openingHour => {
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
      <?php endif; ?>

      <?php if( $settings['view_mode'] === 'today' ) : ?>
        const todayData = getTodayOpeningHours(weeklyOpeningHours);
        centerHours.html(`
          <div class="value">${todayData.value}</div>
          ${todayData.title ? `<div class="reason">${todayData.title}</div>` : '' }
        `);
      <?php endif; ?>
    }

    const getOpeningHoursForNext7Days = (openingHours) => {
      const next7Days = Array.from({ length: 7 }, (_, i) =>
        addDays(startOfWeek(todayDate, { weekStartsOn: 1 }), i)
      );
      const primarySet = openingHours.sets.find((set) => set.is_primary);
      const childSets = openingHours.sets.filter((set) => !set.is_primary);

      return next7Days.map((day) => {
        const dayOfWeek = format(day, "eeee");
        const shortDayOfWeek = format(day, "eee").toLowerCase();
        const holiday = openingHours.holidays.find((h) =>
          isWithinInterval(day, {
            start: parseISO(h.start_date),
            end: parseISO(h.end_date),
          })
        );
        const irregularOpening = openingHours.irregular_openings.find(
          (io) => parseISO(io.start_date).getDate() === day.getDate()
        );
        const childSet = childSets.find((set) =>
          isWithinInterval(day, {
            start: parseISO(set.start_date),
            end: parseISO(set.end_date),
          })
        );

        let returnData = {
          day: dayOfWeek,
          shortDay: format(day, "eee"),
          date: eyeonFormatDate(day),
        };
        if (holiday) {
          returnData.type = 'holiday';
          returnData.closed = true;
          returnData.title = holiday.title;
        } else if (irregularOpening) {
          returnData.type = 'irregular';
          returnData.hours = {
            start: irregularOpening.start_time,
            end: irregularOpening.end_time
          };
          returnData.title = irregularOpening.title;
        } else if (childSet) {
          type: 'child',
          returnData.hours = {
            start: childSet.days[shortDayOfWeek].start_time,
            end: childSet.days[shortDayOfWeek].end_time
          }
        } else if (primarySet) {
          type: 'primary',
          returnData.hours = {
            start: primarySet.days[shortDayOfWeek].start_time,
            end: primarySet.days[shortDayOfWeek].end_time
          }
        } else {
          returnData.closed = true;
        }
        return returnData;
      });
    };

    const formatOpeningHours = (openingHours) => {
      return openingHours.map(item => {
        const newItem = {
          day: settings.day_name_type==='short' ? item.shortDay : item.day,
          value: item.hours ? `${eyeonFormatTime(item.hours.start)} - ${eyeonFormatTime(item.hours.end)}` : 'Closed',
        };
        if(item.title) {
          newItem.title = item.title;
        }
        return newItem;
      });
    }

    function combineCenterHoursDays(data) {
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

    function getTodayOpeningHours(data) {
      let dayIndex = getIndexByDay(format(todayDate, "eee").toLowerCase());
      const todayData = data[dayIndex];
      console.log('todayData', todayData);
      const returnData = {
        value: todayData.hours ? `${eyeonFormatTime(todayData.hours.start)} - ${eyeonFormatTime(todayData.hours.end)}` : 'Closed',
      };
      if(todayData.title) {
        returnData.title = todayData.title;
      }
      return returnData;
    }

    function getIndexByDay(day) {
      const weekDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
      const lowercaseDay = day.toLowerCase();
      const index = weekDays.indexOf(lowercaseDay);
      return index;
    }
  });
</script>