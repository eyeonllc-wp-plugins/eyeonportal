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
      'day_names',
      [
        'label' => __( 'Day Names', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'long',
        'options' => [
          'short' => __( 'Short', EYEON_NAMESPACE ),
          'long' => __( 'Long', EYEON_NAMESPACE ),
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
      ]
    );

    $this->end_controls_section();

    // include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/controls.php');

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

  }

}
