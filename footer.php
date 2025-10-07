<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

$container = get_theme_mod('understrap_container_type');
?>
</main>
<footer class="footer">
	<div class="footer-container container">
		<a rel="home" class="footer-logo" href="<?php echo esc_url(home_url('/')); ?>" itemprop="url">
			<?php
			$footer_logo = get_field('footer_logo', 'option');
			$footer_logo_title = get_field('footer_logo_title', 'option');

			if ($footer_logo) {
				echo '<img src="' . $footer_logo . '" alt="' . $footer_logo_title . '" width: 313px; height: 55px;>';
			} else {
				echo $footer_logo_title;
			}
			?>
		</a>
		<div class="footer-links">
			<div class="footer-column footer-column1">
				<h3>Home</h3>
				<?php
				wp_nav_menu(
					array(
						'menu'  => 'Home',
						'menu_class'      => ''
					)
				);
				?>
			</div>
			<div class="footer-column ">
				<h3>News</h3>
				<?php
				wp_nav_menu(
					array(
						'menu'  => 'News',
						'menu_class'      => ''
					)
				);
				?>
			</div>
			<div class="footer-column footer-column3  ">
				<h3>Market Data</h3>
				<?php
				wp_nav_menu(
					array(
						'menu'  => 'Market Data',
						'menu_class'      => ''
					)
				);
				?>
			</div>
			<div class="footer-column footer-column3">
				<h3>Explore</h3>
				<?php
				wp_nav_menu(
					array(
						'menu'  => 'Explore',
						'menu_class'      => ''
					)
				);
				?>
			</div>
			<div class="footer-column footer-column4">
				<h3>Subscribe to Our Newsletter</h3>
				<p class="newsletter-desc">Stay in the loop with our latest updates and exclusive content.
					Subscribe to our newsletter and never miss out!</p>
				<?php echo do_shortcode('[mc4wp_form id="3880"]'); ?>
			</div>
		</div>
		<div class="footer-bottom">
			<div class="footer-bottom-left">
				<?php
				wp_nav_menu(
					array(
						'menu'  => 'Terms and Policy',
						'menu_class'      => ''
					)
				);
				?>
			</div>
			<div class="footer-bottom-right">
				<?php $copy_right_text = get_field('copy_right_text', 'option'); ?>
				<p class="copyright-text">&copy; <?php echo $copy_right_text; ?></p>
			</div>
		</div>
	</div>
</footer>

<?php // Closing div#page from header.php. 
?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>