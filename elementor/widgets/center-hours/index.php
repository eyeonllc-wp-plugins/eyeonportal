<?php

class EyeOn_Center_Hours_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_center_hours_widget';
  }

  public function get_title() {
      return __( 'EyeOn Center Hours', EYEON_NAMESPACE );
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
      'eyeon-elementor-utils',
      'eyeon-date-fns',
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
        'label' => __( 'Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'view_mode',
      [
        'label' => __( 'View Mode', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'week',
        'options' => [
          'week' => __( 'Week', EYEON_NAMESPACE ),
          'today' => __( 'Today', EYEON_NAMESPACE ),
        ],
      ]
    );

    $this->add_control(
      'day_name_type',
      [
        'label' => __( 'Day Names', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'long',
        'options' => [
          'long' => __( 'Long', EYEON_NAMESPACE ),
          'short' => __( 'Short', EYEON_NAMESPACE ),
        ],
        'condition' => [
          'view_mode' => 'week',
        ],
      ]
    );

    $this->add_control(
      'combine_days',
      [
        'label' => __( 'Combine Days', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Yes', EYEON_NAMESPACE ),
        'label_off' => __( 'No', EYEON_NAMESPACE ),
        'return_value' => 'yes',
        'default' => 'yes',
        'condition' => [
          'view_mode' => 'week',
        ],
      ]
    );

    $this->add_control(
      'center_hours_icon',
      [
        'label' => __( 'Icon', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::ICONS,
      ]
    );

    $this->add_control(
      'center_hours_extra_text',
      [
        'label' => __( 'Additional Text', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // Styles
    // ================================================================
    
    $this->start_controls_section(
      'style_settings',
      [
        'label' => __( 'Styles', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'row_gap',
      [
        'label' => __( 'Row Gap', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 40,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours' => 'gap: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'view_mode' => 'week',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'days_typography',
        'selector' => '{{WRAPPER}} .eyeon-center-hours .center-hours',
      ]
    );

    $this->add_control(
      'days_text_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours' => 'color: {{VALUE}}',
        ],
      ]
    );
    
    $this->end_controls_section();

    // ================================================================
    // ICON
    // ================================================================
    
    $this->start_controls_section(
      'icon_style_settings',
      [
        'label' => __( 'Icon', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'center_hours_icon!' => '',
      ]
    );

    $this->add_responsive_control(
      'icon_size',
      [
        'label' => __( 'Icon Size', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 80,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 40,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .icon-col i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .icon-col svg' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'icon_gap',
      [
        'label' => __( 'Icon Gap', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 40,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'icon_color',
      [
        'label' => __( 'Icon Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .icon-col i' => 'color: {{VALUE}}',
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .icon-col svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // ADDITIONAL TEXT
    // ================================================================
    
    $this->start_controls_section(
      'extra_text_style_settings',
      [
        'label' => __( 'Additional Text', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'center_hours_extra_text!' => '',
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'extra_text_typography',
        'selector' => '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .content-col .extra-text',
      ]
    );

    $this->add_responsive_control(
      'extra_text_padding_top',
      [
        'label' => __( 'Padding Top', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 40,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 12,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .content-col .extra-text' => 'padding-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'extra_text_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-center-hours .center-hours-wrapper .content-col .extra-text' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();
  }

}
