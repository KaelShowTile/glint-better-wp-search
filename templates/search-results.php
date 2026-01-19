<?php
/**
 * Default search results template for Glint Better WP Search
 * This template can be overridden by placing a file with the same name in your child theme.
 */

get_header(); ?>

<div class="glint-search-results">
    <div class="container">
        <h1><?php printf(__('Search Results for: %s', 'glint-better-wp-search'), '<span>' . get_search_query() . '</span>'); ?></h1>

        <?php if (have_posts()) : ?>
            <div class="search-results-count">
                <?php
                global $wp_query;
                $total_results = $wp_query->found_posts;
                printf(_n('%d result found', '%d results found', $total_results, 'glint-better-wp-search'), $total_results);
                ?>
            </div>

            <div class="search-results">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                        <header class="entry-header">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php the_post_thumbnail( 'medium' ); ?>
                            </a>
                            <span class="post-type"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                            </h2>
                        </header>

                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => __('Previous', 'glint-better-wp-search'),
                    'next_text' => __('Next', 'glint-better-wp-search'),
                ));
                ?>
            </div>

        <?php else : ?>
            <div class="no-results">
                <h2><?php _e('Nothing Found', 'glint-better-wp-search'); ?></h2>
                <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'glint-better-wp-search'); ?></p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
