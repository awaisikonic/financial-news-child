<?php
/**
 * Template Name: Home Page
 *
 * @package Ikonic Dev
 */
get_header();
?>
<div class="home-main-wrapper">
    <?php
        get_template_part('template-parts/content', 'sections');
    ?>
</div>
<?php
get_footer();
?>