<?php

class EyeOn_Events_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_events_widget';
  }

  public function get_title() {
      return __( 'EyeOn Events', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-wordpress';
  }

  public function get_categories() {
      return ['eyeon'];
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
          '{{WRAPPER}} .eyeon-events .events-list' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
        ],
      ]
    );

    $this->add_control(
			'hr_1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

    $this->add_control(
      'event_title',
      [
        'label' => esc_html__( 'Title', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Show', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'event_excerpt',
      [
        'label' => esc_html__( 'Excerpt', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Show', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => '',
      ]
    );

    $this->add_control(
      'event_metadata',
      [
        'label' => esc_html__( 'Date & Time', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Show', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
			'hr_2',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

    $this->add_control(
      'external_event_new_tab',
      [
        'label' => esc_html__( 'External Events in New Tab', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', EYEON_NAMESPACE ),
        'label_off' => esc_html__( 'No', EYEON_NAMESPACE ),
        'return_value' => 'yes',
        'default' => 'yes',
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
          '{{WRAPPER}} .eyeon-events .events-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'event_title_style_settings',
      [
        'label' => esc_html__( 'Title', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'event_title' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'event_title_typography',
        'selector' => '{{WRAPPER}} .eyeon-events .events-list .event .event-title',
      ]
    );

    $this->add_control(
			'event_title_text_color',
			[
				'label' => esc_html__( 'Text Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eyeon-events .events-list .event .event-title' => 'color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
      'event_title_spacing',
      [
        'label' => esc_html__( 'Spacing', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-events .events-list .event .event-title' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'event_excerpt_style_settings',
      [
        'label' => esc_html__( 'Excerpt', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'event_excerpt' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'event_excerpt_typography',
        'selector' => '{{WRAPPER}} .eyeon-events .events-list .event .event-excerpt',
      ]
    );

    $this->add_responsive_control(
      'event_excerpt_spacing',
      [
        'label' => esc_html__( 'Spacing', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-events .events-list .event .event-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'event_metadata_style_settings',
      [
        'label' => esc_html__( 'Date & Time', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'event_metadata' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'event_metadata_typography',
        'selector' => '{{WRAPPER}} .eyeon-events .events-list .event .metadata',
      ]
    );

    $this->add_control(
			'event_metadata_icon_text_color',
			[
				'label' => esc_html__( 'Icon Color', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eyeon-events .events-list .event .metadata i.far' => 'color: {{VALUE}}',
				],
			]
		);

    $this->add_responsive_control(
      'event_metadata_icon_size',
      [
        'label' => esc_html__( 'Icon Size', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 32,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-events .events-list .event .metadata i.far' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'event_metadata_margin_top',
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
        'selectors' => [
          '{{WRAPPER}} .eyeon-events .events-list .event .metadata' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
			'event_metadata_direction',
			[
				'label' => esc_html__( 'Direction', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'row' => [
						'title' => esc_html__( 'Row - horizontal', EYEON_NAMESPACE ),
						'icon' => 'eicon-arrow-right',
					],
					'column' => [
						'title' => esc_html__( 'Column - vertical', EYEON_NAMESPACE ),
						'icon' => 'eicon-arrow-down',
					],
				],
				'default' => 'row',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eyeon-events .events-list .event .metadata' => 'flex-direction: {{VALUE}};',
				],
			]
		);

    $this->add_responsive_control(
      'event_metadata_flex_spacing',
      [
        'label' => esc_html__( 'Spacing', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-events .events-list .event .metadata' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

  }

}
