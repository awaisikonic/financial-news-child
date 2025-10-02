<?php

/**
 * Navbar branding
 *
 * @package Understrap
 * @since 1.2.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>
<div class="upper-header">
	<div class="upper-header-wrapper container">
		<?php if (! has_custom_logo()) { ?>

			<h1 class="navbar-brand mb-0">
				<a rel="home" class="logo" href="<?php echo esc_url(home_url('/')); ?>" itemprop="url">
					<?php bloginfo('name'); ?>
				</a>
			</h1>

		<?php
		} else {
			the_custom_logo();
		}
		?>
		<div class="upper-header-right">
			<div class="header-search-bar">
				<?php get_search_form(); ?>
			</div>
			<div class="login-register-wraper">
				<?php if (is_user_logged_in()) { ?>
					<a href="<?php echo esc_url(bp_loggedin_user_domain()); ?>" class="login-register-link">Dashboard</a>
					<span class="separator">|</span>
					<a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="login-register-link">Logout</a>
				<?php } else { ?>
					<a href="/login" class="login-register-link">Login</a>
					<span class="separator">|</span>
					<a href="/register-2" class="login-register-link">Register</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>