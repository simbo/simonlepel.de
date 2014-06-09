<?php

function theme_get_option( $key='' ) {
    return cmb_get_option( THEME_OPTIONS_KEY, THEME_OPTIONS_PREFIX.$key );
}

$themeOptions = new CustomThemeOptions(array(
    // Option key, and option page slug
    'custom_theme_options' =>  THEME_OPTIONS_KEY,
    // Options Page title
    'title' =>  __('Theme Options',TXTD),
    // Options Menu Label
    'label' =>  __('Theme Options',TXTD),
    // Theme prefix
    'prefix' =>  THEME_OPTIONS_PREFIX,
    // Textdomain
    'textdomain' =>  TXTD,
    // Custom Metaboxes Wiki: https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki
    'metabox_options' =>  array(
        'id'            => THEME_OPTIONS_KEY,
        'show_on'       => array( 'key'=>'options-page', 'value'=>array(THEME_OPTIONS_KEY) ),
        'show_names'    => true,
        'fields'        => array(
            array(
                'name' => __('Test Text',TXTD),
                'desc' => __('field description (optional)',TXTD),
                'id'   => THEME_OPTIONS_PREFIX.'test_text',
                'type' => 'text',
            ),
        )
    )
));

