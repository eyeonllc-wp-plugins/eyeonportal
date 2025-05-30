<?php

class EyeOn_Stores_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_stores_widget';
  }

  public function get_title() {
      return __( 'EyeOn Stores', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-cart-medium';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  public function get_script_depends() {
    return [
      'eyeon-elementor-utils',
      'eyeon-owl-carousel'
    ];
  }
  
  public function get_style_depends() {
    return [
      'eyeon-owl-carousel',
      'eyeon-owl-carousel-theme',
      'eyeon-elementor-style'
    ];
  }

  protected function render() {
    global $mcd_settings;
    include dirname(__FILE__) . '/render.php';
  }

  protected function register_controls() {

    $this->start_controls_section(
      'content_settings',
      [
        'label' => __( 'Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'view_mode',
      [
        'label' => __( 'View Mode', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'grid',
        'options' => [
          'grid' => __( 'Grid', EYEON_NAMESPACE ),
          'carousel' => __( 'Carousel', EYEON_NAMESPACE ),
        ],
      ]
    );

    $this->add_control(
      'fetch_all',
      [
        'label' => __( 'Fetch All', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Yes', EYEON_NAMESPACE ),
        'label_off' => __( 'No', EYEON_NAMESPACE ),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'fetch_limit',
      [
        'type' => \Elementor\Controls_Manager::NUMBER,
        'label' => __( 'Limit', EYEON_NAMESPACE ),
        'placeholder' => '0',
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'default' => 18,
        'condition' => [
          'fetch_all' => '',
        ],
      ]
    );

    $this->add_responsive_control(
      'items_per_row',
      [
        'type' => \Elementor\Controls_Manager::NUMBER,
        'label' => __( 'Items per Row', EYEON_NAMESPACE ),
        'min' => 1,
        'max' => 10,
        'step' => 1,
        'default' => 6,
        'tablet_default' => 4,
        'mobile_default' => 2,
        'render_type' => 'ui',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
        ],
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_control(
      'stores_search',
      [
        'label' => __( 'Search', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => '',
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_control(
      'categories_sidebar',
      [
        'label' => __( 'Categories', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          'hide' => __( 'Hide', EYEON_NAMESPACE ),
          'show' => __( 'Sidebar', EYEON_NAMESPACE ),
          'dropdown' => __( 'Dropdown', EYEON_NAMESPACE ),
        ],
        'default' => 'hide',
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_control(
      'deal_flag',
      [
        'label' => __( 'Deal Flag', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'custom_flags',
      [
        'label' => __( 'Custom Flags', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'retailer_categories',
      [
        'label' => __( 'Categories', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'options' => [],
        'default' => [],
        'multiple' => true,
        'label_block' => true,
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'retailer_tags',
      [
        'label' => __( 'Tags', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'options' => [],
        'default' => [],
        'multiple' => true,
        'label_block' => true
      ]
    );

    $this->add_control(
      'featured_image',
      [
        'label' => __( 'Featured Image', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => '',
        'conditions' => [
          'terms' => [
            [
              'name' => 'view_mode',
              'operator' => '===',
              'value' => 'grid',
            ],
            [
              'name' => 'retailer_categories',
              'operator' => 'contains',
              'value' => RESTAURANTS_CATEGORY_ID,
            ],
          ]
        ],
      ]
    );


    $this->add_control(
			'custom_center_id',
			[
				'label' => esc_html__( 'CENTER ID', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::TEXT,
        'description' => 'Override the Center ID you have in plugin settings.'
			]
		);

    $this->end_controls_section();

    include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/controls.php');

    // ================================================================
    // HEADER HEADING CONTENT
    // ================================================================

    $this->start_controls_section(
      'header_heading_content_settings',
      [
        'label' => __( 'Header', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        'condition' => [
          'view_mode' => 'grid',
          'categories_sidebar' => 'dropdown',
        ],
      ]
    );

    $this->add_control(
      'header_heading_show',
      [
        'label' => __( 'Heading', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'header_heading_text',
      [
        'label' => __( 'Text', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Directory',
      ]
    );

    $this->add_control(
      'header_heading_link',
      [
        'label' => __( 'Link', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::URL,
        // 'dynamic' => [
        //   'active' => true,
        // ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // HEADER STYLES
    // ================================================================

    $this->start_controls_section(
      'header_style_settings',
      [
        'label' => __( 'Header', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
          'categories_sidebar' => 'dropdown',
        ],
      ]
    );

    $this->add_responsive_control(
      'stores_header_height',
      [
        'label' => __( 'Height', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 48,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-categories-select .custom-select-wrapper .custom-select .custom-select__trigger' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-directory-heading span' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .search-bar .stores-search' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'stores_header_gap',
      [
        'label' => __( 'Gap', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 30,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header.with-dropdown' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'stores_header_margin_bottom',
      [
        'label' => __( 'Margin Bottom', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 30,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header.with-dropdown' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // HEADER CATEGORIES DROPDOWN STYLES
    // ================================================================

    $this->start_controls_section(
      'categories_dropdown_style_settings',
      [
        'label' => __( 'Categories Dropdown', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
          'categories_sidebar' => 'dropdown',
        ],
      ]
    );

    $this->add_control(
      'categories_dropdown_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#a9dfe9',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-categories-select .custom-select-wrapper .custom-select .custom-select__trigger' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'categories_dropdown_text_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#666',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-categories-select .custom-select-wrapper .custom-select .custom-select__trigger' => 'color: {{VALUE}}',
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-categories-select .custom-select-wrapper .custom-select .custom-select__trigger svg' => 'fill: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'categories_dropdown_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-categories-select .custom-select-wrapper .custom-select .custom-select__trigger span',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // HEADER HEADING STYLES
    // ================================================================

    $this->start_controls_section(
      'header_heading_style_settings',
      [
        'label' => __( 'Heading', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
          'categories_sidebar' => 'dropdown',
        ],
      ]
    );

    $this->add_control(
      'header_heading_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#a9dfe9',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-directory-heading' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'header_heading_text_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#666',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-directory-heading span' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'header_heading_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .eyeon-wrapper .stores-header .stores-directory-heading span',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // GRID SETTINGS
    // ================================================================

    $this->start_controls_section(
      'grid_style_settings',
      [
        'label' => __( 'Grid Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_responsive_control(
      'grid_gap',
      [
        'label' => __( 'Spacing', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 50,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 15,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores' => 'grid-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // GRID ITEM
    // ================================================================

    $this->start_controls_section(
      'grid_item_style_settings',
      [
        'label' => __( 'Retailer Logo', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'store_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image img.retailer-logo' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_responsive_control(
      'store_padding',
      [
        'label' => __( 'Image Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 30,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image img.retailer-logo' => 'padding: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'hover_style',
      [
        'label' => __( 'Hover Style', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => __( 'None', EYEON_NAMESPACE ),
          'grayscale' => __( 'Grayscale', EYEON_NAMESPACE ),
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // CATEGORIES STYLE
    // ================================================================

    $this->start_controls_section(
      'categories_style_settings',
      [
        'label' => __( 'Categories Sidebar', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
          'categories_sidebar' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_width',
      [
        'label' => __( 'Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 150,
            'max' => 400,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 220,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .eyeon-stores .stores-header .categories-sidebar-placeholder' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_item_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 10,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories li' => 'padding: {{SIZE}}{{UNIT}} 0;',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_stores_gap',
      [
        'label' => __( 'Categories & Stores Gap', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols' => 'gap: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .eyeon-stores .stores-header' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'text_align',
      [
        'label' => __( 'Alignment', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-left',
          ],
          'right' => [
            'title' => __( 'Right', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-right',
          ],
        ],
        'default' => 'right',
        'toggle' => true,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories li' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'categories_titles_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories li',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // DEAL FLAG
    // ================================================================

    $this->start_controls_section(
      'deal_flag_style_settings',
      [
        'label' => __( 'Deal Flag', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'deal_flag' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_flag_top',
      [
        'label' => __( 'Top Position', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 40,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .deal-flag' => 'top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_flag_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .deal-flag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'deal_flag_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#58a8ca',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .deal-flag' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'deal_flag_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .deal-flag',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // CUSTOM FLAGS
    // ================================================================

    $this->start_controls_section(
      'custom_flags_style_settings',
      [
        'label' => __( 'Custom Flags', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'custom_flags' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'custom_flags_top',
      [
        'label' => __( 'Top Position', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 40,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .custom-flags' => 'top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'custom_flags_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .custom-flags li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'custom_flags_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#58a8ca',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .custom-flags li' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'custom_flags_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .custom-flags li',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // RETAILER LOCATION
    // ================================================================

    $this->start_controls_section(
      'retailer_location_style_settings',
      [
        'label' => __( 'Retailer Location', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'retailer_location_position',
      [
        'label' => __( 'Position', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
					'left' => [
						'title' => esc_html__( 'Left', EYEON_NAMESPACE ),
						'icon' => 'eicon-order-start',
					],
					'fullwidth' => [
						'title' => esc_html__( 'Full Width', EYEON_NAMESPACE ),
						'icon' => 'eicon-grow',
					],
          'right' => [
            'title' => __( 'Right', EYEON_NAMESPACE ),
            'icon' => 'eicon-order-end',
          ],
        ],
        'default' => 'left',
        'toggle' => true,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location' => '{{VALUE}}',
        ],
        'selectors_dictionary' => [
          'left' => 'left: 0; right: auto;',
          'right' => 'left: auto; right: 0;',
          'fullwidth' => 'left: 0; right: 0;',
        ],
      ]
    );

    $this->add_control(
      'retailer_location_align',
      [
        'label' => __( 'Text Alignment', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => __( 'Center', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => __( 'Right', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-right',
          ],
        ],
        'default' => 'center',
        'toggle' => true,
        'condition' => [
          'retailer_location_position' => 'fullwidth',
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'retailer_location_bottom',
      [
        'label' => __( 'Bottom', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 40,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'size' => '8',
          'unit' => 'px',
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location' => 'bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'retailer_location_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px' ],
        'default' => [
          'top' => '4',
          'right' => '8',
          'bottom' => '4',
          'left' => '8',
          'unit' => 'px',
          'isLinked' => false,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'retailer_location_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#58a8ca',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'retailer_location_text_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#FFFFFF',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'retailer_location_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image .retailer-location',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // NO RESULTS FOUND - STYLE
    // ================================================================

    $this->start_controls_section(
      'no_results_found_style_settings',
      [
        'label' => __( 'No Results Found', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'no_results_found_text',
      [
        'label' => __( 'Text', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Retailers Coming Soon!',
        'label_block' => true,
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'no_results_found_align',
      [
        'label' => __( 'Alignment', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => __( 'Center', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => __( 'Right', EYEON_NAMESPACE ),
            'icon' => 'eicon-text-align-right',
          ],
        ],
        'default' => 'center',
        'toggle' => true,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .no-items-found' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'no_results_found_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .no-items-found',
      ]
    );

    $this->add_control(
      'no_results_found_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .no-items-found' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();

  }

}
