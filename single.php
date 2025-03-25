<?php
/**
 * The template for displaying all single posts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>
<section class="articles-section">
	<?php
	while ( have_posts() ) {
		the_post();
		?>
		<div class="article-grid main-content-with-audio">
			<div class="article-grid-inner-grid article-heading">
				<?php
				$categories = get_the_category();
				$parent_category = '';
				$child_category = '';

				// Check if the post has categories
				if ($categories) {
					foreach ($categories as $category) {
						if ($category->parent == 0) {
							// Parent category
							$parent_category = $category;
						} else {
							// Child category (first found)
							$child_category = $category;
						}
					}
				}

				// Display parent and child category
				if ($parent_category) : ?>
					<div class="article-heading-left">
						<h6>
							<a href="<?php echo get_category_link($parent_category->term_id); ?>"><?php echo esc_html($parent_category->name); ?></a>
							<?php if ($child_category) : ?>
								<br> <span><a href="<?php echo get_category_link($child_category->term_id); ?>"><?php echo esc_html($child_category->name); ?></a></span>
							<?php endif; ?>
						</h6>
					</div>
				<?php endif; ?>
				<div class="article-heading-right">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<?php if (has_post_thumbnail()) : ?>
						<div class="article-heading-img">
							<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" 
								alt="<?php the_title_attribute(); ?>">
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="article-grid main-content-with-audio">
			<div class="article-grid-inner-grid main-content-with-audio">
				<div></div>
				<div class="artical-main-content">
					<?php
					the_content();
					?>
				</div>
			</div>
		</div>

		<?php
		//understrap_post_nav();
	}
	?>
</section>
<section class="related-articles">
	<h2>More From InsightNews</h2>
	<?php get_template_part( 'template-parts/related' ); ?>

	<div class="top-reads">
		<h2>Popular Reads</h2>
		<?php get_template_part( 'template-parts/popular' ); ?>
	</div>
</section>
<?php
get_footer();
