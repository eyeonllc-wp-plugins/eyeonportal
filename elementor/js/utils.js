function eyeonConvertTo12HourFormat(timeString) {
  if (timeString) {
    const [hours, minutes] = timeString.split(':');
    const ampm = hours >= 12 ? 'pm' : 'am';
    const formattedHours = (hours % 12) || 12; // If it's 0, set it to 12
    const formattedTime = `${formattedHours}:${minutes}${ampm}`;
    return formattedTime;
  }
  return timeString;
}

function eyeonFormatDate(dateString) {
  return moment(dateString).format('MMM D, YYYY');
}

function getResponsiveBreakpoints() {
  var breakpoints = [];
  if (window.elementorFrontend && window.elementorFrontend.config && window.elementorFrontend.config.breakpoints) {
    breakpoints = window.elementorFrontend.config.breakpoints;
  }
  return breakpoints;
}

function getDayByDate(dateString, type = 'short') {
  const dateObj = new Date(dateString);
  const options = { weekday: type, timeZone: 'UTC' };
  const dayOfWeek = dateObj.toLocaleDateString('en-US', options);
  return dayOfWeek;
}