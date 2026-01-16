<?php
/**
 * Main search functionality class
 */
class Glint_Search {

    public function __construct() {
        add_action('pre_get_posts', array($this, 'modify_search_query'));
        add_filter('template_include', array($this, 'load_search_template'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    /**
     * Modify the search query to include all selected post types and search title/content
     */
    public function modify_search_query($query) {
        if (!is_admin() && $query->is_main_query() && $query->is_search()) {
            // Get selected post types from settings
            $post_types = get_option('glint_search_post_types', array('post', 'page'));
            $query->set('post_type', $post_types);

            // Set results per page
            $results_per_page = get_option('glint_search_results_per_page', 10);
            $query->set('posts_per_page', intval($results_per_page));

            // Ensure searching in title and content
            $search_term = $query->get('s');
            if (!empty($search_term)) {
                $query->set('s', $search_term);
            }
        }
    }

    /**
     * Load the custom search template
     */
    public function load_search_template($template) {
        if (is_search()) {
            $custom_template = get_option('glint_search_template', 'search-results.php');

            // Check if template exists in child theme
            $child_theme_template = get_stylesheet_directory() . '/' . $custom_template;
            if (file_exists($child_theme_template)) {
                return $child_theme_template;
            }

            // Check if template exists in parent theme
            $parent_theme_template = get_template_directory() . '/' . $custom_template;
            if (file_exists($parent_theme_template)) {
                return $parent_theme_template;
            }

            // Use default template from plugin
            $plugin_template = GLINT_SEARCH_PLUGIN_DIR . 'templates/' . $custom_template;
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        return $template;
    }

    /**
     * Enqueue plugin styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'glint-search-styles',
            GLINT_SEARCH_PLUGIN_URL . 'assets/css/glint-search.css',
            array(),
            '1.0.0'
        );
    }
}
