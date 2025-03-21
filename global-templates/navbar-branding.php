<?php
/**
 * Navbar branding
 *
 * @package Understrap
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="upper-header">
	<div class="upper-header-wrapper container">
	<?php if ( ! has_custom_logo() ) { ?>

		<h1 class="navbar-brand mb-0">
			<a rel="home" class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url">
				<?php bloginfo( 'name' ); ?>
			</a>
		</h1>

	<?php
	} else {
		the_custom_logo();
	}
	?>
		<div class="upper-header-right">
			<div class="header-search-bar">
				<input type="text">
				<button class="header-input-search-btn">
					<i class="fa-solid fa-magnifying-glass"></i>
				</button>
			</div>
		</div>
	</div>
</div>
