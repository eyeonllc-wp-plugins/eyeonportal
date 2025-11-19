jQuery(function ($) {
  elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
    const widgetName = model.attributes.widgetType;

    if (widgetName === 'eyeon_stores_widget') {
      const tagsSelect2 = $('.elementor-control-retailer_tags select');
      const uniqueTagIDs = new Set();
      const selectedOptions = [];

      const selectedTags = model.attributes.settings.attributes.retailer_tags;
      $.each(selectedTags, function (index, item) {
        const parsedTag = JSON.parse(item);
        const option = new Option(parsedTag.text, item, false, false)
        option.selected = true;
        tagsSelect2.append(option);
        uniqueTagIDs.add(parsedTag.id);
        selectedOptions.push(item);
      });
      tagsSelect2.trigger('change');

      tagsSelect2.select2({
        ajax: {
          // url: tagsCustomData.api_endpoint,
          // dataType: 'json',
          // delay: 250,
          // headers: {
          //   center_id: tagsCustomData.center_id
          // },
          // data: function (params) {
          //   return {
          //     limit: 100,
          //     page: 1,
          //     search: params.term,
          //   };
          // },

          url: tagsCustomData.ajaxurl + '?api=' + tagsCustomData.api_endpoint,
          data: function (params) {
            return {
              action: 'eyeon_api_request',
              apiUrl: tagsCustomData.api_endpoint,
              params: {
                limit: 100,
                page: 1,
                search: params.term,
              }
            }
          },
          method: 'POST',
          dataType: 'json',
          delay: 250,
          xhrFields: {
            withCredentials: true
          },
          processResults: function (data) {
            const options = [];

            if (data.items) {
              $.each(data.items, function (index, item) {
                if (!uniqueTagIDs.has(item.id)) {
                  const option = {
                    text: item.name,
                    id: JSON.stringify({ id: item.id, text: item.name })
                  }
                  options.push(option);
                }
              });
            }

            return {
              results: selectedOptions.concat(options),
            };
          },
          cache: true,
        },
      });

    }
  });
});
