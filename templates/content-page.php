<?php global $post; ?>
<section id="<?php echo $post->post_name; ?>">
    <?php edit_post_link( '<span class="fa fa-pencil-square-o"></span>', '<div class="entry-edit">', '</div>' ); ?>
    <div class="container">
        <?php the_content(); ?>
    </div>
</section>
<?php
/* ==========================================================================
   Additional Sections
   ========================================================================== */
$sections = get_post_meta( $post->ID, '_multiple_sections', true );
foreach( (array) $sections as $key => $section ) :
    $section = wp_parse_args( $section, array(
        'id' => '',
        'content' => ''
    ));
    if( !empty($section['content']) ) :
        $section['content'] = apply_filters( 'the_content', $section['content'] );
        ?>
        <section<?php echo !empty($section['id'])?' id="'.$section['id'].'"':''; ?>>
            <div class="container">
                <?php edit_post_link( '<span class="fa fa-pencil-square-o"></span>', '<div class="entry-edit">', '</div>' ); ?>
                <?php echo $section['content']; ?>
            </div>
        </section>
        <?php
    endif;
endforeach;
