<?php
$categories = get_the_category();
if ($categories) {
    $category_ids = [];

    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }

    $related_posts = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 4,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category__in'   => $category_ids,
    ]);

    if ($related_posts->have_posts()) : ?>
        <div class="articles-grid">
            <?php while ($related_posts->have_posts()) : $related_posts->the_post();
                $post_id = get_the_ID();
                $user_id = get_current_user_id();
                $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                $is_bookmarked = in_array($post_id, $saved_articles);
                $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
            ?>
                <article class="article-card">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>">
                            <?php if (is_user_logged_in()) { ?>
                                <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                    <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                </button>
                            <?php } ?>
                        <?php else : ?>
                            <img src="https://via.placeholder.com/300x225" alt="Placeholder Image">
                            <?php if (is_user_logged_in()) { ?>
                                <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                    <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                </button>
                            <?php } ?>
                        <?php endif; ?>
                        <h3><?php the_title(); ?></h3>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
<?php endif;
} ?>