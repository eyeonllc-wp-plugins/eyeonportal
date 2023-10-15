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

  protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Settings', EYEON_NAMESPACE ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// $this->add_control(
		// 	'title',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::TEXT,
		// 		'label' => esc_html__( 'Title', EYEON_NAMESPACE ),
		// 		'placeholder' => esc_html__( 'Enter your title', EYEON_NAMESPACE ),
		// 	]
		// );

		$this->add_responsive_control(
			'items_per_row',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Items per Row', EYEON_NAMESPACE ),
				'placeholder' => '0',
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 6,
        'render_type' => 'ui',
        'selectors' => [
          '{{WRAPPER}}' => 'grid-gap: {{SIZE}}px;',
        ],
			]
		);

    // $this->add_responsive_control(
    //   'padding1',
    //   [
    //     'type' => \Elementor\Controls_Manager::DIMENSIONS,
    //     'label' => esc_html__('Padding', EYEON_NAMESPACE),
    //     'size_units' => ['px', 'em', '%'],
    //     'selectors' => [
    //       '{{SELECTOR}} .your-element-class' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
    //     ],
    //     'render_type' => 'ui',
    //   ]
    // );

    $this->add_responsive_control(
      'items_spacing1',
      [
        'label' => esc_html__( 'Spacing', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
        'size_units' => ['px', 'em', '%'],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'padding: {{SIZE}}{{UNIT}};',
				],
      ]
    );

		// $this->add_control(
		// 	'open_lightbox',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'label' => esc_html__( 'Lightbox', EYEON_NAMESPACE ),
		// 		'options' => [
		// 			'default' => esc_html__( 'Default', EYEON_NAMESPACE ),
		// 			'yes' => esc_html__( 'Yes', EYEON_NAMESPACE ),
		// 			'no' => esc_html__( 'No', EYEON_NAMESPACE ),
		// 		],
		// 		'default' => 'no',
		// 	]
		// );

		// $this->add_control(
		// 	'alignment',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::CHOOSE,
		// 		'label' => esc_html__( 'Alignment', EYEON_NAMESPACE ),
		// 		'options' => [
		// 			'left' => [
		// 				'title' => esc_html__( 'Left', EYEON_NAMESPACE ),
		// 				'icon' => 'eicon-text-align-left',
		// 			],
		// 			'center' => [
		// 				'title' => esc_html__( 'Center', EYEON_NAMESPACE ),
		// 				'icon' => 'eicon-text-align-center',
		// 			],
		// 			'right' => [
		// 				'title' => esc_html__( 'Right', EYEON_NAMESPACE ),
		// 				'icon' => 'eicon-text-align-right',
		// 			],
		// 		],
		// 		'default' => 'center',
		// 	]
		// );

		$this->end_controls_section();

	}

  protected function render() {
    $settings = $this->get_settings();
    add_action('elementor/frontend/after_enqueue_styles', function() {
      wp_enqueue_style('eyeon-stores-widget-styles', MCD_PLUGIN_URL . 'elementor/stores/css/style.css', [], '1.0.0');
    });
    include dirname(__FILE__) . '/render.php';
  }

}
