<?php
/**
 * Admin settings class
 */
class Glint_Search_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Glint Better WP Search',
            'Search Setting',
            'manage_options',
            'glint-search-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('glint_search_options', 'glint_search_post_types');
        register_setting('glint_search_options', 'glint_search_template');
        register_setting('glint_search_options', 'glint_search_results_per_page');

        add_settings_section(
            'glint_search_main',
            'Search Settings',
            array($this, 'settings_section_callback'),
            'glint_search_options'
        );

        add_settings_field(
            'glint_search_post_types',
            'Post Types to Include',
            array($this, 'post_types_field_callback'),
            'glint_search_options',
            'glint_search_main'
        );

        add_settings_field(
            'glint_search_template',
            'Search Results Template',
            array($this, 'template_field_callback'),
            'glint_search_options',
            'glint_search_main'
        );

        add_settings_field(
            'glint_search_results_per_page',
            'Results Per Page',
            array($this, 'results_per_page_field_callback'),
            'glint_search_options',
            'glint_search_main'
        );
    }

    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>Search Settings.</p>';
    }

    /**
     * Post types field callback
     */
    public function post_types_field_callback() {
        $post_types = get_option('glint_search_post_types', array('post', 'page'));
        if (!is_array($post_types)) {
            $post_types = array('post', 'page');
        }
        $all_post_types = get_post_types(array('public' => true), 'objects');

        foreach ($all_post_types as $type) {
            $checked = in_array($type->name, $post_types) ? 'checked' : '';
            echo '<label><input type="checkbox" name="glint_search_post_types[]" value="' . esc_attr($type->name) . '" ' . $checked . '> ' . esc_html($type->label) . '</label><br>';
        }
    }

    /**
     * Template field callback
     */
    public function template_field_callback() {
        $template = get_option('glint_search_template', 'search-results.php');
        echo '<input type="text" name="glint_search_template" value="' . esc_attr($template) . '" class="regular-text">';
        echo '<p class="description">Enter the filename of the search results template (e.g., search-results.php). Place this file in your child theme directory.</p>';
    }

    /**
     * Results per page field callback
     */
    public function results_per_page_field_callback() {
        $results_per_page = get_option('glint_search_results_per_page', 10);
        echo '<input type="number" name="glint_search_results_per_page" value="' . esc_attr($results_per_page) . '" class="small-text" min="1" max="100">';
        echo '<p class="description">Number of search results to display per page (1-100).</p>';
    }

    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Glint Better WP Search Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('glint_search_options');
                do_settings_sections('glint_search_options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
