<?php
/**
 * The template for displaying archive pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
$category = get_queried_object();
$parent_id = $category->parent;

if ($parent_id == 0) {
    get_template_part('category', 'parent');
} else {
    get_template_part('category', 'sub');
}
get_footer();
