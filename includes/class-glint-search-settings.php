<?php
if (!defined('ABSPATH')) {
    exit;
}

class Glint_Search_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_options_page(
            __('ST Search Settings', 'glint-better-wp-search'),
            __('ST Search', 'glint-better-wp-search'),
            'manage_options',
            'glint-search-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('glint_search_settings_group', 'glint_search_post_types');
        register_setting('glint_search_settings_group', 'glint_search_enable_exact_repeater');
        register_setting('glint_search_settings_group', 'glint_search_acf_fields');
        register_setting('glint_search_settings_group', 'glint_search_template');
        register_setting('glint_search_settings_group', 'glint_search_results_per_page');
    }

    public function render_settings_page() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $selected_post_types = get_option('glint_search_post_types', array('post', 'page'));
        $enable_exact = get_option('glint_search_enable_exact_repeater', 'no');
        $selected_acf = get_option('glint_search_acf_fields', array());
        $template = get_option('glint_search_template', 'search-results.php');
        $results_per_page = get_option('glint_search_results_per_page', 10);

        ?>
        <div class="wrap">
<<<<<<< HEAD
            <h1><?php _e('ST Search Settings', 'glint-better-wp-search'); ?></h1>
=======
            <h1>ST Search Settings</h1>
>>>>>>> 4a36391d8c35d805ba75eee0e2e2992a9f78859f
            <form method="post" action="options.php">
                <?php settings_fields('glint_search_settings_group'); ?>
                
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Post Types to Search', 'glint-better-wp-search'); ?></th>
                        <td>
                            <?php foreach ($post_types as $pt) : ?>
                                <label>
                                    <input type="checkbox" name="glint_search_post_types[]" value="<?php echo esc_attr($pt->name); ?>" <?php checked(in_array($pt->name, $selected_post_types)); ?> />
                                    <?php echo esc_html($pt->label); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php _e('Search Results Template', 'glint-better-wp-search'); ?></th>
                        <td>
                            <input type="text" name="glint_search_template" value="<?php echo esc_attr($template); ?>" class="regular-text" />
                            <p class="description"><?php _e('Filename of your custom search results template in your child theme (e.g., search-results.php).', 'glint-better-wp-search'); ?></p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php _e('Results Per Page', 'glint-better-wp-search'); ?></th>
                        <td>
                            <input type="number" name="glint_search_results_per_page" value="<?php echo esc_attr($results_per_page); ?>" class="small-text" />
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php _e('Enable Exact Repeater Match', 'glint-better-wp-search'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="glint_search_enable_exact_repeater" value="yes" <?php checked($enable_exact, 'yes'); ?> />
                                <?php _e('Display exact matches for ACF repeater sub-fields as separate items above normal search results.', 'glint-better-wp-search'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('ACF Fields Mapping', 'glint-better-wp-search'); ?></h2>
                <p><?php _e('Select which ACF fields should be included in the search for each post type.', 'glint-better-wp-search'); ?></p>
                
                <?php if (function_exists('acf_get_field_groups')) : ?>
                    <?php foreach ($selected_post_types as $pt_name) : 
                        $pt_obj = get_post_type_object($pt_name);
                        if (!$pt_obj) continue;
                    ?>
                        <h3><?php echo esc_html($pt_obj->label); ?></h3>
                        <div style="background: #fff; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px;">
                            <?php
                            $groups = acf_get_field_groups(array('post_type' => $pt_name));
                            if (empty($groups)) {
                                echo '<p>' . __('No ACF field groups found for this post type.', 'glint-better-wp-search') . '</p>';
                            } else {
                                foreach ($groups as $group) {
                                    echo '<h4>' . esc_html($group['title']) . '</h4>';
                                    $fields = acf_get_fields($group['key']);
                                    if ($fields) {
                                        $this->render_acf_fields_checkboxes($fields, $pt_name, $selected_acf);
                                    }
                                }
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="color: red;"><?php _e('Advanced Custom Fields (ACF) is not active. Please install and activate ACF to use this feature.', 'glint-better-wp-search'); ?></p>
                <?php endif; ?>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    private function render_acf_fields_checkboxes($fields, $post_type, $selected, $prefix = '') {
        if (!$fields) return;
        foreach ($fields as $field) {
            $field_key = isset($field['name']) ? $field['name'] : ''; 
            if (!$field_key) continue;

            $full_field_path = $prefix ? $prefix . '_' . $field_key : $field_key;
            $is_checked = isset($selected[$post_type]) && in_array($full_field_path, (array)$selected[$post_type]);

            echo '<label style="display:block; margin-left: ' . ($prefix ? '20px' : '0') . ';">';
            echo '<input type="checkbox" name="glint_search_acf_fields[' . esc_attr($post_type) . '][]" value="' . esc_attr($full_field_path) . '" ' . checked($is_checked, true, false) . ' /> ';
            echo esc_html($field['label']) . ' (' . esc_html($field['type']) . ')';
            echo '</label>';

            // If this is a repeater, we append the wildcard `%` for its sub-fields index so we can catch all row numbers
            if (($field['type'] === 'repeater' || $field['type'] === 'group') && isset($field['sub_fields'])) {
                $new_prefix = $field['type'] === 'repeater' ? $full_field_path . '_%' : $full_field_path;
                $this->render_acf_fields_checkboxes($field['sub_fields'], $post_type, $selected, $new_prefix);
            }
        }
    }
}