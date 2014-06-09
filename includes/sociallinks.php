<?php

class Sociallinks {

    private static $metabox_id = '_sociallink_';

    public static function hook() {
        add_action( 'init',           array( __CLASS__, 'posttype' ));
        add_action( 'admin_head',     array( __CLASS__, 'posttypeMenuStyles' ));
        add_filter( 'cmb_meta_boxes', array( __CLASS__, 'metaboxes' ));
        add_shortcode( 'sociallinks', array( __CLASS__, 'shortcode' ));
    }

    public static function posttype() {
        register_post_type( 'sociallink',
            array(
                'label'               => 'Social Links',
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'has_archive'         => false,
                'menu_position'       => 21,
                'menu_icon'           => '',
                'capability_type'     => 'post',
                'supports'            => array(
                    'title',
                    'page-attributes'
                ),
            )
        );
    }

    public static function posttypeMenuStyles(){
        // Icons Overview: http://melchoyce.github.io/dashicons/
        ?><style> #adminmenu .menu-icon-sociallink .wp-menu-image:before { content: '\f103'; } </style><?php
    }

    public static function getItemData() {
        $post_id = get_the_id();
        $data = array(
            'title'     => get_the_title(),
            'title_att' => the_title_attribute('echo=0'),
            'url'       => get_post_meta( $post_id, self::$metabox_id.'url', true ),
            'icon'      => get_post_meta( $post_id, self::$metabox_id.'icon', true ),
        );
        return $data;
    }

    public static function getItemHtml() {
        $data = self::getItemData();
        extract($data);
        $html = '<li class="item">'.
                '<a href="'.$url.'" data-toggle="tooltip" data-placement="top" title="'.$title_att.'">'.
                    '<span class="fa fa-'.$icon.'"></span>'.
                    '<span class="sr-only">'.$title.'</span>'.
                '</a>'.
            '</li>';
        return $html;
    }

    public static function metaboxes( $metaboxes ) {
        $metaboxes[self::$metabox_id] = array(
            'id'         => self::$metabox_id.'metabox',
            'title'      => __('Social Link Properties',TXTD),
            'pages'      => array('sociallink'), // Post type
            'context'    => 'normal',
            'priority'   => 'high',
            'show_names' => true, // Show field names on the left
            'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
            'fields'     => array(
                array(
                    'name' => __('URL',TXTD),
                    'id'   => self::$metabox_id.'url',
                    'type' => 'text',
                ),
                array(
                    'name'            => __('Description',TXTD),
                    'id'              => self::$metabox_id.'description',
                    'type'            => 'text',
                    'sanitization_cb' => 'sanitize_text_field',
                ),
                array(
                    'name'            => __('Icon',TXTD),
                    'id'              => self::$metabox_id.'icon',
                    'description'     => '<small><a href="http://fortawesome.github.io/Font-Awesome/icons/#brand" target="_blank">Font Awesome Icons</a></small>',
                    'type'            => 'text_medium',
                    'sanitization_cb' => 'sanitize_key',
                ),
            )
        );
        return $metaboxes;
    }

    public static function shortcode( $atts, $content=null ) {
        extract( shortcode_atts( array(
        ), $atts ) );
        $query  = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type'      => 'sociallink',
            'order'          => 'ASC',
            'orderby'        => 'menu_order',
        ));
        $html = '';
        if( $query->have_posts() ) :
            $html .= '<ul class="sociallinks-list">';
            while( $query->have_posts() ) :
                $query->the_post();
                $html .= self::getItemHtml();
            endwhile;
            $html .= '</ul>';
        endif;
        wp_reset_postdata();
        return $html;
    }

}
