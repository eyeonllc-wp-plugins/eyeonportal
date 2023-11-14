<?php

$this->start_controls_section(
  'carousel_settings',
  [
    'label' => __( 'Carousel Settings', EYEON_NAMESPACE ),
    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    'condition' => [
      'view_mode' => 'carousel',
    ],
  ]
);

$this->add_responsive_control(
  'carousel_items',
  [
    'type' => \Elementor\Controls_Manager::NUMBER,
    'label' => __( 'Items', EYEON_NAMESPACE ),
    'min' => 1,
    'max' => 20,
    'step' => 1,
    'default' => 6,
    'tablet_default' => 4,
    'mobile_default' => 2,
  ]
);

$this->add_responsive_control(
  'carousel_slideby',
  [
    'type' => \Elementor\Controls_Manager::NUMBER,
    'label' => __( 'Slide By', EYEON_NAMESPACE ),
    'min' => 1,
    'max' => 10,
    'step' => 1,
    'default' => 1,
    'tablet_default' => 1,
    'mobile_default' => 1,
  ]
);

$this->add_responsive_control(
  'carousel_margin',
  [
    'type' => \Elementor\Controls_Manager::NUMBER,
    'label' => __( 'Space Between', EYEON_NAMESPACE ),
    'min' => 0,
    'max' => 60,
    'step' => 1,
    'default' => 15,
    'tablet_default' => 15,
    'mobile_default' => 15,
  ]
);

$this->add_control(
  'carousel_navigation',
  [
    'label' => __( 'Navigation', EYEON_NAMESPACE ),
    'type' => \Elementor\Controls_Manager::SWITCHER,
    'label_on' => __( 'Show', EYEON_NAMESPACE ),
    'label_off' => __( 'Hide', EYEON_NAMESPACE ),
    'return_value' => 'show',
    'default' => 'show',
  ]
);

$this->add_control(
  'carousel_dots',
  [
    'label' => __( 'Dots', EYEON_NAMESPACE ),
    'type' => \Elementor\Controls_Manager::SWITCHER,
    'label_on' => __( 'Show', EYEON_NAMESPACE ),
    'label_off' => __( 'Hide', EYEON_NAMESPACE ),
    'return_value' => 'show',
    'default' => '',
  ]
);

$this->add_control(
  'carousel_autoplay',
  [
    'label' => __( 'Autoplay', EYEON_NAMESPACE ),
    'type' => \Elementor\Controls_Manager::SWITCHER,
    'label_on' => __( 'Yes', EYEON_NAMESPACE ),
    'label_off' => __( 'No', EYEON_NAMESPACE ),
    'return_value' => 'yes',
    'default' => 'no',
  ]
);

$this->add_control(
  'carousel_autoplay_speed',
  [
    'type' => \Elementor\Controls_Manager::NUMBER,
    'label' => __( 'Autoplay Speed', EYEON_NAMESPACE ),
    'placeholder' => '0',
    'min' => 1000,
    'max' => 10000,
    'step' => 100,
    'default' => 5000,
    'condition' => [
      'carousel_autoplay' => 'yes',
    ],
  ]
);
  
$this->add_control(
  'carousel_loop',
  [
    'label' => __( 'Loop', EYEON_NAMESPACE ),
    'type' => \Elementor\Controls_Manager::SWITCHER,
    'label_on' => __( 'Yes', EYEON_NAMESPACE ),
    'label_off' => __( 'No', EYEON_NAMESPACE ),
    'return_value' => 'yes',
    'default' => 'no',
  ]
);

$this->end_controls_section();

