<?php

class EyeOn_Stores_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_stores_widget';
  }

  public function get_title() {
      return __( 'EyeOn Stores', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-wordpress';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  protected function render() {
    global $mcd_settings;
    $settings = $this->get_settings_for_display();
    include dirname(__FILE__) . '/render.php';
  }

  protected function register_controls() {

    $this->start_controls_section(
      'content_settings',
      [
        'label' => esc_html__( 'Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'fetch_all',
      [
        'label' => esc_html__( 'Fetch All Retailers', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'No', EYEON_NAMESPACE ),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'fetch_limit',
      [
        'type' => \Elementor\Controls_Manager::NUMBER,
        'label' => esc_html__( 'Custom Limit', EYEON_NAMESPACE ),
        'placeholder' => '0',
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'default' => 20,
        'condition' => [
          'fetch_all' => '',
        ],
      ]
    );

    $this->add_responsive_control(
      'items_per_row',
      [
        'type' => \Elementor\Controls_Manager::NUMBER,
        'label' => esc_html__( 'Items per Row', EYEON_NAMESPACE ),
        'min' => 1,
        'max' => 10,
        'step' => 1,
        'default' => 6,
        'tablet_default' => 4,
        'mobile_default' => 2,
        'render_type' => 'ui',
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
        ],
      ]
    );

    $this->add_control(
      'categories_sidebar',
      [
        'label' => esc_html__( 'Categories Sidebar', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Show', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => '',
      ]
    );

    $this->add_control(
      'deal_flag',
      [
        'label' => esc_html__( 'Deal Flag', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Show', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'grid_style_settings',
      [
        'label' => esc_html__( 'Grid', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'grid_gap',
      [
        'label' => esc_html__( 'Spacing', EYEON_NAMESPACE ),
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

    $this->start_controls_section(
      'grid_item_style_settings',
      [
        'label' => esc_html__( 'Grid Item', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'store_bg_color',
      [
        'label' => esc_html__( 'Imgae Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image img' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_responsive_control(
      'store_padding',
      [
        'label' => esc_html__( 'Padding', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image img' => 'padding: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'hover_style',
      [
        'label' => esc_html__( 'Hover Style', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => esc_html__( 'None', EYEON_NAMESPACE ),
          'grayscale' => esc_html__( 'Grayscale', EYEON_NAMESPACE ),
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'categories_style_settings',
      [
        'label' => esc_html__( 'Categories', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'categories_sidebar' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_width',
      [
        'label' => esc_html__( 'Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 150,
            'max' => 400,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_item_padding',
      [
        'label' => esc_html__( 'Padding', EYEON_NAMESPACE ),
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
        'label' => esc_html__( 'Categories & Stores Gap', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-stores .content-cols' => 'grid-gap: {{SIZE}}{{UNIT}};',
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

    $this->start_controls_section(
      'deal_flag_style_settings',
      [
        'label' => esc_html__( 'Deal Flag', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'deal_flag' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_flag_top',
      [
        'label' => esc_html__( 'Top Position', EYEON_NAMESPACE ),
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

    $this->add_control(
			'deal_flag_padding',
			[
				'label' => esc_html__( 'Padding', EYEON_NAMESPACE ),
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
				'label' => esc_html__( 'Background Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
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

  }

}