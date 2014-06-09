<?php

/* =============================================================================
   Statics
   ========================================================================== */

define( 'TXTD',                 'custom_theme');            // theme textdomain
define( 'THEME_OPTIONS_KEY',    'custom_theme_options' );   // Theme options page slug
define( 'THEME_OPTIONS_PREFIX', THEME_OPTIONS_KEY.'_'  );   // Theme options fields key prefix
define( 'POST_EXCERPT_LENGTH',  40);                        // Length in words for excerpt_length filter
define( 'JQUERY_VERSION',       '1.11.0');                  // jQuery version

/* =============================================================================
   Roots modules
   ========================================================================== */

add_theme_support('root-relative-urls');    // Enable relative URLs
add_theme_support('bootstrap-top-navbar');  // Enable Bootstrap's top navbar
add_theme_support('nice-search');           // Enable /?s= to /search/ redirect

/* =============================================================================
   Google Fonts
   ========================================================================== */

function google_fonts() {
    return array(
        'Lato:300,400,700,900,300italic,400italic,700italic,900italic'
        // 'Inconsolata:400,700'
    );
}

/* =============================================================================
   Auto-paragraph filter
   ========================================================================== */

// remove auto-p filter
remove_filter( 'the_content', 'wpautop' );
// add auto-p filter after shortcodes
//add_filter( 'the_content', 'wpautop' , 12 );

/* =============================================================================
   Jpeg quality
   ========================================================================== */

add_filter('jpeg_quality', function() {
    return 85;
});

/* =============================================================================
   Widgets / Sidebars
   ========================================================================== */

function theme_widgets_init() {

    register_sidebar( array(
        'name'          => __('Main Sidebar',TXTD),
        'id'            => 'sidebar-main',
        'description'   => __('The main Sidebar of this theme.',TXTD),
        'class'         => '',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>'
    ) );

}
add_action( 'widgets_init', 'theme_widgets_init' );

/* =============================================================================
   Header visibility (Conditions on displaying the header)
   ========================================================================== */

function display_header() {
    return (new ConditionalDisplay(
        array(
        )
    ))->display ? false : true;
}

/* =============================================================================
   Sidebar visibility (Conditions on displaying the sidebar)
   ========================================================================== */

function display_sidebar() {
    return (new ConditionalDisplay(
        array(
        )
    ))->display ? false : true;
}

/* =============================================================================
   Footer visibility (Conditions on NOT displaying the footer)
   ========================================================================== */

function display_footer() {
    return (new ConditionalDisplay(
        array(
            'is_404'
        )
    ))->display;
}

/* =============================================================================
   Content container class (depending on sidebar visibility)
   ========================================================================== */

function theme_content_class() {
    return display_sidebar() ? '' : '';
}

/* =============================================================================
   Main container class (depending on sidebar visibility)
   ========================================================================== */

function theme_main_class() {
    return display_sidebar() ? 'col col-sm-8' : '';
}

/* =============================================================================
   Sidebar container class
   ========================================================================== */

function theme_sidebar_class() {
    return 'col col-sm-4';
}

/* =============================================================================
   Content width for WordPress media
   ========================================================================== */

global $content_width;
if( !isset($content_width) )
    $content_width = 1140;
