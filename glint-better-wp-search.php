<?php
/**
 * Plugin Name: ST Search
 * Description: Enhanced WordPress search plugin that supports all post types, searches title and content, with customizable templates.
 * Version: 1.0.0
 * Author: kael
 * License: GPL v2 or later
 * Text Domain: glint-better-wp-search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GLINT_SEARCH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GLINT_SEARCH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once GLINT_SEARCH_PLUGIN_DIR . 'includes/class-glint-search.php';
require_once GLINT_SEARCH_PLUGIN_DIR . 'includes/class-glint-search-settings.php';

// Initialize the plugin
function glint_search_init() {
    $search = new Glint_Search();
    $settings = new Glint_Search_Settings();
}
add_action('plugins_loaded', 'glint_search_init');

// Activation hook
register_activation_hook(__FILE__, 'glint_search_activate');
function glint_search_activate() {
    // Set default options
    add_option('glint_search_post_types', array('post', 'page'));
    add_option('glint_search_template', 'search-results.php');
    add_option('glint_search_results_per_page', 10);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'glint_search_deactivate');
function glint_search_deactivate() {
    // Cleanup if needed
}

/**
 * Generate a simple search form
 *
 * @param array $args {
 *     Optional. Array of arguments.
 *
 *     @type string $placeholder Placeholder text for the search input. Default 'Search...'.
 *     @type string $button_text Text for the submit button. Default 'Search'.
 *     @type string $class CSS class for the form. Default 'glint-search-form'.
 *     @type bool $echo Whether to echo the form or return it. Default true.
 * }
 * @return string|void The search form HTML if $echo is false.
 */
function glint_search_form($args = array()) {
    $defaults = array(
        'placeholder' => __('Search...', 'glint-better-wp-search'),
        'button_text' => __('Search', 'glint-better-wp-search'),
        'class' => 'glint-search-form',
        'echo' => true,
    );

    $args = wp_parse_args($args, $defaults);

    $form = '<form role="search" method="get" class="' . esc_attr($args['class']) . '" action="' . esc_url(home_url('/')) . '">
        <label>
            <input type="search" class="search-field" placeholder="' . esc_attr($args['placeholder']) . '" value="' . get_search_query() . '" name="s" />
        </label>
        <input type="submit" class="search-submit" value="' . esc_attr($args['button_text']) . '" />
    </form>';

    if ($args['echo']) {
        echo $form;
    } else {
        return $form;
    }
}
