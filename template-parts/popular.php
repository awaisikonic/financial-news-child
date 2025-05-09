<?php
$popular_posts = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => 4,
    'meta_key'       => 'post_views_count',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC'
]);

if ($popular_posts->have_posts()) : ?>
    <div class="articles-grid">
        <?php while ($popular_posts->have_posts()) : $popular_posts->the_post(); ?>
            <article>
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>">
                    </a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="https://via.placeholder.com/300x225" alt="Placeholder Image">
                    </a>
                <?php endif; ?>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p>by <?php the_author(); ?></p>
            </article>
        <?php endwhile; ?>
    </div>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>