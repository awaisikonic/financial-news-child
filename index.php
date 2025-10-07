<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
?>

<div class="latest-post-main-wrapper">
	<?php
	if (have_posts()) {
	?>
		<section class="latest-post__featured-section">
			<h1 class="latest-post__title">Latest Articles</h1>
			<div class="future-section__latest-header-catg">
				<button class="future-section__latest-header-catg-btn" id="toggleDropdownblog">
					All categories <i class="fas fa-chevron-down"></i>
				</button>
				<ul class="future-section__latest-header-catg-list" id="catgDropdown">
					<li class="future-section__latest-header-catg-item">All categories</li>
					<?php
					$categories = get_categories(['parent' => 0, 'hide_empty' => true, 'exclude'     => 1,]);
					foreach ($categories as $category) : ?>
						<li class="future-section__latest-header-catg-item" data-cat="<?php echo $category->term_id; ?>">
							<?php echo esc_html($category->name); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<div class="more-industrie-main" id="more-industrie-main">
			<?php
			// Start the Loop.
			while (have_posts()) {
				the_post();
				$post_id = get_the_ID();
				$user_id = get_current_user_id();
				$saved_articles = get_user_meta($user_id, 'saved_articles', true);
				$saved_articles = !empty($saved_articles) ? $saved_articles : array();

				$is_bookmarked = in_array($post_id, $saved_articles);
				$button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
				$bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
			?>
				<!-- card  -->
				<div class="latest-post-news-card">
					<div>
						<p><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></p>
						<h5 class="entry-title"><a href="<?php the_permalink(); ?>" class=""><?php the_title(); ?></a>
							<?php if (is_user_logged_in()) { ?>
								<button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
									<span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
								</button>
							<?php } ?>
						</h5>
					</div>
					<?php if (has_post_thumbnail()) : ?>
						<a href="<?php the_permalink(); ?>" class="">
							<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>"
								alt="<?php the_title_attribute(); ?>">
						</a>
					<?php endif; ?>
				</div>
			<?php
			}
			?>
		</div>
	<?php
	} else {
		get_template_part('loop-templates/content', 'none');
	}
	?>
	<?php
	// Display the pagination component.
	understrap_pagination();
	?>
</div>
<?php
get_footer();
