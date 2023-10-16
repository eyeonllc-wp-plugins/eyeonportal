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
    add_action('elementor/frontend/after_enqueue_styles', function() {
      wp_enqueue_style('eyeon-stores-widget-styles', MCD_PLUGIN_URL . 'elementor/stores/css/style.css', [], '1.0.0');
    });
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
				'label' => esc_html__( 'Retailers Limit', EYEON_NAMESPACE ),
				'placeholder' => '0',
				'min' => 1,
				'max' => 1000,
				'step' => 1,
				'default' => 20,
        'condition' => [
          'fetch_all' => '',
        ],
        'validate' => function( $value ) {
          if ( $value < 0 || $value > 100 ) {
            return __( 'Invalid value. Please enter a value between 0 and 100.', 'your-text-domain' );
          }
          return true;
        },
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
			'show_categories',
			[
				'label' => esc_html__( 'Show Categories', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', EYEON_NAMESPACE ),
				'label_off' => esc_html__( 'No', EYEON_NAMESPACE ),
				'return_value' => 'yes',
				'default' => '',
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
			]
		);

    $this->add_responsive_control(
      'categories_width',
      [
        'label' => esc_html__( 'Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
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

    $this->end_controls_section();
  }

}
