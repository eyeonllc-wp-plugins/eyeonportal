<?php
$settings = $this->get_settings_for_display();
$fields = [
  'slider_id',
];
$filtered_settings = array_intersect_key($settings, array_flip($fields));
$unique_id = uniqid();
?>

<div id="eyeon-slider-widget-<?= $unique_id ?>" class="eyeon-slider-widget eyeon-loader">
  <div id="eyeon-slider-<?= $unique_id ?>" class="eyeon-slider"></div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
  const settings = <?= json_encode($filtered_settings) ?>;
  const eyeonSlider = $('#eyeon-slider-widget-<?= $unique_id ?>');

  let currentDevice = getCurrentDevice();

  let defaultSliderSettings;
  let defaultSlides;
  let sliderSettings;
  let slides;

  fetch_slider();

  function fetch_slider() {
    const slider_id = settings.slider_id;
    const ajaxData = {
      slider_id,
    };

    $.ajax({
      url: "<?= MCD_API_SLIDERS ?>/" + slider_id,
      data: ajaxData,
      method: 'GET',
      dataType: 'json',
      headers: {
        center_id: '<?= $mcd_settings['center_id'] ?>'
      },
      success: function(response) {
        defaultSliderSettings = response.settings.settings;
        defaultSlides = response.settings.slides;

        defaultSliderSettings.height = generateScreenResponsiveNumberUnitValues(defaultSliderSettings.height);

        defaultSlides.forEach((slide) => {
          slide.layers.forEach((layer) => {
            layer.position = generateScreenResponsivePositionValues(layer.position);
            if (layer.type === 'text' || layer.type === 'button') {
              layer.font.size = generateScreenResponsiveNumberUnitValues(layer.font.size);
              layer.padding = generateScreenResponsivePaddingValues(layer.padding);
            }
            if (layer.type === 'image') {
              layer.width = generateScreenResponsiveNumberUnitValues(layer.width);
            }
          });
        });
        
        calculateResizeValues();
      }
    });
  }

  function calculateResizeValues() {
    const width = window.innerWidth;
    if (width < DefaultDeviceWidths.default) {
      // Deep clone settings
      const settingsCopy = JSON.parse(JSON.stringify(defaultSliderSettings));
      const sliderHeightRatio = Number(settingsCopy.height[currentDevice].value) / DefaultDeviceWidths[currentDevice];
      settingsCopy.height[currentDevice].value = sliderHeightRatio * width;

      // Deep clone slides
      const slidesCopy = JSON.parse(JSON.stringify(defaultSlides));
      slidesCopy.forEach((slide) => {
        slide.layers.forEach((layer) => {
          // Update position
          layer.position[currentDevice].x = (Number(layer.position[currentDevice]?.x) / DefaultDeviceWidths[currentDevice]) * width;
          layer.position[currentDevice].y = (Number(layer.position[currentDevice]?.y) / DefaultDeviceWidths[currentDevice]) * width;

          // Update text and button specific properties
          if (layer.type === 'text' || layer.type === 'button') {
            layer.font.size[currentDevice].value = (Number(layer.font.size[currentDevice].value) / DefaultDeviceWidths[currentDevice]) * width;

            // Update padding
            layer.padding[currentDevice].top = (Number(layer.padding[currentDevice].top) / DefaultDeviceWidths[currentDevice]) * width;
            layer.padding[currentDevice].right = (Number(layer.padding[currentDevice].right) / DefaultDeviceWidths[currentDevice]) * width;
            layer.padding[currentDevice].bottom = (Number(layer.padding[currentDevice].bottom) / DefaultDeviceWidths[currentDevice]) * width;
            layer.padding[currentDevice].left = (Number(layer.padding[currentDevice].left) / DefaultDeviceWidths[currentDevice]) * width;
          }

          // Update image specific properties
          if (layer.type === 'image') {
            layer.width[currentDevice].value = (Number(layer.width[currentDevice]?.value) / DefaultDeviceWidths[currentDevice]) * width;
          }
        });
      });

      sliderSettings = settingsCopy;
      slides = slidesCopy;
    } else {
      sliderSettings = defaultSliderSettings;
      slides = defaultSlides;
    }


    renderSlider();
  }

  function renderSlider() {
    if (!sliderSettings || !slides) return;

    // Remove loader class
    eyeonSlider.removeClass('eyeon-loader');

    // Create slider HTML structure
    let sliderHtml = `
      <div class="slider-wrapper" style="--dot-color: ${sliderSettings.dotSettings.defaultColor};--dot-active-color: ${sliderSettings.dotSettings.activeColor};--dot-width: ${sliderSettings.dotSettings.width.value}${sliderSettings.dotSettings.width.unit};--dot-height: ${sliderSettings.dotSettings.height.value}${sliderSettings.dotSettings.height.unit};--dot-border-radius: ${sliderSettings.dotSettings.borderRadius.value}${sliderSettings.dotSettings.borderRadius.unit};--dot-spacing: ${Number(sliderSettings.dotSettings.spacing.value) * 0.5}${sliderSettings.dotSettings.spacing.unit};--dot-bottom: ${sliderSettings.dotSettings.bottom.value}${sliderSettings.dotSettings.bottom.unit};--nav-color: ${sliderSettings.navSettings.color};--nav-size: ${sliderSettings.navSettings.size.value*2}${sliderSettings.navSettings.size.unit};--nav-position: ${sliderSettings.navSettings.position.value}${sliderSettings.navSettings.position.unit};">
        <div class="owl-carousel" style="height: ${sliderSettings.height[currentDevice].value}${sliderSettings.height[currentDevice].unit};">
    `;

    // Add slides
    slides.forEach(slide => {
      sliderHtml += `
        <div class="slide-item" style="height: ${sliderSettings.height[currentDevice].value}${sliderSettings.height[currentDevice].unit};
                                background-image: url('${slide.bgImageUrl || ''}'); 
                                background-color: ${slide.bgColor}; 
                                cursor: ${slide.link ? 'pointer' : 'default'}"
              data-link="${slide.link || ''}">
          <div class="slider-container" style="max-width: ${sliderSettings.width.value}${sliderSettings.width.unit}">
            <div class="slide-container" style="max-width: ${DefaultDeviceWidths[currentDevice]}px">
      `;

      // Add layers with device-specific values
      slide.layers.forEach(layer => {
        const position = `position: absolute; top: ${layer.position[currentDevice].y}px; left: ${layer.position[currentDevice].x}px;`;
        
        if (layer.type === 'text') {
          sliderHtml += `
            <div class="slide-layer" style="${position}">
              <div style="font-size: ${layer.font.size[currentDevice].value}${layer.font.size[currentDevice].unit};
                          font-weight: ${layer.font.weight};
                          line-height: ${layer.font.lineHeight?.value}${layer.font.lineHeight?.unit};
                          color: ${layer.font.color};
                          padding: ${layer.padding[currentDevice].top}px ${layer.padding[currentDevice].right}px ${layer.padding[currentDevice].bottom}px ${layer.padding[currentDevice].left}px;
                          background-color: ${layer.bgColor};">
                ${layer.content}
              </div>
            </div>
          `;
        } else if (layer.type === 'button') {
          sliderHtml += `
            <div class="slide-layer" style="${position}">
              <div style="font-size: ${layer.font.size[currentDevice].value}${layer.font.size[currentDevice].unit};
                          font-weight: ${layer.font.weight};
                          line-height: ${layer.font.lineHeight?.value}${layer.font.lineHeight?.unit};
                          color: ${layer.font.color};
                          padding: ${layer.padding[currentDevice].top}px ${layer.padding[currentDevice].right}px ${layer.padding[currentDevice].bottom}px ${layer.padding[currentDevice].left}px;
                          background-color: ${layer.bgColor};
                          border-radius: ${layer.borderRadius.value}${layer.borderRadius.unit};
                          cursor: pointer;"
                    data-link="${layer.link}">
                ${layer.content}
              </div>
            </div>
          `;
        } else if (layer.type === 'image') {
          sliderHtml += `
            <div class="slide-layer" style="${position}">
              <img src="${layer.imageUrl}" 
                   alt="" 
                   style="width: ${layer.width[currentDevice].value}${layer.width[currentDevice].unit};"
                   data-link="${layer.link}" />
            </div>
          `;
        }
      });

      sliderHtml += `
            </div>
          </div>
        </div>
      `;
    });

    sliderHtml += `
        </div>
      </div>
    `;

    // Insert slider HTML
    $('#eyeon-slider-' + '<?= $unique_id ?>').html(sliderHtml);

    // Initialize Owl Carousel
    $('.owl-carousel').owlCarousel({
      items: 1,
      loop: sliderSettings.loop || false,
      autoplay: sliderSettings.autoPlay || false,
      autoplayTimeout: sliderSettings.autoPlayDelay || 5000,
      dots: sliderSettings.showDots || false,
      nav: sliderSettings.showNav || false,
      // navText: [
      //   '<div class="owl-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>',
      //   '<div class="owl-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>'
      // ]
    });

    // Handle click events
    $('.item').on('click', function() {
      const link = $(this).data('link');
      if (link) openLink(link);
    });

    $('.slide-layer [data-link]').on('click', function(e) {
      e.stopPropagation();
      const link = $(this).data('link');
      if (link) openLink(link);
    });
  }

  function openLink(link) {
    if (link) {
      if (link.startsWith('http')) {
        window.location.href = link;
      } else {
        window.location.href = link; // For internal links
      }
    }
  }

  // Update resize event listener
  window.addEventListener('resize', function() {
    currentDevice = getCurrentDevice();
    const width = window.innerWidth;
    calculateResizeValues();
    // currentDevice = getCurrentDevice();
    // if (oldDevice !== currentDevice) {
    //   sliderSettings = result.settings;
    //   slides = result.slides;
    // }
  });
});
</script>

