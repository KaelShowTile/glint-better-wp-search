<?php
/**
 * Default search results template for Glint Better WP Search
 * This template can be overridden by placing a file with the same name in your child theme.
 */

get_header(); ?>

<div class="glint-search-results">
    <div class="container">
        <h1><?php printf(__('Search Results for: %s', 'glint-better-wp-search'), '<span>' . get_search_query() . '</span>'); ?></h1>

        <?php
        // Exact Repeater Matches (Feature #3)
        $exact_matches = array();
        // Only fetch and display exact matches if we are on the first page of search results
        if (!is_paged() && class_exists('Glint_Search')) {
            $exact_matches = Glint_Search::get_exact_repeater_matches(get_query_var('s'));
        }
        ?>

        <?php if (!empty($exact_matches)) : ?>
            <div class="search-results exact-matches-section">
                <?php foreach ($exact_matches as $match){

                    $tile_finish = null; 
                    $tile_code = null;
                    $tile_size = null;
                    $tile_size_check_string = "tile_size_name";
                    $tile_link = esc_url($match['permalink']);
                    $tile_img = null;

                    foreach ($match['subfields'] as $sf_key => $sf_value) {
                        if ($sf_key == "finish_name"){
                            $tile_finish = esc_html($sf_value);
                        }elseif($sf_key == "product_code"){
                            $tile_code = esc_html($sf_value);
                            $tile_link = esc_url($match['permalink']) . "/#" . $tile_code;
                        }elseif($sf_key == "finish_image"){
                            $tile_img = wp_get_attachment_image($sf_value, 'medium');
                        }elseif(str_contains($sf_key, $tile_size_check_string)){ 
                            if($tile_size){
                                $tile_size .= " ," . esc_html($sf_value);
                            }else{
                                $tile_size .= esc_html($sf_value);
                            }  
                        }
                    }?>

                    <article class="exact-result-item">
                        <div class="item-thumbnail">
                            <a href="<?php echo $tile_link; ?>">
                                <?php echo $tile_img ? $tile_img : '<div style="width:100px;height:100px;background:#ccc;"></div>'; ?>
                            </a>
                        </div>
                        <div class="item-details">
                            <h2><a href="<?php echo $tile_link;?>"><?php echo esc_html($match['post_title']); ?></a></h2>
                            
                            <ul class="item-meta">
                                <?php if($tile_finish) : ?>
                                    <li>Finish: <?php echo $tile_finish; ?></li>
                                <?php endif; ?>
                                <?php if($tile_code) : ?>
                                    <li>Code: <?php echo $tile_code; ?></li>
                                <?php endif; ?>
                                <?php if($tile_size) : ?>
                                    <li>Size: <?php echo $tile_size; ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </article>

                <?php } ?>
            </div>
        <?php endif; ?>

        <?php if (have_posts()) : ?>

            <div class="search-results">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?> class="search-results article">
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
                            <?php //the_excerpt(); ?>
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
