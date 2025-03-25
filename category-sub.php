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
            if ( have_posts() ) {
                // Start the loop.
                while ( have_posts() ) {
                    the_post();

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
                    </div>
                    <?php
                }
            } else {
                get_template_part( 'loop-templates/content', 'none' );
            }
            understrap_pagination();
            ?>
        </div>
    </section>
</div>

<?php get_footer(); ?>