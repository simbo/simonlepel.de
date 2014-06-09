<?php

/* =============================================================================
   Determine if current page is login or registration
   ========================================================================== */

function is_login() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php') );
}

/* =============================================================================
   Determine if a page is a child page (or child of a specific page)
   ========================================================================== */

function is_page_child( $parent_page_id=null, $page_id=null ) {
    global $post;
    $page = $page_id ? get_post($page_id) : $post;
    if( $page && is_page($page->ID) && isset($page->post_parent) )
        return ($parent_page_id && $page->post_parent==$parent_page_id) || $page->post_parent>0;
    return false;
}
