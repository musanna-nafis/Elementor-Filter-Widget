<?php
/**
 * Plugin Name:       AF Elementor Filter Widget
 * Description:       A custom Elementor widget that provides a dual AJAX filtering system for events with full styling controls.
 * Version:           2.1.0
 * Author:            Hasan Al Musanna
 * Text Domain:       af-elementor-widget
 */

if (!defined('WPINC')) die;

/**
 * Main Plugin Class
 */
final class AF_Elementor_Filter_Plugin {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        // Check if Elementor is loaded before doing anything
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Register the widget
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        
        // Include other necessary files
        $this->includes();
    }

    public function register_widgets($widgets_manager) {
        require_once(__DIR__ . '/widgets/event-filter-widget.php');
        $widgets_manager->register(new \AF_Event_Filter_Widget());
    }

    public function includes() {
        require_once(__DIR__ . '/inc/ajax-handler.php');
    }
}

// Run the plugin
AF_Elementor_Filter_Plugin::instance();