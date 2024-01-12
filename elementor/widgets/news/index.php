<?php

class EyeOn_News_Widget extends \Elementor\Widget_Base {
  public function get_name() {
      return 'eyeon_news_widget';
  }

  public function get_title() {
      return __( 'EyeOn News', EYEON_NAMESPACE );
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
        'label' => __( 'Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'view_mode',
      [
        'label' => __( 'View Mode', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'grid',
        'options' => [
          'grid' => __( 'Grid', EYEON_NAMESPACE ),
          'carousel' => __( 'Carousel', EYEON_NAMESPACE ),
        ],
      ]
    );

    $this->add_control(
      'fetch_all',
      [
        'label' => __( 'Fetch All', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Yes', EYEON_NAMESPACE ),
        'label_off' => __( 'No', EYEON_NAMESPACE ),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'fetch_limit',
      [
        'type' => \Elementor\Controls_Manager::NUMBER,
        'label' => __( 'Limit', EYEON_NAMESPACE ),
        'placeholder' => '0',
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'default' => 18,
        'condition' => [
          'fetch_all' => '',
        ],
      ]
    );

    $this->add_responsive_control(
      'items_per_row',
      [
        'type' => \Elementor\Controls_Manager::NUMBER,
        'label' => __( 'Items per Row', EYEON_NAMESPACE ),
        'min' => 1,
        'max' => 10,
        'step' => 1,
        'default' => 5,
        'tablet_default' => 3,
        'mobile_default' => 2,
        'render_type' => 'ui',
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news.grid-view' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
        ],
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_control(
      'categories_filters',
      [
        'label' => __( 'Categories Filters', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_control(
			'hr_1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

    $this->end_controls_section();

    // include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/controls.php');

    $this->start_controls_section(
      'grid_style_settings',
      [
        'label' => __( 'Grid Settings', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
        ],
      ]
    );

    $this->add_responsive_control(
      'grid_gap',
      [
        'label' => __( 'Spacing', EYEON_NAMESPACE ),
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
          'size' => 20,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news.grid-view' => 'grid-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'grid_item_style_settings',
      [
        'label' => __( 'Retailer Logo', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'store_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image img.retailer-logo' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_responsive_control(
      'store_padding',
      [
        'label' => __( 'Image Padding', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-stores .stores-list .stores .store .image img.retailer-logo' => 'padding: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'hover_style',
      [
        'label' => __( 'Hover Style', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => __( 'None', EYEON_NAMESPACE ),
          'grayscale' => __( 'Grayscale', EYEON_NAMESPACE ),
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'categories_style_settings',
      [
        'label' => __( 'Categories', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
          'categories_sidebar' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_width',
      [
        'label' => __( 'Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 150,
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

    $this->add_responsive_control(
      'categories_item_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 10,
            'step' => 1
          ],
        ],
        'size_units' => ['px'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories li' => 'padding: {{SIZE}}{{UNIT}} 0;',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_stores_gap',
      [
        'label' => __( 'Categories & Stores Gap', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'selectors' => [
          '{{WRAPPER}} .eyeon-stores .content-cols' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
			'text_align',
			[
				'label' => __( 'Alignment', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __( 'Right', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'right',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eyeon-stores .content-cols .stores-categories li' => 'text-align: {{VALUE}};',
				],
			]
		);

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'categories_titles_typography',
        'selector' => '{{WRAPPER}} .eyeon-stores .content-cols .stores-categories li',
      ]
    );

    $this->end_controls_section();

    // ==============================================================================================
    // ==============================================================================================
    // ==============================================================================================

  }

}