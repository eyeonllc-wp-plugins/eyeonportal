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
      'show_post_date',
      [
        'label' => __( 'Post Date', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
      ]
    );

    $this->add_control(
      'show_excerpt',
      [
        'label' => __( 'Excerpt', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', EYEON_NAMESPACE ),
        'label_off' => __( 'Hide', EYEON_NAMESPACE ),
        'return_value' => 'show',
        'default' => 'show',
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

    // ================================================================
    // FILTERS
    // ================================================================
    
    $this->start_controls_section(
      'categories_style_settings',
      [
        'label' => __( 'Categories', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'view_mode' => 'grid',
          'categories_filters' => 'show',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_margin_bottom',
      [
        'label' => __( 'Filters-Grid Gap', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 40,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-categories' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_item_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'default' => [
          'top' => '0',
          'right' => '18',
          'bottom' => '10',
          'left' => '18',
          'unit' => 'px',
          'isLinked' => false,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-categories ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'categories_item_bottom_width',
      [
        'label' => __( 'Border Width', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 5,
            'step' => 1
          ],
        ],
        'size_units' => ['px', '%'],
        'default' => [
          'unit' => 'px',
          'size' => 1,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-categories ul li' => 'border-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->start_controls_tabs(
			'item_style_tabs'
		);

      $this->start_controls_tab(
        'item_style_normal_tab',
        [
          'label' => esc_html__( 'Normal', EYEON_NAMESPACE ),
        ]
      );

        $this->add_group_control(
          \Elementor\Group_Control_Typography::get_type(),
          [
            'name' => 'item_style_normal_typography',
            'selector' => '{{WRAPPER}} .eyeon-news .news-categories ul li',
          ]
        );

        $this->add_control(
          'item_style_normal_color',
          [
            'label' => __( 'Text Color', EYEON_NAMESPACE ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
              '{{WRAPPER}} .eyeon-news .news-categories ul li' => 'color: {{VALUE}}',
            ],
          ]
        );

        $this->add_control(
          'item_style_normal_border_color',
          [
            'label' => __( 'Border Color', EYEON_NAMESPACE ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#DDD',
            'selectors' => [
              '{{WRAPPER}} .eyeon-news .news-categories ul li' => 'border-color: {{VALUE}}',
            ],
          ]
        );

        $this->add_control(
          'item_style_normal_bg_color',
          [
            'label' => __( 'Background Color', EYEON_NAMESPACE ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
              '{{WRAPPER}} .eyeon-news .news-categories ul li' => 'background-color: {{VALUE}}',
            ],
          ]
        );

      $this->end_controls_tab();

      $this->start_controls_tab(
        'item_style_active_tab',
        [
          'label' => esc_html__( 'Active', EYEON_NAMESPACE ),
        ]
      );

        $this->add_group_control(
          \Elementor\Group_Control_Typography::get_type(),
          [
            'name' => 'item_style_active_typography',
            'selector' => '{{WRAPPER}} .eyeon-news .news-categories ul li.active',
          ]
        );

        $this->add_control(
          'item_style_active_color',
          [
            'label' => __( 'Text Color', EYEON_NAMESPACE ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
              '{{WRAPPER}} .eyeon-news .news-categories ul li.active' => 'color: {{VALUE}}',
            ],
          ]
        );

        $this->add_control(
          'item_style_active_border_color',
          [
            'label' => __( 'Border Color', EYEON_NAMESPACE ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#888',
            'selectors' => [
              '{{WRAPPER}} .eyeon-news .news-categories ul li.active' => 'border-color: {{VALUE}}',
            ],
          ]
        );

        $this->add_control(
          'item_style_active_bg_color',
          [
            'label' => __( 'Background Color', EYEON_NAMESPACE ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
              '{{WRAPPER}} .eyeon-news .news-categories ul li.active' => 'background-color: {{VALUE}}',
            ],
          ]
        );

      $this->end_controls_tab();

    $this->end_controls_section();

    // ================================================================
    // GRID SETTINGS
    // ================================================================

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
      'grid_row_gap',
      [
        'label' => __( 'Horizontal Spacing', EYEON_NAMESPACE ),
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
          '{{WRAPPER}} .eyeon-news .news-list .news.grid-view' => 'column-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'grid_column_gap',
      [
        'label' => __( 'Vertical Spacing', EYEON_NAMESPACE ),
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
          'size' => 30,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news.grid-view' => 'row-gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // GRID SINGLE ITEM
    // ================================================================

    $this->start_controls_section(
      'grid_item_settings',
      [
        'label' => __( 'Single News Item', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'news_details_padding',
      [
        'label' => __( 'Padding', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'news_item_bg_color',
      [
        'label' => __( 'Background Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news .news-item' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();
    
    // ================================================================
    // POST DATE
    // ================================================================

    $this->start_controls_section(
      'post_date_style',
      [
        'label' => __( 'Post Date', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_post_date' => 'show',
        ],
      ]
    );
      
    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'post_date_typography',
        'selector' => '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-post-date',
      ]
    );

    $this->add_responsive_control(
			'post_date_align',
			[
				'label' => __( 'Alignment', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-post-date' => 'align-self: {{VALUE}};',
				],
			]
		);

    $this->end_controls_section();

    // ================================================================
    // NEWS TITLE
    // ================================================================

    $this->start_controls_section(
      'news_title_style',
      [
        'label' => __( 'Title', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'news_title_typography',
        'selector' => '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-title',
      ]
    );

    $this->add_responsive_control(
			'news_title_align',
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
					'{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-title' => 'text-align: {{VALUE}};',
				],
			]
		);
      
    $this->add_responsive_control(
      'news_title_margin_top',
      [
        'label' => esc_html__( 'Margin Top', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 30,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-title' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // NEWS EXCERPT
    // ================================================================

    $this->start_controls_section(
      'news_excerpt_style',
      [
        'label' => __( 'Excerpt', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_excerpt' => 'show',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'news_excerpt_typography',
        'selector' => '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-excerpt',
      ]
    );

    $this->add_responsive_control(
			'news_excerpt_align',
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
					'{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-excerpt' => 'text-align: {{VALUE}};',
				],
			]
		);
      
    $this->add_responsive_control(
      'news_excerpt_margin_top',
      [
        'label' => esc_html__( 'Margin Top', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 30,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .news-content .news-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // ================================================================
    // READ MORE
    // ================================================================

    $this->start_controls_section(
      'news_readmore_style',
      [
        'label' => __( 'Read More', EYEON_NAMESPACE ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'news_readmore_typography',
        'selector' => '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .readmore',
      ]
    );

    $this->add_responsive_control(
			'news_readmore_align',
			[
				'label' => __( 'Alignment', EYEON_NAMESPACE ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', EYEON_NAMESPACE ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .readmore' => 'align-self: {{VALUE}};',
				],
			]
		);
      
    $this->add_responsive_control(
      'news_readmore_margin_top',
      [
        'label' => esc_html__( 'Margin Top', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 30,
            'step' => 1,
          ],
        ],
        'size_units' => ['px'],
        'default' => [
          'unit' => 'px',
          'size' => 8,
        ],
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .news-list .news .news-item .news-details .readmore' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
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
        'default' => 'More News Coming Soon!',
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
          '{{WRAPPER}} .eyeon-news .no-items-found' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'no_results_found_typography',
        'selector' => '{{WRAPPER}} .eyeon-news .no-items-found',
      ]
    );

    $this->add_control(
      'no_results_found_color',
      [
        'label' => __( 'Text Color', EYEON_NAMESPACE ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .eyeon-news .no-items-found' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();

  }

}
