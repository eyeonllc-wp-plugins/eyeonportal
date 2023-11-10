jQuery(function ($) {
  elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
    const widgetName = model.attributes.widgetType;

    if (widgetName === 'eyeon_stores_widget' || widgetName === 'eyeon_carousel_widget') {
      const categoriesSelect2 = $('.elementor-control-retailer_categories select');
      const uniqueCategoryIDs = new Set();

      const selectedCategories = model.attributes.settings.attributes.retailer_categories;
      $.each(selectedCategories, function (index, item) {
        const parsedCategory = JSON.parse(item);
        const option = new Option(parsedCategory.text, item, false, false)
        option.selected = true;
        categoriesSelect2.append(option);
        uniqueCategoryIDs.add(parsedCategory.id);
      });
      categoriesSelect2.trigger('change');

      $.ajax({
        url: categoriesCustomData.api_endpoint,
        data: {
          limit: 100,
          page: 1,
        },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: categoriesCustomData.center_id
        },
        success: function (response) {
          if (response.items) {
            $.each(response.items, function (index, item) {
              if (!uniqueCategoryIDs.has(item.id)) {
                const option = new Option(item.name, JSON.stringify({ id: item.id, text: item.name }), false, false)
                categoriesSelect2.append(option);
                uniqueCategoryIDs.add(item.id);
              }
            });
            categoriesSelect2.trigger('change');
          }
        }
      });
    }
  });
});

