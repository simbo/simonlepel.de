<?php

function metabox_multiple_sections( array $meta_boxes ) {

    // id of the custom field, start with an underscore to hide fields from custom fields list
    $id = '_multiple_sections';

    $meta_boxes[$id] = array(
        'id'         => $id.'_metabox',
        'title'      => __('Additional Sections',TXTD),
        'pages'      => array( 'page' ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => false, // hide field names on the left
        'cmb_styles' => true, // enqueue the CMB stylesheet on the frontend
        'fields'     => array(
            array(
                'id'          => $id,
                'type'        => 'group',
                'options'     => array(
                    'add_button'    => __('Add Section',TXTD),
                    'remove_button' => __('Remove Section',TXTD),
                    'sortable'      => true, // beta
                ),
                // field ids only have to be unique within this group
                'fields'      => array(
                    array(
                        'name' => __('Section ID',TXTD),
                        'id'   => 'id',
                        'before' => '<strong><code>#</code></strong>',
                        'type' => 'text_medium',
                        'sanitization_cb' => 'sanitize_key',
                    ),
                    array(
                        'name'    => __('Section Content',TXTD),
                        'id'      => 'content',
                        'type'    => 'wysiwyg',
                        'options' => array(
                            'textarea_rows' => 20,
                            'wpautop' => has_filter('the_content','wpautop') ? true : false,
                        ),
                        'sanitization_cb' => create_function( '$value', 'return trim($value);' )
                    )
                )
            )
        )
    );
    return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'metabox_multiple_sections' );

function metabox_head() {
    ?>
    <style>
        #_multiple_sections_metabox td { padding: 0; }
        #_multiple_sections_metabox td td { padding: 10px 0; }
    </style>
    <script>
        (function($) {
            $(document).ready( function() {
                $('#_multiple_sections_metabox').find('.remove-group-row').click( function(ev) {
                    if( !confirm('Bist du sicher, dass du diese Sektion entfernen möchtest?') ) {
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                });
            });
        })(jQuery);
    </script>
    <?php
}
add_action( 'admin_head-post.php', 'metabox_head');
add_action( 'admin_head-post-new.php', 'metabox_head');
