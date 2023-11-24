jQuery(function ($) {
  elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
    const widgetName = model.attributes.widgetType;

    if (widgetName === 'eyeon_stores_widget' || widgetName === 'eyeon_carousel_widget') {
      const categoriesSelect2 = $('.elementor-control-retailer_categories select');
      const selectedCategories = model.attributes.settings.attributes.retailer_categories.map(function (item) {
        return parseInt(item);
      });

      $.ajax({
        url: categoriesCustomData.api_endpoint,
        data: {
          limit: 100,
          page: 1,
          group: true
        },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: categoriesCustomData.center_id
        },
        success: function (response) {
          if (response.items) {
            $.each(response.items, function (index, item) {
              const selected = selectedCategories.includes(item.id);
              const option = new Option(item.name, item.id, false, selected)
              categoriesSelect2.append(option);
            });
            categoriesSelect2.trigger('change');
          }
        }
      });
    }
  });
});

