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
            <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                <article>
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <img src="https://via.placeholder.com/300x225" alt="Placeholder Image">
                        <?php endif; ?>
                        <h3><?php the_title(); ?></h3>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif;
} ?>