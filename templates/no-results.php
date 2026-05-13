<?php
/**
 * No search results template for Glint Better WP Search
 * This template can be overridden by placing a file with the same name in your child theme.
 */

get_header(); ?>

<div class="glint-search-results no-result">
    <div class="container">
        <h1><?php printf(__('Search Results for: %s', 'glint-better-wp-search'), '<span>' . esc_html(get_search_query()) . '</span>'); ?></h1>
        <div class="no-results">
            <h2><?php _e('Nothing Found', 'glint-better-wp-search'); ?></h2>
            <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'glint-better-wp-search'); ?></p>
            <?php get_search_form(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>