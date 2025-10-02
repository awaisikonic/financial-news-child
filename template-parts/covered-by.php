<?php
$post_id = get_the_ID();
$limit = 4;
$keywords = get_post_meta($post_id, '_article_keywords', true);

if (empty($keywords) || !is_array($keywords)) {
    return [];
}

$args = [
    'post_type'      => 'post',
    'posts_per_page' => $limit,
    'post__not_in'   => [$post_id],
    'meta_query'     => [
        'relation' => 'OR',
    ],
];

// Add OR conditions for each keyword
foreach ($keywords as $kw) {
    $args['meta_query'][] = [
        'key'     => '_article_keywords',
        'value'   => $kw,
        'compare' => 'LIKE',
    ];
}

$popular_posts = new WP_Query($args);

if ($popular_posts->have_posts()) : ?>
    <div class="articles-grid">
        <?php while ($popular_posts->have_posts()) : $popular_posts->the_post();
            $post_id = get_the_ID();
            $likes_count = wp_ulike_get_post_likes($post_id);
            $user_id = get_current_user_id();
            $saved_articles = get_user_meta($user_id, 'saved_articles', true);
            $saved_articles = !empty($saved_articles) ? $saved_articles : array();

            $is_bookmarked = in_array($post_id, $saved_articles);
            $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
            $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
        ?>
            <article class="article-card">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php if (is_user_logged_in()) { ?>
                            <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                            </button>
                        <?php } ?>
                    </a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="https://via.placeholder.com/300x225" alt="Placeholder Image">
                        <?php if (is_user_logged_in()) { ?>
                            <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                            </button>
                        <?php } ?>
                    </a>
                <?php endif; ?>
                <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p>by <?php the_author(); ?></p>
            </article>
        <?php endwhile; ?>
    </div>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>