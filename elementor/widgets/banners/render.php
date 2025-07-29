<?php
$settings = $this->get_settings_for_display();
$fields = [
  'banner_id',
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
    const banner_id = settings.banner_id;
    const ajaxData = {
      banner_id,
    };

    $.ajax({
      url: "<?= MCD_API_BANNERS ?>/" + banner_id,
      data: ajaxData,
      method: 'GET',
      dataType: 'json',
      headers: {
        center_id: '<?= $mcd_settings['center_id'] ?>'
      },
      success: function(response) {
        defaultSliderSettings = response.settings.settings;
        defaultSlides = response.settings.slides.filter(slide => slide.active);

        defaultSliderSettings.height = generateScreenResponsiveNumberUnitValues(defaultSliderSettings.height);

        defaultSlides.forEach((slide) => {
          slide.layers.forEach((layer) => {
            console.log('layer', JSON.parse(JSON.stringify(layer)));

            layer.isVisible = generateScreenResponsiveValues(layer.isVisible);

            layer.positionGroup.offset = generateScreenResponsiveOffsetValues(layer.positionGroup.offset);
            layer.positionGroup.position = generateScreenResponsivePositionValues(layer.positionGroup.position);
            
            if (layer.type === 'text' || layer.type === 'button') {
              if(layer.width) layer.width = generateScreenResponsiveNumberUnitValues(layer.width);
              layer.fontGroup.font.alignment = generateScreenResponsiveValues(layer.fontGroup.font.alignment);
              layer.fontGroup.font.size = generateScreenResponsiveNumberUnitValues(layer.fontGroup.font.size);
              layer.styleGroup.padding = generateScreenResponsivePaddingValues(layer.styleGroup.padding);
            }
            if (layer.type === 'image' ||layer.type === 'box') {
              layer.sizeGroup.width = generateScreenResponsiveNumberUnitValues(layer.sizeGroup.width);
              layer.sizeGroup.height = generateScreenResponsiveNumberUnitValues(layer.sizeGroup.height);
            }
            if (layer.type === 'image') {
              layer.sizeGroup.objectFit = generateScreenResponsiveValues(layer.sizeGroup.objectFit);
            }
          });
        });
        
        calculateResizeValues();
      },
      error: function(xhr, status, error) {
        console.log('error', error);
        eyeonSlider.removeClass('eyeon-loader').html('Banner not found.');
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
          layer.positionGroup.offset[currentDevice].x = (Number(layer.positionGroup.offset[currentDevice]?.x) / DefaultDeviceWidths[currentDevice]) * width;
          layer.positionGroup.offset[currentDevice].y = (Number(layer.positionGroup.offset[currentDevice]?.y) / DefaultDeviceWidths[currentDevice]) * width;

          if (layer.type === 'text' || layer.type === 'button') {
            layer.font.size[currentDevice].value = (Number(layer.font.size[currentDevice].value) / DefaultDeviceWidths[currentDevice]) * width;

            // Update padding
            layer.padding[currentDevice].top = (Number(layer.padding[currentDevice].top) / DefaultDeviceWidths[currentDevice]) * width;
            layer.padding[currentDevice].right = (Number(layer.padding[currentDevice].right) / DefaultDeviceWidths[currentDevice]) * width;
            layer.padding[currentDevice].bottom = (Number(layer.padding[currentDevice].bottom) / DefaultDeviceWidths[currentDevice]) * width;
            layer.padding[currentDevice].left = (Number(layer.padding[currentDevice].left) / DefaultDeviceWidths[currentDevice]) * width;
          }

          if (layer.type === 'image' || layer.type === 'box') {
            layer.width[currentDevice].value = (Number(layer.width[currentDevice]?.value) / DefaultDeviceWidths[currentDevice]) * width;
            layer.height[currentDevice].value = (Number(layer.height[currentDevice]?.value) / DefaultDeviceWidths[currentDevice]) * width;
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
        <div
          class="slide-item"
          style="
            height: ${sliderSettings.height[currentDevice].value}${sliderSettings.height[currentDevice].unit};
            background-image: url('${slide.bgImageUrl || ''}'); 
            ${slide.bgColor ? `background-color: ${slide.bgColor};` : ''}
            ${slide.link ? 'cursor: pointer;' : ''}
          "
          ${slide.link ? `data-link="${slide.link}"` : ''}
        ">
          <div class="slider-container" style="max-width: ${sliderSettings.width.value}${sliderSettings.width.unit}">
            <div class="slide-container" style="max-width: ${DefaultDeviceWidths[currentDevice]}px">
      `;

      // Add layers with device-specific values
      slide.layers.forEach(layer => {
        let layerStyles = `
          transform: ${layer.positionGroup.position[currentDevice].horizontal === 'center' ? 'translateX(-50%)' : ''} ${layer.positionGroup.position[currentDevice].vertical === 'middle' ? 'translateY(-50%)' : ''} translate(${layer.positionGroup.offset[currentDevice].x}px, ${layer.positionGroup.offset[currentDevice].y}px);
          display: ${layer.isVisible[currentDevice] ? 'block' : 'none'};
          ${layer.zIndex ? `z-index: ${layer.zIndex};` : ''}
        `;

        if (layer.type === 'text' || layer.type === 'button') {
          layerStyles += `
            font-size: ${layer.fontGroup.font.size[currentDevice].value}${layer.fontGroup.font.size[currentDevice].unit};
            font-weight: ${layer.fontGroup.font.weight};
            line-height: ${layer.fontGroup.font.lineHeight?.value}${layer.fontGroup.font.lineHeight?.unit};
            letter-spacing: ${layer.fontGroup.font.letterSpacing?.value}${layer.fontGroup.font.letterSpacing?.unit};
            color: ${layer.styleGroup.color};
            padding-top: ${layer.styleGroup.padding[currentDevice].top}px;
            padding-right: ${layer.styleGroup.padding[currentDevice].right}px;
            padding-bottom: ${layer.styleGroup.padding[currentDevice].bottom}px;
            padding-left: ${layer.styleGroup.padding[currentDevice].left}px;
            ${layer.styleGroup.bgColor ? `background-color: ${layer.styleGroup.bgColor};` : ''}
          `;
        }
        if (layer.type === 'button') {
          layerStyles += `
            border-radius: ${layer.styleGroup.borderRadius.value}${layer.styleGroup.borderRadius.unit};
          `;
        }
        if (layer.type === 'image' || layer.type === 'box') {
          layerStyles += `
            width: ${layer.sizeGroup.width[currentDevice].value}${layer.sizeGroup.width[currentDevice].unit};
            height: ${layer.sizeGroup.height[currentDevice].value}${layer.sizeGroup.height[currentDevice].unit};
          `;
        }
        if (layer.type === 'box') {
          layerStyles += `
            ${layer.styleGroup.bgColor ? `background-color: ${layer.styleGroup.bgColor};` : ''}
          `;
        }

        sliderHtml += `<div
          class="slide-layer ${layer.type}-layer alignX-${layer.positionGroup.position[currentDevice].horizontal} alignY-${layer.positionGroup.position[currentDevice].vertical}"
          style="${layerStyles}">`;

        if (layer.type === 'text') sliderHtml += `${layer.content}`;
        
        if (layer.type === 'button') sliderHtml += `<div ${layer.link ? `data-link="${layer.link}"` : ''}>${layer.content}</div>`;

        if (layer.type === 'image') {
          sliderHtml += `
              <img
                src="${layer.imageUrl}"
                ${layer.link ? `data-link="${layer.link}"` : ''}
                style="object-fit: ${layer.sizeGroup.objectFit[currentDevice]};"
              />
          `;
        }

        sliderHtml += `</div>`;
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
      dots: (sliderSettings.showDots && defaultSlides.length > 1) || false,
      nav: (sliderSettings.showNav && defaultSlides.length > 1) || false,
      // navText: [
      //   '<div class="owl-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>',
      //   '<div class="owl-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>'
      // ]
    });

    // Handle click events
    $('.slide-item').on('click', function() {
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
        window.open(link, '_blank');
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

