<?php

/* =============================================================================
   SHORTCODE: Bootstrap Grid System - Rows
   ========================================================================== */

function shortcode_bsgrid_row( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'class' => '',
        'visible' => '',
        'hidden' => ''
    ), $atts ) );
    $content = do_shortcode($content);
    $html = '<div class="row'.
        ( !empty($visible) ? ' visible-'.implode(' visible-', array_map('trim',explode(',',$visible)) ) : '' ).
        ( !empty($hidden)  ? ' hidden-' .implode(' hidden-',  array_map('trim',explode(',',$hidden))  ) : '' ).
        ( $class!=='' ? ' '.$class : '' ).
        '">'.$content.'</div>';
    return $html;
}
add_shortcode( 'row', 'shortcode_bsgrid_row' );

for( $i=0; $i<10; $i++ )
    add_shortcode( 'row'.$i, 'shortcode_bsgrid_row' );

/* =============================================================================
   SHORTCODE: Bootstrap Grid System - Cols
   ========================================================================== */

function shortcode_bsgrid_col( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'xs' => '',
        'sm' => '',
        'md' => '',
        'lg' => '',
        'xs_offset' => '',
        'sm_offset' => '',
        'md_offset' => '',
        'lg_offset' => '',
        'xs_push' => '',
        'sm_push' => '',
        'md_push' => '',
        'lg_push' => '',
        'xs_pull' => '',
        'sm_pull' => '',
        'md_pull' => '',
        'lg_pull' => '',
        'visible' => '',
        'hidden' => '',
        'class' => ''
    ), $atts ) );
    if( has_filter( 'the_content', 'wpautop' ) )
        $content = wpautop(trim(do_shortcode($content)));
    $content = trim(do_shortcode($content));
    $html = '<div class="col'.
        ( $xs!='' ? ' col-xs-'.$xs : '' ).
        ( $sm!='' ? ' col-sm-'.$sm : '' ).
        ( $md!='' ? ' col-md-'.$md : '' ).
        ( $lg!='' ? ' col-lg-'.$lg : '' ).
        ( $xs_offset!='' ? ' col-xs-offset-'.$xs_offset : '' ).
        ( $sm_offset!='' ? ' col-sm-offset-'.$sm_offset : '' ).
        ( $md_offset!='' ? ' col-md-offset-'.$md_offset : '' ).
        ( $lg_offset!='' ? ' col-lg-offset-'.$lg_offset : '' ).
        ( $xs_push!='' ? ' col-xs-push-'.$xs_push : '' ).
        ( $sm_push!='' ? ' col-sm-push-'.$sm_push : '' ).
        ( $md_push!='' ? ' col-md-push-'.$md_push : '' ).
        ( $lg_push!='' ? ' col-lg-push-'.$lg_push : '' ).
        ( $xs_pull!='' ? ' col-xs-pull-'.$xs_pull : '' ).
        ( $sm_pull!='' ? ' col-sm-pull-'.$sm_pull : '' ).
        ( $md_pull!='' ? ' col-md-pull-'.$md_pull : '' ).
        ( $lg_pull!='' ? ' col-lg-pull-'.$lg_pull : '' ).
        ( !empty($visible) ? ' visible-'.implode(' visible-', array_map('trim',explode(',',$visible)) ) : '' ).
        ( !empty($hidden)  ? ' hidden-' .implode(' hidden-',  array_map('trim',explode(',',$hidden))  ) : '' ).
        ( $class!=='' ? ' '.$class : '' ).
        '">'.$content.'</div>';
    return $html;
}
add_shortcode( 'col', 'shortcode_bsgrid_col' );

for( $i=0; $i<10; $i++ )
    add_shortcode( 'col'.$i, 'shortcode_bsgrid_col' );

/* =============================================================================
   SHORTCODE: Clear
   ========================================================================== */

function shortcode_bsgrid_clear( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'visible' => '',
        'hidden' => ''
    ), $atts ) );
    $html = '<div class="row'.
        ( !empty($visible) ? ' visible-'.implode(' visible-', array_map('trim',explode(',',$visible)) ) : '' ).
        ( !empty($hidden)  ? ' hidden-' .implode(' hidden-',  array_map('trim',explode(',',$hidden))  ) : '' ).
        '">'.$content.'</div>';
    return $html;
}
add_shortcode( 'clear', 'shortcode_bsgrid_clear' );
