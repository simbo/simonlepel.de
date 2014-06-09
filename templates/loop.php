<?php
global $post;
if( have_posts() ) :
    while( have_posts() ) :
        the_post();
        get_template_part('templates/content', get_post_type());
    endwhile;
else :
    get_template_part('templates/content', '404');
endif;
