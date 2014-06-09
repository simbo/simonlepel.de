<?php

function theme_setup() {

    // translation ready
    load_theme_textdomain( TXTD, get_template_directory().'/lang' );

    // roots translation
    load_theme_textdomain('roots', get_template_directory().'/lang/vendor/roots');


    // register nav menus
    register_nav_menus(array(
        'primary_navigation' => __('Primary Navigation', TXTD),
        'footer_navigation' => __('Footer Navigation', TXTD),
    ));

    // post thumbnails
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(380, 0, false);
    add_image_size('fullscreen', 1600, 0);

    // portfolio thumbs
    add_image_size('portfolio-thumb', 600, 600, true);
    add_image_size('portfolio-screen', 800, 0);

    // add editor styles
    // add_editor_style( '/assets/css/editor-style.min.css' );

}
add_action( 'after_setup_theme', 'theme_setup' );

// hook metabox initialization
function theme_init_cmb_metaboxes() {
    if( !class_exists('cmb_Meta_Box') )
       require_once locate_template('includes/vendor/metabox/init.php');
}
add_action( 'init', 'theme_init_cmb_metaboxes', 9999 );

// hook portfolio
Portfolio::hook();

// hook sociallinks
Sociallinks::hook();
