<?php
/**
 * The template for displaying search results pages
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );

?>
<div class="search-result-wrapper container">
	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h2 class="search-result-title">
				<?php
				printf(
					/* translators: %s: query term */
					esc_html__( 'Search Results for: %s', 'understrap' ),
					'<span id="searchParams">' . get_search_query() . '</span>'
				);
				?>
			</h2>
		</header><!-- .page-header -->
		<?php
		while ( have_posts() ) :
			the_post();

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
			?>

			<a href="<?php the_permalink();?>" class="search-result-item">
				<?php if (has_post_thumbnail()) : ?>
					<div class="search-result-item-img">
						<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" 
							alt="<?php the_title_attribute(); ?>">
					</div>
				<?php endif; ?>
				<div class="search-result-item-content">
					<?php
					// Display parent and child category
					if ($parent_category) : ?>
						<div class="article-heading-left">
							<h6 class="search-result-item-sub-title">
								<?php echo esc_html($parent_category->name); ?>
								<?php if ($child_category) : ?>
									<span><?php echo esc_html($child_category->name); ?></span>
								<?php endif; ?>
							</h6>
						</div>
					<?php endif; ?>
					<?php the_title( '<h3 class="search-result-item-title">', '</h3>' ); ?>
					<p class="search-result-item-desc"><?php echo wp_trim_words(get_the_excerpt(), 40, '...'); ?></p>
					<div class="search-result-item-info">
						<span class="search-result-item-date"><?php echo get_the_date('F j, Y'); ?></span>
					</div>
				</div>   
			</a>
			<?php
		endwhile;
		?>

	<?php else : ?>

		<?php get_template_part( 'loop-templates/content', 'none' ); ?>

	<?php endif; ?>
<?php
understrap_pagination();
?>

</div>
<?php
get_footer();
