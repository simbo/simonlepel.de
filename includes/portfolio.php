<?php

class Portfolio {

    private static $metabox_id = '_portfolio_';

    public static function hook() {
        add_action( 'wp',             array( __CLASS__, 'ajaxResponse' ));
        add_action( 'init',           array( __CLASS__, 'posttype' ));
        add_action( 'admin_head',     array( __CLASS__, 'posttypeMenuStyles' ));
        add_filter( 'cmb_meta_boxes', array( __CLASS__, 'metaboxes' ));
        add_shortcode( 'portfolio',   array( __CLASS__, 'shortcode' ));
    }

    public static function posttype() {
        register_post_type( 'portfolio',
            array(
                'label'           => 'Portfolio',
                'public'          => true,
                'has_archive'     => true,
                'menu_position'   => 20,
                'menu_icon'       => '',
                'capability_type' => 'post',
                'supports'        => array(
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                    'custom-fields',
                    'revisions',
                    'page-attributes'
                ),
            )
        );
        register_taxonomy( 'portfolio-category', 'portfolio', array( 'hierarchical' => true ));
        register_taxonomy( 'portfolio-tag',      'portfolio' );
        register_taxonomy_for_object_type( 'portfolio-category', 'portfolio' );
        register_taxonomy_for_object_type( 'portfolio-tag',      'portfolio' );
    }

    public static function posttypeMenuStyles() {
        // Icons Overview: http://melchoyce.github.io/dashicons/
        ?><style> #adminmenu .menu-icon-portfolio .wp-menu-image:before { content: '\f115'; } </style><?php
    }

    public static function ajaxResponse() {
        if( get_post_type() == 'portfolio' && have_posts() ) :
            if( strtolower($_SERVER['REQUEST_METHOD'])=='post' && isset($_POST['ajax']) && $_POST['ajax']=='1' ) :
                the_post();
                $data = self::getDetailsData();
                $data['html'] = self::getDetailsHtml($data);
                header("Content-type: text/plain");
                echo json_encode($data);
                die();
            else :
                wp_redirect( get_home_url() );
            endif;
        endif;
    }

    public static function getDetailsData() {
        $post_id  = get_the_id();
        $title    = get_the_title();
        $subtitle = get_post_meta( $post_id, self::$metabox_id.'subtitle', true );
        $links    = get_post_meta( $post_id, self::$metabox_id.'links', true );
        $content  = get_the_content();
        $images   = array();
        $title    = apply_filters( 'the_title', $title );
        $subtitle = apply_filters( 'the_title', $subtitle );
        $content  = apply_filters( 'the_content', $content );
        $content  = wpautop( $content );
        $_images  = get_post_meta( $post_id, '_portfolio_images', true );
        foreach( $_images as $img_id => $full_url ) {
            $src = wp_get_attachment_image_src( $img_id, 'portfolio-screen' );
            array_push( $images, array(
                'src'    => $src[0],
                'width'  => $src[1],
                'height' => $src[2],
                'id'     => $img_id
            ));
        }
        $data = array(
            'title'       => $title,
            'subtitle'    => $subtitle,
            'content'     => $content,
            'links'       => $links,
            'images'      => $images,
        );
        return $data;
    }

    public static function getPreviewData() {
        $post_id      = get_the_id();
        $permalink    = get_the_permalink();
        $title        = get_the_title();
        $title_att    = the_title_attribute('echo=0');
        $subtitle     = get_post_meta( $post_id, self::$metabox_id.'subtitle', true );
        $description  = get_post_meta( $post_id, self::$metabox_id.'description', true );
        $title        = apply_filters( 'the_title', $title );
        $subtitle     = apply_filters( 'the_title', $subtitle );
        $description  = wpautop( $description );
        $thumb_exists = has_post_thumbnail($post_id) ? true : false;
        $thumb_id     = $thumb_exists ? get_post_thumbnail_id($post_id) : false;
        $thumb_src    = $thumb_id ? wp_get_attachment_image_src( $thumb_id, 'portfolio-thumb' ) : false;
        $data = array(
            'permalink'    => $permalink,
            'title'        => $title,
            'title_att'    => $title_att,
            'subtitle'     => $subtitle,
            'description'  => $description,
            'thumb_exists' => $thumb_exists,
            'thumb_src'    => $thumb_src,
        );
        return $data;
    }

    public static function getDetailsHtml( $data=null ) {
        if( !$data )
            $data = self::getDetailsData();
        extract($data);
        $html = '<div class="portfolio-details">'.
                    '<div class="row">'.
                    '<div class="col col-sm-7">'.
                        '<div class="slider">';
                            foreach( $images as $i => $img ) :
                                $html .= '<div class="slider-item'.($i==0?' active':'').'">'.
                                    '<figure>'.
                                        '<img src="'.$img['src'].'" alt="" width="'.$img['width'].'" height="'.$img['height'].'">'.
                                    '</figure>'.
                                '</div>';
                            endforeach;
        $html .=        '</div>'.
                    '</div>'.
                    '<div class="col col-sm-5">'.
                        '<div class="portfolio-content">'.
                            '<div class="inner">'.
                                '<h3 class="title">'.$title.'</h3>'.
                                '<p class="subtitle">'.$subtitle.'</p>'.
                                '<div class="description">'.$content.'</div>';
                                if( is_array($links) ) foreach( $links as $link ) :
                                    $html .= '<a href="'.$link['url'].'" class="btn btn-transparent-white">'.$link['label'].'</a>';
                                endforeach;
        $html .=            '</div>'.
                        '</div>'.
                    '</div>'.
                    '<div class="close"></div>'.
                '</div>'.
            '</div>';
        return $html;
    }

    public static function getPreviewHtml( $data=null ) {
        if( !$data )
            $data = self::getPreviewData();
        extract($data);
        $html = '';
        if( $thumb_exists ) :
            $html .= '<li class="item">'.
                    '<figure>'.
                        '<div class="viewport">'.
                            '<div class="moving-area">'.
                                '<img src="'.$thumb_src[0].'"'.
                                    ' width="'.$thumb_src[1].'"'.
                                    ' height="'.$thumb_src[2].'"'.
                                    ' alt="'.$title_att.'">'.
                            '</div>'.
                        '</div>'.
                        '<figcaption class="portfolio-content">'.
                            '<h4 class="title">'.$title.'</h4>'.
                            '<p class="subtitle">'.$subtitle.'</p>'.
                            // '<p class="short-description">'.$description.'</p>'.
                            '<a class="btn btn-transparent-white" href="'.$permalink.'">mehr zeigen</a>'.
                            '<span class="loading-indicator white"></span>'.
                        '</figcaption>'.
                    '</figure>'.
                '</li>';
        endif;
        return $html;
    }

    public static function metaboxes($metaboxes) {
        $id = self::$metabox_id;
        $metaboxes[$id] = array(
            'id'         => $id.'metabox',
            'title'      => __('Project Properties',TXTD),
            'pages'      => array( 'portfolio' ), // Post type
            'context'    => 'normal',
            'priority'   => 'high',
            'show_names' => true, // hide field names on the left
            'cmb_styles' => true, // enqueue the CMB stylesheet on the frontend
            'fields'     => array(
                array(
                    'name' => __('Subtitle',TXTD),
                    'id'   => $id.'subtitle',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Short Description',TXTD),
                    'id'   => $id.'description',
                    'type' => 'textarea_small',
                ),
                array(
                    'name'         => __('Slider Images',TXTD),
                    'id'           => $id.'images',
                    'type'         => 'file_list',
                    'preview_size' => array( 160, 120 ),
                ),
                array(
                    'id'      => $id.'links',
                    'type'    => 'group',
                    'options' => array(
                        'add_button'    => __('Add Another Entry',TXTD),
                        'remove_button' => __('Remove Entry', TXTD),
                        'sortable'      => true,
                    ),
                    'fields' => array(
                        array(
                            'name' => __('URL',TXTD),
                            'id'   => 'url',
                            'type' => 'text_url',
                        ),
                        array(
                            'name' => __('Label',TXTD),
                            'id'   => 'label',
                            'type' => 'text',
                        ),
                    ),
                ),
            )
        );
        return $metaboxes;
    }

    public static function shortcode( $atts, $content=null ) {
        extract( shortcode_atts( array(
        ), $atts ) );
        $query = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type'      => 'portfolio',
            'order'          => 'ASC',
            'orderby'        => 'menu_order',
        ));
        $html = '';
        if( $query->have_posts() ) :
            $html = '<div class="portfolio-details-container"></div>'.
                '<ul class="portfolio-index">';
            while( $query->have_posts() ) :
                $query->the_post();
                $html .= self::getPreviewHtml();
            endwhile;
            $html .= '</ul>';
        endif;
        wp_reset_postdata();
        return $html;
    }

}
