<?php

/**
 * The template for displaying search results pages
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');

?>
<div class="search-result-wrapper container">
	<?php if (have_posts()) : ?>

		<header class="page-header">
			<h2 class="search-result-title">
				<?php
				printf(
					/* translators: %s: query term */
					esc_html__('Search Results for: %s', 'understrap'),
					'<span id="searchParams">' . get_search_query() . '</span>'
				);
				?>
			</h2>
		</header><!-- .page-header -->
		<?php
		while (have_posts()) :
			the_post();
			$post_id = get_the_ID();
			$likes_count = wp_ulike_get_post_likes($post_id);
			$user_id = get_current_user_id();
			$saved_articles = get_user_meta($user_id, 'saved_articles', true);
			$saved_articles = !empty($saved_articles) ? $saved_articles : array();

			$is_bookmarked = in_array($post_id, $saved_articles);
			$button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
			$bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';

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

			<a href="<?php the_permalink(); ?>" class="search-result-item">
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
					<div class="item-title-wrapper">
						<h3 class="search-result-item-title">
							<?php the_title(); ?>
							<?php if (is_user_logged_in()) { ?>
								<button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
									<span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
								</button>
							<?php } ?>

						</h3>
					</div>
					<p class="search-result-item-desc"><?php echo wp_trim_words(get_the_excerpt(), 40, '...'); ?></p>
					<div class="search-result-item-info">
						<span class="search-result-item-date"><?php echo get_the_date('F j, Y'); ?></span>
						<p class="count-wrapper count-inline-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
					</div>
				</div>
			</a>
		<?php
		endwhile;
		?>

	<?php else : ?>

		<?php get_template_part('loop-templates/content', 'none'); ?>

	<?php endif; ?>
	<?php
	understrap_pagination();
	?>

</div>
<?php
get_footer();
