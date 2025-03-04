<?php

class EyeOn_Slider_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_slider_widget';
  }

  public function get_title() {
      return __( 'EyeOn Slider', EYEON_NAMESPACE );
  }

  public function get_icon() {
      return 'eicon-wordpress';
  }

  public function get_categories() {
      return ['eyeon'];
  }

  public function get_script_depends() {
    return [
      'eyeon-owl-carousel',
      'eyeon-elementor-center-website',
    ];
  }
  
  public function get_style_depends() {
    return [
      'eyeon-owl-carousel',
      'eyeon-owl-carousel-theme',
      'eyeon-elementor-style'
    ];
  }

  private function get_categories_from_api() {
    $slidersResp = mcd_api_data(MCD_API_SLIDERS);
    $options = array();
    if( isset($slidersResp['items']) & count($slidersResp['items'])>0 ) {
      foreach( $slidersResp['items'] as $slider ) {
        $options[$slider['id']] = $slider['name'];
      }
    }
    return $options;
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
      'slider_id',
      [
        'label' => __( 'Slider', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $this->get_categories_from_api(),
        'default' => 0,
        'label_block' => false,
        'frontend_available' => true,
      ]
    );

    $this->end_controls_section();

  }

}
