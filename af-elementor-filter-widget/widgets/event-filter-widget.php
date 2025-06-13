<?php
// widgets/event-filter-widget.php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager; // আইকন ম্যানেজারের জন্য use করতে হবে

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AF_Event_Filter_Widget extends Widget_Base {

    public function get_name() {
        return 'af_event_filter_widget';
    }

    public function get_title() {
        return __('AF Event Filter System', 'af-elementor-widget');
    }

    public function get_icon() {
        return 'eicon-filter';
    }

    public function get_categories() {
        return ['general'];
    }

    // =================================================================
    // ============== পরিবর্তন শুরু: _register_controls() ==============
    // =================================================================
    protected function _register_controls() {
        
        // --- Content Tab ---
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Filter Settings', 'af-elementor-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Events Per Page', 'af-elementor-widget'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 9,
                'description' => 'Set how many events to show per page.',
            ]
        );

        $this->add_control(
            'default_event_status',
            [
                'label' => __('Default Event Status', 'af-elementor-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'past',
                'options' => [
                    'past'      => __('Past Events', 'af-elementor-widget'),
                    'upcoming'  => __('Upcoming Events', 'af-elementor-widget'),
                    'ongoing'   => __('Ongoing Events', 'af-elementor-widget'),
                ],
            ]
        );
        $this->end_controls_section();

        // =====================================================================
        // --- Style Tab: সম্পূর্ণ নতুন করে সাজানো হয়েছে ---
        // =====================================================================

        // Section: Total Events Counter
        $this->start_controls_section(
            'style_section_counter',
            [
                'label' => __('Total Events Counter', 'af-elementor-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('counter_text_color', [
            'label' => __('Text Color', 'af-elementor-widget'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .total-events-counter' => 'color: {{VALUE}};',
                '{{WRAPPER}} .total-events-counter strong' => 'color: {{VALUE}};'
            ],
        ]);
        
        $this->add_control('counter_count_color', [
            'label' => __('Count Color', 'af-elementor-widget'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .total-events-counter span' => 'color: {{VALUE}};'],
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'counter_typography',
                'label' => __('Typography', 'af-elementor-widget'),
                'selector' => '{{WRAPPER}} .total-events-counter',
            ]
        );

        $this->end_controls_section();


        // Section: Filter Bar
        $this->start_controls_section(
            'style_section_filter_bar',
            [
                'label' => __('Filter Bar', 'af-elementor-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('bar_bg_color', [
            'label' => 'Bar Background Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .modern-filter-bar' => 'background-color: {{VALUE}};']
        ]);

        $this->add_control('bar_separator_color', [
            'label' => 'Separator Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .modern-filter-bar .filter-item' => 'border-color: {{VALUE}};']
        ]);

        $this->add_control('bar_text_heading', [
            'label' => __('Input Text', 'af-elementor-widget'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('bar_text_color', [
            'label' => 'Text Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .modern-filter-bar input, {{WRAPPER}} .modern-filter-bar select' => 'color: {{VALUE}};']
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'bar_text_typography',
                'label' => __('Typography', 'af-elementor-widget'),
                'selector' => '{{WRAPPER}} .modern-filter-bar input, {{WRAPPER}} .modern-filter-bar select',
            ]
        );

        $this->end_controls_section();


        // Section: Icons
        $this->start_controls_section(
            'style_section_icons',
            [
                'label' => __('Icons', 'af-elementor-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('field_icons_heading', ['label' => __('Field Icons', 'af-elementor-widget'), 'type' => Controls_Manager::HEADING]);
        
        $this->add_control('search_field_icon', [
            'label' => __('Search Field Icon', 'af-elementor-widget'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-search', 'library' => 'solid'],
        ]);

        $this->add_control('location_field_icon', [
            'label' => __('Location Field Icon', 'af-elementor-widget'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-map-marker-alt', 'library' => 'solid'],
        ]);

        $this->add_control('tag_field_icon', [
            'label' => __('Tag Field Icon', 'af-elementor-widget'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-tag', 'library' => 'solid'],
        ]);

        $this->add_control('date_field_icon', [
            'label' => __('Date Field Icon', 'af-elementor-widget'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-calendar-alt', 'library' => 'solid'],
        ]);

        $this->add_control('field_icon_color', [
            'label' => 'Field Icon Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .filter-icon' => 'color: {{VALUE}};']
        ]);
        
        $this->add_control('submit_button_heading', ['label' => __('Submit Button', 'af-elementor-widget'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        
        $this->add_control('submit_button_icon', [
            'label' => __('Submit Button Icon', 'af-elementor-widget'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-search', 'library' => 'solid'],
        ]);

        $this->add_control('submit_button_bg_color', [
            'label' => 'Button Background',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .search-submit-button' => 'background-color: {{VALUE}};']
        ]);


        $this->add_control('submit_button_icon_color', [
            'label' => 'Button Icon Color',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .search-submit-button i' => 'color: {{VALUE}} !important;',
            ],
        ]);
       
        
        $this->end_controls_section();


        // ==============Event Card Style Section ==============
        $this->start_controls_section(
            'style_section_event_card',
            [
                'label' => __('Event Card', 'af-elementor-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('card_bg_color', [
            'label' => 'Card Background Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .etn-event-item' => 'background-color: {{VALUE}};']
        ]);
        
        $this->add_control('card_title_heading', [
            'label' => __('Title', 'af-elementor-widget'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('card_title_color', [
            'label' => 'Title Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .etn-event-title a' => 'color: {{VALUE}};']
        ]);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_title_typography',
                'label' => 'Title Typography',
                'selector' => '{{WRAPPER}} .etn-event-title'
            ]
        );

        $this->add_control('card_meta_heading', [
            'label' => __('Meta Info (Date/Location)', 'af-elementor-widget'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('card_meta_color', [
            'label' => 'Meta Text Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .etn-meta-info' => 'color: {{VALUE}};']
        ]);
        $this->add_control('card_meta_icon_color', [
            'label' => 'Meta Icon Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .etn-meta-info i' => 'color: {{VALUE}};']
        ]);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_meta_typography',
                'label' => 'Meta Typography',
                'selector' => '{{WRAPPER}} .etn-meta-info'
            ]
        );
        
        $this->add_control('card_organizer_heading', [
            'label' => __('Organizer Text', 'af-elementor-widget'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('organizer_label_color', [
            'label' => 'Label Color ("Organized By")',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .etn-organizer .organizer-label' => 'color: {{VALUE}};']
        ]);
        $this->add_control('organizer_name_color', [
            'label' => 'Vendor Name Color',
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .etn-organizer .organizer-name' => 'color: {{VALUE}};']
        ]);

        $this->add_control('card_button_heading', [
            'label' => __('Buy Button', 'af-elementor-widget'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_button_typography',
                'label' => 'Button Typography',
                'selector' => '{{WRAPPER}} .etn-btn'
            ]
        );
        $this->start_controls_tabs('card_button_style_tabs');
        $this->start_controls_tab('card_button_normal', ['label' => __('Normal', 'af-elementor-widget')]);
        $this->add_control('card_button_text_color', ['label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .etn-buy-btn .etn-btn' => 'color: {{VALUE}};']]);
        $this->add_control('card_button_border_color', ['label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .etn-buy-btn .etn-btn' => 'border-color: {{VALUE}};']]);
        $this->add_control('card_button_bg_color', ['label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .etn-buy-btn .etn-btn' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('card_button_hover', ['label' => __('Hover', 'af-elementor-widget')]);
        $this->add_control('card_button_hover_text_color', ['label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .etn-buy-btn .etn-btn:hover' => 'color: {{VALUE}};']]);
        $this->add_control('card_button_hover_border_color', ['label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .etn-buy-btn .etn-btn:hover' => 'border-color: {{VALUE}};']]);
        $this->add_control('card_button_hover_bg_color', ['label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .etn-buy-btn .etn-btn:hover' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();







        // Section: Pagination
        $this->start_controls_section(
            'style_section_pagination',
            [
                'label' => __('Pagination', 'af-elementor-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'label' => __('Typography', 'af-elementor-widget'),
                'selector' => '{{WRAPPER}} .pagination-wrapper .page-numbers',
            ]
        );

        $this->start_controls_tabs('pagination_style_tabs');

        // Tab: Normal
        $this->start_controls_tab('pagination_normal', ['label' => __('Normal', 'af-elementor-widget')]);
        $this->add_control('pagination_link_color', ['label' => 'Link Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .pagination-wrapper .page-numbers' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_bg_color', ['label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .pagination-wrapper .page-numbers' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();

        // Tab: Hover
        $this->start_controls_tab('pagination_hover', ['label' => __('Hover', 'af-elementor-widget')]);
        $this->add_control('pagination_hover_link_color', ['label' => 'Link Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .pagination-wrapper .page-numbers:hover' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_hover_bg_color', ['label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .pagination-wrapper .page-numbers:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}}' ]]);
        $this->end_controls_tab();
        
        // Tab: Active
        $this->start_controls_tab('pagination_active', ['label' => __('Active', 'af-elementor-widget')]);
        $this->add_control('pagination_active_link_color', ['label' => 'Link Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .pagination-wrapper .page-numbers.current' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_active_bg_color', ['label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .pagination-wrapper .page-numbers.current' => 'background-color: {{VALUE}}; border-color: {{VALUE}}']]);
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();
    }


    // ===========================================================
    // ============== render() ==============
    // ===========================================================
    protected function render() {
        $settings = $this->get_settings_for_display();
        $posts_per_page = $settings['posts_per_page'];
        $default_status = $settings['default_event_status'];
        ?>
        <div class="af-elementor-widget-container">
            <div id="af-event-filter-container">
                <div class="top-bar-wrapper">
                    <div class="total-events-counter"><strong>Total Events:</strong> <span id="event-count">...</span></div>
                </div>
                
                <form id="af-main-filter-form" class="modern-filter-bar" 
                      data-per-page="<?php echo esc_attr($posts_per_page); ?>" 
                      data-default-status="<?php echo esc_attr($default_status); ?>">

                    <div class="filter-item">
                        <span class="filter-icon">
                            <?php Icons_Manager::render_icon($settings['search_field_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                        <input type="search" placeholder="Find your next event" name="s" />
                    </div>

                    <div class="filter-item">
                        <span class="filter-icon">
                            <?php Icons_Manager::render_icon($settings['location_field_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                        <div class="select-wrapper">
                            <select name="event_location">
                                <option value="">Event Location</option>
                                <?php global $wpdb; $locations = $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'event_location' ORDER BY meta_value ASC"); if ($locations) foreach ($locations as $location) echo '<option value="' . esc_attr($location) . '">' . esc_html($location) . '</option>'; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-item">
                        <span class="filter-icon">
                            <?php Icons_Manager::render_icon($settings['tag_field_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                        <div class="select-wrapper">
                            <select name="event_tag">
                                <option value="">Event Tag</option>
                                <?php global $wpdb; $tags = $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'event_tag' AND meta_value != '' ORDER BY meta_value ASC"); if ($tags) foreach ($tags as $tag) echo '<option value="' . esc_attr($tag) . '">' . esc_html($tag) . '</option>'; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-item">
                        <span class="filter-icon">
                            <?php Icons_Manager::render_icon($settings['date_field_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                        <input type="month" name="event_date" title="Select month and year" class="date-input">
                    </div>

                    <div class="filter-item submit-item">
                        <button type="submit" class="search-submit-button" aria-label="Search">
                            <?php Icons_Manager::render_icon($settings['submit_button_icon'], ['aria-hidden' => 'true']); ?>
                        </button>
                    </div>

                    <?php wp_nonce_field('af_complex_filter_nonce', 'security'); ?>
                </form>
                
                <div id="af-event-results-loader" class="loader" style="display:none;">Loading...</div>
                <div id="af-event-results-container"></div>
                <div id="af-event-pagination-container" class="pagination-wrapper"></div>
            </div>
        </div>
        <?php
    }
}