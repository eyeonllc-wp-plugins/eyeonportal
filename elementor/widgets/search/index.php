<?php

class EyeOn_Search_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_search_widget';
  }

  public function get_title() {
      return __( 'EyeOn Search', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-search-bold';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  public function get_script_depends() {
    return [];
  }
  
  public function get_style_depends() {
    return [
      'eyeon-elementor-style'
    ];
  }

  protected function render() {
    global $mcd_settings;
    include dirname(__FILE__) . '/render.php';
  }

  protected function register_controls() {

    $this->start_controls_section(
      'search_bar_content_settings',
      [
        'label' => __( 'Search Bar', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'search_placeholder_text',
      [
        'label' => __( 'Placeholder Text', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Search',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'search_icon',
      [
        'label' => __( 'Search Icon', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // SEARCH ICON STYLES
    // ================================================================

    $this->start_controls_section(
      'search_results_content_settings',
      [
        'label' => __( 'Search Results', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'search_results_retailer_logo',
      [
        'label' => __( 'Retailer Logo', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'search_results_categories',
      [
        'label' => __( 'Categories', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // SEARCH ICON STYLES
    // ================================================================

    $this->start_controls_section(
      'search_bar_styles',
      [
        'label' => __( 'Search Bar', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'search_bar_height',
      [
        'label' => esc_html__( 'Height', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 42,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'search_bar_border_width',
      [
        'label' => __( 'Border Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 5,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 1,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar' => 'border-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'search_bar_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar' => 'background-color: {{VALUE}}',
        ],
        'default' => '#F8F8F8',
      ]
    );

    $this->add_control(
      'search_bar_border_color',
      [
        'label' => __( 'Border Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar' => 'border-color: {{VALUE}}',
        ],
        'default' => '#CCCCCC',
      ]
    );

    $this->add_responsive_control(
      'search_bar_border_radius',
      [
        'label' => __( 'Border Radius', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 30,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 4,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar' => 'border-radius: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // SEARCH ICON STYLES
    // ================================================================

    $this->start_controls_section(
      'search_icon_styles',
      [
        'label' => __( 'Search Icon', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'search_icon' => 'show',
        ],
      ]
    );

    $this->add_control(
      'search_icon_size',
      [
        'label' => __( 'Size', EYEON_NAMESPACE ),
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
          'unit' => 'px',
          'size' => 20,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar .icon-search' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'search_icon_color',
      [
        'label' => __( 'Icon Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#888888',
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar .icon-search' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // SEARCH ICON STYLES
    // ================================================================

    $this->start_controls_section(
      'search_input_styles',
      [
        'label' => __( 'Search Input', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'search_input_typography',
        'selector' => '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar .stores-search',
      ]
    );

    $this->add_control(
      'search_input_text_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#444444',
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar .stores-search' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'search_input_placeholder_color',
      [
        'label' => __( 'Placeholder Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#888888',
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-bar .stores-search::placeholder' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // Search Results Retailer Name
    // ================================================================
    $this->start_controls_section(
      'section_retailer_name',
      [
        'label' => esc_html__('Retailer Name', EYEON_NAMESPACE),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'retailer_name_typography',
        'label' => esc_html__('Typography', EYEON_NAMESPACE),
        'selector' => '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-result-item .retailer-name',
      ]
    );

    $this->add_control(
      'retailer_name_color',
      [
        'label' => esc_html__('Color', EYEON_NAMESPACE),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-result-item .retailer-name' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'retailer_name_spacing',
      [
        'label' => esc_html__('Bottom Spacing', EYEON_NAMESPACE),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 20,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 2,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-result-item .retailer-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // Search Results Categories
    // ================================================================
    $this->start_controls_section(
      'section_retailer_categories',
      [
        'label' => esc_html__('Categories', EYEON_NAMESPACE),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'search_results_categories' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'retailer_category_typography',
        'label' => esc_html__('Typography', EYEON_NAMESPACE),
        'selector' => '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-result-item .retailer-category',
      ]
    );

    $this->add_control(
      'retailer_category_color',
      [
        'label' => esc_html__('Color', EYEON_NAMESPACE),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-search .eyeon-wrapper .search-result-item .retailer-category' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();
  }
}

