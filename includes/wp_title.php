<?php

/* ==========================================================================
   Filter Site Title
   ========================================================================== */

function custom_wp_title( $title, $sep ) {

    global $page, $paged;

    if ( is_feed() )
        return $title;

    $site_title = get_bloginfo('name');
    $site_description = get_bloginfo('description');

    if( 2 <= $paged || 2 <= $page )
        $title .=  $sep.sprintf( 'Seite %s', max($paged,$page) );

    if( is_front_page() )
        $title = $site_title . ( !empty($site_description) ? $sep.$site_description : '' );
    else
        $title .= $sep.$site_title;

    return $title;
}
add_filter( 'wp_title', 'custom_wp_title', 11, 2);
