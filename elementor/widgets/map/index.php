<?php

class EyeOn_Map_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_map_widget';
  }

  public function get_title() {
      return __( 'EyeOn Map', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-wordpress';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  public function get_script_depends() {
		return [
      'eyeon-map'
    ];
	}
  
	public function get_style_depends() {
    return [
      'eyeon-map',
      'eyeon-elementor-style'
    ];
	}

  protected function render() {
    global $mcd_settings;
    include dirname(__FILE__) . '/render.php';
  }

  protected function register_controls() {

    $this->start_controls_section(
      'map_settings',
      [
        'label' => esc_html__( 'Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_responsive_control(
      'map_height',
      [
        'label' => esc_html__( 'Map Height', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 600,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-map .eyeon-wrapper #root' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

  }

}
