<?php
if( have_rows('content-sections') ):
    while ( have_rows('content-sections') ) : the_row();
        if( get_row_layout() == 'featured_section' ):
            get_template_part('template-parts/featured-section');

        elseif( get_row_layout() == 'posts_section' ): 
            get_template_part('template-parts/posts-section');
        endif;

    endwhile;

else :
    // Do something...
endif;