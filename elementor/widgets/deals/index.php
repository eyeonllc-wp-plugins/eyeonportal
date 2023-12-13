<?php

class EyeOn_Deals_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_deals_widget';
  }

  public function get_title() {
      return __( 'EyeOn Deals', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-wordpress';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  public function get_script_depends() {
		return [
      'eyeon-moment',
      'eyeon-elementor-utils'
    ];
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
      'content_settings',
      [
        'label' => esc_html__( 'Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'fetch_all',
      [
        'label' => esc_html__( 'Fetch All', EYEON_NAMESPACE ),
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
        'default' => 8,
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
        'default' => 4,
        'render_type' => 'ui',
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
        ],
        'condition' => [
          'view_mode' => 'grid',
        ],
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
            'max' => 60,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 20,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'deal_image_style_settings',
      [
        'label' => esc_html__( 'Deal Image', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'deal_image_margin_bottom',
      [
        'label' => esc_html__( 'Margin Bottom', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'deal_retailer_logo_style_settings',
      [
        'label' => esc_html__( 'Retailer Logo', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'deal_retailer_logo_bg_color',
      [
        'label' => esc_html__( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .retailer-logo' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_retailer_logo_width',
      [
        'label' => esc_html__( 'Logo Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 80,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .retailer-logo' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_retailer_logo_height',
      [
        'label' => esc_html__( 'Logo Height', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 80,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .retailer-logo' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_retailer_logo_padding',
      [
        'label' => esc_html__( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 20,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .retailer-logo' => 'padding: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'deal_retailer_logo_margin_right',
      [
        'label' => esc_html__( 'Spacing', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
			'deal_retailer_logo_self_align',
			[
				'label' => esc_html__( 'Self Align', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Flex Start', EYEON_NAMESPACE ),
						'icon' => 'eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html__( 'Center', EYEON_NAMESPACE ),
						'icon' => 'eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html__( 'Flex End', EYEON_NAMESPACE ),
						'icon' => 'eicon-align-end-v',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .retailer-logo' => 'align-self: {{VALUE}};',
				],
			]
		);

    $this->end_controls_section();

    $this->start_controls_section(
      'deal_title_style_settings',
      [
        'label' => esc_html__( 'Title', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'deal_title_typography',
        'selector' => '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .details .deal-title',
      ]
    );

    $this->add_control(
			'deal_title_text_color',
			[
				'label' => esc_html__( 'Text Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .details .deal-title' => 'color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
      'deal_title_margin_bottom',
      [
        'label' => esc_html__( 'Margin Bottom', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 20,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-deals .deals-list .deal .deal-content .details .deal-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

  }

}
