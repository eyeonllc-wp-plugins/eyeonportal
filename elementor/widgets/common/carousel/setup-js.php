<?php if ($settings['view_mode'] === 'carousel') : ?>
const breakpoints = window.getResponsiveBreakpoints();
console.log('breakpoints', breakpoints);

var owl_options = {
  nav: settings.carousel_navigation === 'show',
  navText: ['', ''],
  dots: settings.carousel_dots === 'show',
  dotsEach: false,
  loop: settings.carousel_loop === 'yes',
  items: settings.carousel_items,
  autoplay: settings.carousel_autoplay === 'yes',
  autoplayTimeout: settings.carousel_autoplay_speed || 5000,
  autoplayHoverPause: true,
  slideBy: settings.carousel_slideby,
  touchDrag: true,
  mouseDrag: true,
  center: false,
  margin: settings.carousel_margin,
  responsive: {
    0: {
      items: settings.carousel_items_mobile,
      slideBy: settings.carousel_slideby_mobile,
      margin: settings.carousel_margin_mobile,
    },
    [breakpoints.md]: {
      items: settings.carousel_items_tablet,
      slideBy: settings.carousel_slideby_tablet,
      margin: settings.carousel_margin_tablet,
    },
    [breakpoints.lg]: {
      items: settings.carousel_items,
      slideBy: settings.carousel_slideby,
      margin: settings.carousel_margin,
    },
  }
};
jQuery('.owl-carousel-<?= $unique_id ?>').owlCarousel(owl_options);
<?php endif; ?>
