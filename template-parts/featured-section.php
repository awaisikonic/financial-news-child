<?php
$select_posts = get_sub_field('select_posts');
?>

<section class="future-section">
    <div class="future-section__wrapper-grid">
        <!-- Left Column -->
        <div class="future-section__main-column">
            <?php if ($select_posts): ?>
                <?php $post_count = 0; ?>

                <?php foreach ($select_posts as $post): setup_postdata($post);
                    $post_id = get_the_ID();
                    $likes_count = wp_ulike_get_post_likes($post_id);
                    $user_id = get_current_user_id();
                    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
                    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

                    $is_bookmarked = in_array($post_id, $saved_articles);
                    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
                    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                ?>

                    <?php if ($post_count === 0): ?>
                        <a href="<?php the_permalink(); ?>" class="future-section__card-link">
                            <article class="future-section__main-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title_attribute(); ?>" class="future-section__main-image">
                                    <?php if (is_user_logged_in()) { ?>
                                        <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                            <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                        </button>
                                    <?php } ?>
                                <?php endif; ?>
                                <div class="future-section__card-content">
                                    <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                                    <h2 class="future-section__main-title"><?php the_title(); ?></h2>
                                    <p class="future-section__main-desc"><?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?></p>
                                </div>
                            </article>
                        </a>
                        <div class="future-section__side-cards">

                        <?php else: // The other 2 posts 
                        ?>
                            <a href="<?php the_permalink(); ?>" class="future-section__card-link">
                                <article class="future-section__side-card">
                                    <?php if (has_post_thumbnail()): ?>
                                        <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title_attribute(); ?>">
                                        <?php if (is_user_logged_in()) { ?>
                                            <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                                                <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                                            </button>
                                        <?php } ?>
                                    <?php endif; ?>
                                    <div class="future-section__card-content">
                                        <p class="count-wrapper"><i class="fa-solid fa-thumbs-up"></i><span class="like-count"><?php echo $likes_count; ?></span></p>
                                        <h3 class="future-section__side-title"><?php the_title(); ?></h3>
                                    </div>
                                </article>
                            </a>
                        <?php endif; ?>

                        <?php $post_count++; ?>

                    <?php endforeach; ?>

                        </div> <!-- Close .future-section__side-cards -->

                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
        </div>

        <!-- Right Column - Latest News -->
        <?php echo do_shortcode('[latest_news]'); ?>
    </div>
</section>