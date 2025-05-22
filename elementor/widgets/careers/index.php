<?php

class EyeOn_Careers_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_careers_widget';
  }

  public function get_title() {
      return __( 'EyeOn Careers', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-person';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  public function get_script_depends() {
		return [
      'eyeon-moment',
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
          '{{WRAPPER}} .eyeon-careers .careers-list' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
        ],
      ]
    );

    $this->add_control(
      'career_excerpt',
      [
        'label' => esc_html__( 'Excerpt', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Show', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'expiry_date',
      [
        'label' => esc_html__( 'Expiry Date', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-careers .careers-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'career_retailer_logo_style_settings',
      [
        'label' => esc_html__( 'Retailer Logo', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'career_retailer_logo_bg_color',
      [
        'label' => esc_html__( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-careers .careers-list .career .retailer-logo' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_responsive_control(
      'career_retailer_logo_height',
      [
        'label' => esc_html__( 'Logo Height', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 400,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 210,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-careers .careers-list .career .retailer-logo' => 'padding-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'career_retailer_logo_padding',
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
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 0,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-careers .careers-list .career .retailer-logo img' => 'padding: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();
    
    $this->start_controls_section(
      'career_content_box_style_settings',
      [
      'label' => esc_html__( 'Content Box', EYEON_NAMESPACE ),
      'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );
      
    $this->add_control(
			'career_content_box_text_color',
			[
				'label' => esc_html__( 'Background Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content' => 'background-color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
			'career_content_box_padding',
			[
				'label' => __( 'Padding', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

    $this->end_controls_section();

    $this->start_controls_section(
      'career_title_style_settings',
      [
        'label' => esc_html__( 'Title', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'career_title_typography',
        'selector' => '{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-title',
      ]
    );

    $this->add_control(
			'career_title_text_color',
			[
				'label' => esc_html__( 'Text Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-title' => 'color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
			'career_title_text_align',
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
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-title' => 'text-align: {{VALUE}};',
				],
			]
		);

    $this->add_control(
      'career_title_max_lines',
      [
        'label' => __( 'Max Lines', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          '1' => __( '1', EYEON_NAMESPACE ),
          '2' => __( '2', EYEON_NAMESPACE ),
          '3' => __( '3', EYEON_NAMESPACE ),
          '4' => __( '4', EYEON_NAMESPACE ),
          '5' => __( '5', EYEON_NAMESPACE ),
        ],
        'default' => '2',
        'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-title' => '-webkit-line-clamp: {{VALUE}};',
				],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'career_excerpt_style_settings',
      [
        'label' => esc_html__( 'Excerpt', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'career_excerpt' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'career_excerpt_typography',
        'selector' => '{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-excerpt',
      ]
    );

    $this->add_control(
			'career_excerpt_text_color',
			[
				'label' => esc_html__( 'Text Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
        'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-excerpt' => 'color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
			'career_excerpt_text_align',
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
					'justify' => [
						'title' => __( 'Justify', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-excerpt' => 'text-align: {{VALUE}};',
				],
			]
		);

    $this->add_control(
      'career_excerpt_max_lines',
      [
        'label' => __( 'Max Lines', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          '1' => __( '1', EYEON_NAMESPACE ),
          '2' => __( '2', EYEON_NAMESPACE ),
          '3' => __( '3', EYEON_NAMESPACE ),
          '4' => __( '4', EYEON_NAMESPACE ),
          '5' => __( '5', EYEON_NAMESPACE ),
        ],
        'default' => '2',
        'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-excerpt' => '-webkit-line-clamp: {{VALUE}};',
				],
      ]
    );

    $this->add_responsive_control(
      'deal_title_margin_top',
      [
        'label' => esc_html__( 'Margin Top', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
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
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'career_expiry_style_settings',
      [
        'label' => esc_html__( 'Expiry Date', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'expiry_date' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'career_expiry_typography',
        'selector' => '{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-expiry',
      ]
    );

    $this->add_control(
			'career_expiry_text_color',
			[
				'label' => esc_html__( 'Text Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
        'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-expiry' => 'color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
			'career_expiry_text_align',
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
					'{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-expiry' => 'text-align: {{VALUE}};',
				],
			]
		);

    $this->add_responsive_control(
      'career_expiry_margin_top',
      [
        'label' => esc_html__( 'Margin Top', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
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
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-careers .careers-list .career .career-content .career-expiry' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
			'career_expiry_prefix',
			[
				'label' => esc_html__( 'Prefix', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Apply before', EYEON_NAMESPACE ),
			]
		);

    $this->add_control(
			'career_expiry_suffix',
			[
				'label' => esc_html__( 'Suffix', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '', EYEON_NAMESPACE ),
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
        'default' => 'More Careers Coming Soon!',
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
          '{{WRAPPER}} .eyeon-careers .no-items-found' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'no_results_found_typography',
        'selector' => '{{WRAPPER}} .eyeon-careers .no-items-found',
      ]
    );

    $this->add_control(
      'no_results_found_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-careers .no-items-found' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();
  }

}
