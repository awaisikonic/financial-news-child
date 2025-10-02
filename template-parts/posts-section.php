<?php
$select_category = get_sub_field('select_category');
$post_count = get_sub_field('post_count');

if ($select_category) {
    // Get parent category
    $parent_category = get_term($select_category, 'category');

    // Get child categories
    $child_categories = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => true,
        'parent'     => $select_category,
    ]);

    // Prepare category IDs (parent + children)
    $category_ids = [$select_category]; // Include parent category
    foreach ($child_categories as $child) {
        $category_ids[] = $child->term_id; // Add child categories
    }

    // Query posts
    $query_args = [
        'post_type'      => 'post',
        'posts_per_page' => $post_count,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category__in'   => $category_ids, // Fetch posts from parent and child categories
    ];

    $category_posts = new WP_Query($query_args);
?>

    <section class="home-category__featured">
        <!-- Category Buttons -->
        <div class="home-category__featured_buttons">
            <a href="<?php echo get_category_link($parent_category->term_id); ?>">
                <?php echo esc_html($parent_category->name); ?>
            </a>
            <?php foreach ($child_categories as $child) : ?>
                <a href="<?php echo get_category_link($child->term_id); ?>">
                    <?php echo esc_html($child->name); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Post Grid -->
        <div class="home-category__grid">
            <?php if ($category_posts->have_posts()) : ?>
                <?php while ($category_posts->have_posts()) : $category_posts->the_post();
                    $post_id = get_the_ID();
                    $likes_count = wp_ulike_get_post_likes($post_id);
                    $user_id = get_current_user_id();
                    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                    $is_bookmarked = in_array($post_id, $saved_articles);
                    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                ?>
                    <div class="home-category__main-card">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                                <?php if (is_user_logged_in()) { ?>
                                    <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                        <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                    </button>
                                <?php } ?>
                            <?php else : ?>
                                <img src="https://via.placeholder.com/300x200" alt="Placeholder Image">
                                <?php if (is_user_logged_in()) { ?>
                                    <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                        <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                    </button>
                                <?php } ?>
                            <?php endif; ?>
                            <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                            <h3><?php the_title(); ?></h3>
                        </a>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p>No posts found in this category.</p>
            <?php endif; ?>
        </div>
    </section>

<?php } ?>