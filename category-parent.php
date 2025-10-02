<?php

/**
 * The template for displaying Parent Category pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
get_header();

$category = get_queried_object();
$parent_id = $category->term_id;

// Get child categories
$child_categories = get_categories([
    'parent' => $parent_id,
    'hide_empty' => true
]);
$main_posts = [];
?>

<div class="bb-category-main-wrapper">
    <!-- Main Category and Subcategories -->
    <section class="bb-main-category">
        <h1 class="bb-main-category-title"><?php echo esc_html($category->name); ?></h1>
        <nav class="bb-main-sub-category-nav">
            <ul>
                <?php foreach ($child_categories as $child_cat): ?>
                    <li><a href="<?php echo get_category_link($child_cat->term_id); ?>"><?php echo esc_html($child_cat->name); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </section>

    <!-- Fetch latest 2 posts for parent category -->
    <div class="bb-category-main-grid">
        <div class="main-side">
            <section class="bb-article-category">
                <?php
                $main_query = new WP_Query([
                    'cat' => $parent_id,
                    'posts_per_page' => 2
                ]);
                while ($main_query->have_posts()): $main_query->the_post();
                    $main_posts[] = get_the_ID();

                    $post_id = get_the_ID();
                    $likes_count = wp_ulike_get_post_likes($post_id);
                    $user_id = get_current_user_id();
                    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                    $is_bookmarked = in_array($post_id, $saved_articles);
                    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                ?>
                    <article class="bb-article-item" data-post-id="<?php echo get_the_ID(); ?>">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()): ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                                <?php if (is_user_logged_in()) { ?>
                                    <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                        <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                    </button>
                                <?php } ?>
                            <?php endif; ?>
                            <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                            <h2><?php the_title(); ?></h2>
                        </a>
                    </article>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </section>

            <!-- Fetch 6 more recent posts -->
            <section class="bb-article-grid-section">
                <div class="bb-article-grid">
                    <?php
                    $extra_posts = new WP_Query([
                        'cat' => $parent_id,
                        'posts_per_page' => 6,
                        'offset' => 2
                    ]);
                    while ($extra_posts->have_posts()): $extra_posts->the_post();
                        $main_posts[] = get_the_ID();

                        $post_id = get_the_ID();
                        $likes_count = wp_ulike_get_post_likes($post_id);
                        $user_id = get_current_user_id();
                        $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                        $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                        $is_bookmarked = in_array($post_id, $saved_articles);
                        $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                        $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                    ?>
                        <a href="<?php the_permalink(); ?>" class="bb-article-item small" data-post-id="<?php echo get_the_ID(); ?>">
                            <?php if (has_post_thumbnail()): ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                                <?php if (is_user_logged_in()) { ?>
                                    <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                        <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                    </button>
                                <?php } ?>
                            <?php endif; ?>
                            <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                            <h3><?php the_title(); ?></h3>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Subcategories Section -->
<section class="bb-category-bottom-section">
    <?php foreach ($child_categories as $child_cat): ?>
        <section class="bb-category-bottom-article-main-section">
            <h2 class="bottom-article-main-section-title">
                <a href="<?php echo get_category_link($child_cat->term_id); ?>"><?php echo esc_html($child_cat->name); ?></a>
            </h2>
            <div class="bb-category-bottom-article-grid">
                <?php
                $subcat_posts = new WP_Query([
                    'cat' => $child_cat->term_id,
                    'posts_per_page' => 4
                ]);
                while ($subcat_posts->have_posts()): $subcat_posts->the_post();
                    $main_posts[] = get_the_ID();

                    $post_id = get_the_ID();
                    $likes_count = wp_ulike_get_post_likes($post_id);
                    $user_id = get_current_user_id();
                    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                    $is_bookmarked = in_array($post_id, $saved_articles);
                    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                ?>
                    <a href="<?php the_permalink(); ?>" class="bb-article-item small" data-post-id="<?php echo get_the_ID(); ?>">
                        <?php if (has_post_thumbnail()): ?>
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                            <?php if (is_user_logged_in()) { ?>
                                <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                    <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                </button>
                            <?php } ?>
                        <?php endif; ?>
                        <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                        <h3><?php the_title(); ?></h3>
                    </a>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        </section>
    <?php endforeach; ?>
</section>

<?php
$posts_per_page = 3; // Initial posts to display
$more_news_query = new WP_Query([
    'category__in' => array_merge([$parent_id], wp_list_pluck($child_categories, 'term_id')),
    'posts_per_page' => $posts_per_page,
    'offset' => 0,
    'post_status' => 'publish',
    'post__not_in' => $main_posts
]);

if ($more_news_query->have_posts()) {
?>
    <section class="bb-category-more-industrie-section">
        <div class="more-industrie-main">
            <h2 class="industrie-title">More <?php echo esc_html($category->name); ?> News</h2>

            <div id="industrie-news-container">
                <?php
                while ($more_news_query->have_posts()): $more_news_query->the_post();
                    $post_id = get_the_ID();
                    $likes_count = wp_ulike_get_post_likes($post_id);
                    $user_id = get_current_user_id();
                    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                    $is_bookmarked = in_array($post_id, $saved_articles);
                    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                    $button_text = $is_bookmarked ? 'Saved Article' : 'Bookmark This Article';
                    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                ?>

                    <div class="industrie-news-card" data-post-id="<?php echo get_the_ID(); ?>">
                        <div>
                            <p><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></p>
                            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        </div>
                        <?php if (has_post_thumbnail()): ?>
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
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
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>

            <div class="industrie-news-load-more-btn">
                <button class="load-more-btn" id="loadMoreBtn" data-offset="<?php echo $posts_per_page; ?>" data-category-id="<?php echo $parent_id; ?>">Load More</button>
            </div>
        </div>
    </section>
<?php
}
get_footer(); ?>