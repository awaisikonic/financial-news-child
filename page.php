<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
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
				<div class="article-heading-right">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
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

<?php
get_footer();
