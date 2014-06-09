<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>
    <div id="top-of-page"></div>
    <?php if( display_header() ) : ?>
        <?php get_template_part('templates/header-top-navbar'); ?>
    <?php else : ?>
        <header class="sr-only">
            <h1 class="site-title"><?php bloginfo('name'); ?></h1>
        </header>
    <?php endif; ?>
    <div class="content <?php echo theme_content_class() ?>" role="document">
        <!--[if lt IE 9]>
            <div class="old-browser alert alert-danger">
                <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
            </div>
        <![endif]-->
        <?php if( display_sidebar() ) : ?>
            <div class="row">
        <?php endif; ?>
        <main class="main <?php echo theme_main_class(); ?>" role="main">
            <?php include roots_template_path(); ?>
        </main>
        <?php if( display_sidebar() ) : ?>
                <aside class="sidebar <?php echo theme_sidebar_class(); ?>" role="complementary">
                    <?php include roots_sidebar_path(); ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
    <?php if( display_footer() ) : ?>
        <?php get_template_part('templates/footer'); ?>
    <?php endif; ?>
    <?php wp_footer(); ?>
</body>
</html>
