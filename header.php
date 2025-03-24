<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$bootstrap_version = get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' );
$navbar_type       = get_theme_mod( 'understrap_navbar_type', 'collapse' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<!-- ----- google font  -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link
		href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">
	<!-- ----- icon cdn  -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
		integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php understrap_body_attributes(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="site" id="page">
    <!-- ===== header ====  -->
    <header class="header" id="wrapper-navbar">
        <div class="desktop-header-wrapper">

            <?php get_template_part( 'global-templates/navbar-branding' ); ?>

            <?php get_template_part( 'global-templates/navbar', $navbar_type . '-' . $bootstrap_version ); ?>
        </div>
    </header> <!-- #wrapper-navbar -->
	
	<div class="market-ticker-container container">
		<div class="market-ticker-slider-container">
			<select class="market-ticker-filter market-ticker-filter-desktop" id="categoryFilter">
				<option value="securities">Securities</option>
				<option value="indices">Indices</option>
				<option value="rates">Rates & Bonds</option>
			</select>
			<select class="market-ticker-filter market-ticker-filter-mobile" id="categoryFilterMobile">
				<option value="securities">Securities</option>
				<option value="indices">Indices</option>
				<option value="rates">Rates & Bonds</option>
			</select>
			
			<div id="marketDataWrapper" class="market-ticker-wrapper market-ticker-buttons-wrapper">
				<?php echo do_shortcode('[market_ticker category="securities"]'); ?>
			</div>
			
			<div class="market-ticker-nav-container">
				<button id="marketSlidePrev" class="market-ticker-nav-btn">
					<i class="fas fa-chevron-left"></i>
				</button>
				<button id="marketSlideNext" class="market-ticker-nav-btn">
					<i class="fas fa-chevron-right"></i>
				</button>
			</div>
		</div>
	</div>
	<?php
	$container_class = 'container'; // Default class

	if (is_single() || is_page()) {
		$container_class .= ' main-article'; // Add class for single posts
	} elseif (is_home() || is_archive()) {
		$container_class .= ' latest-post'; // Add class for blog/archive pages
	}
	?>
    <main class="<?php echo $container_class;?>">