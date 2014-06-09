<?php

/* =============================================================================
   If not in admin backend...
   ========================================================================== */

if( !is_admin() && !is_login() ) :

    /* =============================================================================
       Replace Wordpress' jQuery version with a Google CDN version
       ========================================================================== */

    function jquery_replacement() {
        if( !is_admin() ) {
            $jquery_cdn = 'https://ajax.googleapis.com/ajax/libs/jquery/'.JQUERY_VERSION.'/jquery.min.js';
            wp_deregister_script('jquery');
            wp_register_script( 'jquery', $jquery_cdn, false, null, true );
        }
    }
    add_action('init', 'jquery_replacement' );

    /* =============================================================================
       jQuery local fallback   (http://wordpress.stackexchange.com/a/12450)
       ========================================================================== */

    function jquery_fallback( $src, $handle = null ) {
        static $run_next = false;
        if( $run_next ) {
            $jquery_local = get_template_directory_uri().'/assets/js/vendor/jquery/jquery.min.js';
            echo '<script type="text/javascript">/*//<![CDATA[*/window.jQuery || document.write(\'<script type="text/javascript" src="'.$jquery_local.'"><\/script>\');/*//]]>*/</script>'."\n";
            $run_next = false;
        }
        if( $handle === 'jquery' )
            $run_next = true;
        return $src;
    }
    add_filter( 'script_loader_src', 'jquery_fallback', 10, 2 );
    add_action( 'wp_head', 'jquery_fallback', 2 );

    /* =============================================================================
       Scripts for IE 9 and lower
       ========================================================================== */

    function scripts_for_ie9() {
        echo '<!--[if lt IE 9]>'."\n".
            '<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>'."\n".
            '<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>'."\n".
            '<![endif]-->'."\n";
    }
    add_action( 'wp_head', 'scripts_for_ie9' );

/* =============================================================================
   End if not in admin backend.
   ========================================================================== */

endif;

/* =============================================================================
   Web Font Loader - inline script, directly after <head>
   ========================================================================== */

function webfont_loader() {
    echo "<script type=\"text/javascript\">".
        "WebFontConfig={".
            "google:{".
                "families:['".implode("','",google_fonts())."']".
            "},".
            "custom:{".
                "families:['FontAwesome'],".
                "urls:['".get_template_directory_uri()."/assets/css/fontawesome-font.min.css'],".
                "testStrings:{'FontAwesome':'\uf00c\uf000'}".
            "}".
         "};".
         "(function(){".
             "var wf=document.createElement('script');".
             "wf.src=('https:'==document.location.protocol?'https':'http')+'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';".
             "wf.type='text/javascript';".
             "wf.async='true';".
             "var s=document.getElementsByTagName('script')[0];".
             "s.parentNode.insertBefore(wf,s);".
         "})();".
        "</script>\n";
}


/* =============================================================================
   Enqueue Scripts and Styles
   ========================================================================== */

function scripts_n_styles() {
    wp_enqueue_style('theme-styles', get_template_directory_uri().'/assets/css/styles.min.css', array(), '0959');
    // modernizr
    wp_register_script('modernizr',get_template_directory_uri().'/assets/js/vendor/modernizr/modernizr.min.js', array(), null, false);
    // theme scripts
    $script_dependencies = array('modernizr','jquery');
    wp_register_script('theme-scripts', get_template_directory_uri().'/assets/js/main.min.js', $script_dependencies, '94ed', false);
    wp_enqueue_script('theme-scripts');
    // comment scripts
    if( is_single() && comments_open() && get_option('thread_comments') )
        wp_enqueue_script('comment-reply');
}
add_action( 'wp_enqueue_scripts', 'scripts_n_styles', 99 );
