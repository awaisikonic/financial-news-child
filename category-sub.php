<?php

/**
 * The template for displaying Sub Category pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */
get_header();
$category = get_queried_object();
$parent_id = $category->term_id;
?>
<div class="sub-category-main-wrapper">
    <section class="sub-category__featured-section">
        <h1 class="sub-category__title"><?php echo esc_html($category->name); ?></h1>
        <!-- More Industries news   -->
        <div class="more-industrie-main">
            <?php
            if (have_posts()) {
                // Start the loop.
                while (have_posts()) {
                    the_post();

                    $post_id = get_the_ID();
                    $likes_count = wp_ulike_get_post_likes($post_id);
                    $user_id = get_current_user_id();
                    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                    $is_bookmarked = in_array($post_id, $saved_articles);
                    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                    $button_text = $is_bookmarked ? 'Saved Article' : 'Bookmark This Article';
                    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';

                    /*
                    * Include the Post-Format-specific template for the content.
                    * If you want to override this in a child theme, then include a file
                    * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                    */
            ?>
                    <div class="industrie-news-card">
                        <div>
                            <p><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></p>
                            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        </div>
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()): ?>
                                <img src="<?php the_post_thumbnail_url('full'); ?>" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </a>
                        <div class="post-bottom-wraper">
                            <?php if (is_user_logged_in()) { ?>
                                <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                    <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                    <span class="bookmark-text"><?php echo $button_text; ?></span>
                                </button>
                            <?php } ?>
                            <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                        </div>
                    </div>
            <?php
                }
            } else {
                get_template_part('loop-templates/content', 'none');
            }
            understrap_pagination();
            ?>
        </div>
    </section>
</div>

<?php get_footer(); ?>