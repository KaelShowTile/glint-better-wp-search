<?php
if (!defined('ABSPATH')) {
    exit;
}

class Glint_Search {

    public function __construct() {
        add_action('pre_get_posts', array($this, 'include_post_types'));
        add_filter('posts_join', array($this, 'search_join'), 10, 2);
        add_filter('posts_search', array($this, 'search_where'), 10, 2);
        add_filter('posts_distinct', array($this, 'search_distinct'), 10, 2);
        add_filter('template_include', array($this, 'load_search_template'));
    }

    public function include_post_types($query) {
        if ($query->is_main_query() && $query->is_search() && !is_admin()) {
            $post_types = get_option('glint_search_post_types', array('post', 'page'));
            $query->set('post_type', $post_types);

            $results_per_page = get_option('glint_search_results_per_page', 10);
            $query->set('posts_per_page', $results_per_page);
        }
    }

    public function load_search_template($template) {
        if (is_search()) {
            $custom_template = get_option('glint_search_template', 'search-results.php');
            
            // 1. Check if the active theme has an override
            $theme_template = locate_template(array($custom_template));
            if ($theme_template) {
                return $theme_template;
            }
            
            // 2. Check if it exists in the plugin's template directory
            $plugin_template = GLINT_SEARCH_PLUGIN_DIR . 'templates/' . $custom_template;
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
            
            // 3. Fallback to default search-results.php if the input file isn't found
            return GLINT_SEARCH_PLUGIN_DIR . 'templates/search-results.php';
        }
        return $template;
    }

    public function search_join($join, $query) {
        global $wpdb;
        if ($query->is_main_query() && $query->is_search() && !is_admin()) {
            $join .= " LEFT JOIN {$wpdb->postmeta} glint_pm ON ({$wpdb->posts}.ID = glint_pm.post_id) ";
        }
        return $join;
    }

    public function search_where($where, $query) {
        global $wpdb;
        if ($query->is_main_query() && $query->is_search() && !is_admin()) {
            $search_term = $query->query_vars['s'];
            if (empty($search_term)) return $where;

            $selected_acf = get_option('glint_search_acf_fields', array());
            $post_types = get_option('glint_search_post_types', array('post', 'page'));

            $allowed_keys = array();
            foreach ($post_types as $pt) {
                if (isset($selected_acf[$pt])) {
                    $allowed_keys = array_merge($allowed_keys, $selected_acf[$pt]);
                }
            }
            $allowed_keys = array_unique($allowed_keys);

            if (empty($allowed_keys)) {
                return $where;
            }

            $meta_key_conditions = array();
            foreach ($allowed_keys as $key) {
                if (strpos($key, '%') !== false) {
                    $meta_key_conditions[] = $wpdb->prepare("glint_pm.meta_key LIKE %s", $key);
                } else {
                    $meta_key_conditions[] = $wpdb->prepare("glint_pm.meta_key = %s", $key);
                }
            }

            $meta_key_sql = implode(' OR ', $meta_key_conditions);

            // Inject ACF Meta logic into the WordPress generated search term bracket.
            // This elegantly allows for multiple search words natively supported by WP.
            $where = preg_replace(
                "/(\(\s*{$wpdb->posts}\.post_title\s+LIKE\s*('[^']+')\s*\))/",
                "$1 OR ( ($meta_key_sql) AND glint_pm.meta_value LIKE $2 )",
                $where
            );
        }
        return $where;
    }

    public function search_distinct($distinct, $query) {
        if ($query->is_main_query() && $query->is_search() && !is_admin()) {
            return "DISTINCT";
        }
        return $distinct;
    }

    /**
     * Extracts specific exact-matches isolated into repeater data cards
     */
    public static function get_exact_repeater_matches($search_term) {
        global $wpdb;
        
        $enable_exact = get_option('glint_search_enable_exact_repeater', 'no');
        if ($enable_exact !== 'yes') return array();

        $selected_acf = get_option('glint_search_acf_fields', array());
        $repeater_keys = array();
        
        foreach ($selected_acf as $pt => $keys) {
            foreach ($keys as $key) {
                if (strpos($key, '%') !== false) {
                    $repeater_keys[] = $key;
                }
            }
        }
        $repeater_keys = array_unique($repeater_keys);
        if (empty($repeater_keys)) return array();

        $key_conditions = array();
        foreach ($repeater_keys as $key) {
            $key_conditions[] = $wpdb->prepare("meta_key LIKE %s", $key);
        }
        $key_sql = implode(' OR ', $key_conditions);

        $sql = $wpdb->prepare(
            "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE ($key_sql) AND meta_value = %s",
            $search_term
        );
        
        $matches = $wpdb->get_results($sql);
        $results = array();

        foreach ($matches as $match) {
            $post_id = $match->post_id;
            $meta_key = $match->meta_key;
            
            // E.g. meta_key "spec_0_size" -> Repeater Name "spec", Row "0", Field "size"
            if (preg_match('/^(.+?)_([0-9]+)_(.+)$/', $meta_key, $regex_matches)) {
                $repeater_name = $regex_matches[1];
                $row_index = $regex_matches[2];
                
                $row_sql = $wpdb->prepare(
                    "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
                    $post_id,
                    $wpdb->esc_like("{$repeater_name}_{$row_index}_") . '%'
                );
                $row_data = $wpdb->get_results($row_sql);
                
                $subfields = array();
                foreach ($row_data as $rd) {
                    $subfield_name = str_replace("{$repeater_name}_{$row_index}_", "", $rd->meta_key);
                    if (strpos($subfield_name, '_') === 0) continue; // Skip hidden ACF references
                    
                    $subfields[$subfield_name] = $rd->meta_value;
                }

                $unique_key = $post_id . '_' . $repeater_name . '_' . $row_index;
                if (!isset($results[$unique_key])) {
                    $results[$unique_key] = array(
                        'post_id' => $post_id,
                        'post_title' => get_the_title($post_id),
                        'thumbnail' => get_the_post_thumbnail($post_id, 'medium'),
                        'permalink' => get_permalink($post_id),
                        'subfields' => $subfields,
                        'matched_key' => $meta_key
                    );
                }
            }
        }
        
        return array_values($results);
    }
}