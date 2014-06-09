<?php

class CustomThemeOptions{

    protected $defaults = array(
                'id'                =>  'custom_theme_options',  // Option key, and option page slug
                'title'             =>  'Options',               // Options Page title
                'label'             =>  null,                    // Options Menu Label, defaults to title
                'prefix'            =>  'theme',                 // Theme prefix
                'textdomain'        =>  null,                    // Textdomain, defaults to prefix
                'metabox_options'   =>  null,                    // cmb_metabox_form params
                'parent_slug'       =>  'themes.php',            // Submenu parent_slug, set to false to create a first level menu item
                'capabilities'      =>  'manage_options'         // Permissions needed
            );

    public function __construct( $args=array() ) {
        $options = wp_parse_args($args, $this->defaults);
        extract($options, EXTR_SKIP);
        if( !empty($id) && !empty($title) && !empty($prefix) ) {
            $this->id               =   $id;
            $this->title            =   $title;
            $this->label            =   !empty($label) ? $label : $title;
            $this->prefix           =   $prefix;
            $this->textdomain       =   !empty($textdomain) ? $textdomain : $prefix;
            $this->metaboxOptions   =   is_array($metabox_options) ? $metabox_options : array();
            $this->parent_slug      =   is_string($parent_slug) && !empty($parent_slug) ? $parent_slug : false;
            $this->capabilities     =   $capabilities;
            $this->hook();
        }
    }

    private function hook() {
        add_action( 'admin_init', array($this,'init')     );
        add_action( 'admin_menu', array($this,'add_page') );
    }

    public function init() {
        register_setting( $this->id, $this->id );
    }

    public function add_page() {
        $options_page = $this->parent_slug ?
            add_submenu_page(
                $this->parent_slug,
                $this->title,
                $this->label,
                $this->capabilities,
                $this->id,
                array($this,'admin_page_display')
            ) :
            add_menu_page(
                $this->title,
                $this->label,
                $this->capabilities,
                $this->id,
                array($this,'admin_page_display')
            );
        add_action( 'admin_head-'.$options_page, array($this,'admin_head') );
    }

    public function admin_head() {
        do_action($this->prefix.'_admin_head');
    }

    public function admin_page_display() {
        ?>
            <div class="wrap">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
                <?php cmb_metabox_form( $this->metaboxOptions, $this->id ); ?>
            </div>
        <?php
    }

}
