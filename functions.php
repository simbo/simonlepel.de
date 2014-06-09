<?php

/* =============================================================================
   Includes
   ========================================================================== */

$includes = array(

    'lib/utils',                        // Utility functions
    'vendor/roots/utils',               // [ROOTS] Utility functions

    'config',                           // Theme config

    'vendor/roots/wrapper',             // [ROOTS] Theme wrapper class
    'vendor/roots/cleanup',             // [ROOTS] Cleanup
    'vendor/roots/nav',                 // [ROOTS] Bootstrap nav walker
    'vendor/roots/comments',            // [ROOTS] Custom comments modifications
    'vendor/roots/relative-urls',       // [ROOTS] Root relative URLs
    'vendor/roots/titles',              // [ROOTS] Page titles

    'lib/class-custom-theme-options',   // Custom Theme Options Class
    'lib/conditional-display',          // Conditional display for template parts
    'lib/shortcode-bootstrap-grid',     // Shortcodes for Bootstrap Grid

    'contactform',                      // Contactform handler
    'portfolio',                        // Portfolio Class
    'sociallinks',                      // Sociallinks Class
    'metabox-multiple-sections',        // Metabox for multiple content sections

    'init',                             // Initialize basics
    'theme-options',                    // Custom Theme Options Config
    'scriptsnstyles',                   // Scripts'n'styles
    'wp_title',                         // Filter wp_title
    'piwik'                             // Piwik Analytics Code

);

foreach( $includes as $i )
    require_once locate_template('includes/'.$i.'.php');
