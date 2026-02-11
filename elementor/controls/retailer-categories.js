jQuery(function ($) {
  elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
    const widgetName = model.attributes.widgetType;

    if (widgetName === 'eyeon_stores_widget') {
      const categoriesSelect2 = $('.elementor-control-retailer_categories select');

      const selectedCategories = model.attributes.settings.attributes.retailer_categories.map(function (item) {
        return parseInt(item);
      });

      $.ajax({
        url: categoriesCustomData.ajaxurl + '?api=' + categoriesCustomData.api_endpoint,
        data: {
          action: 'eyeon_api_request',
          nonce: categoriesCustomData.nonce,
          apiUrl: categoriesCustomData.api_endpoint,
          force_refresh: true,
          paginated_data: true,
          params: {
            group: true
          }
        },
        method: 'POST',
        dataType: 'json',
        xhrFields: {
          withCredentials: true
        },
        success: function (response) {
          console.log('response', response);
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

