<footer class="site-footer" role="contentinfo">
    <a href="#top-of-page" class="btn-scroll up" data-duration="1600"><span class="fa fa-angle-up"></span></a>
    <nav class="container">
        <?php
            if( has_nav_menu('footer_navigation') ) :
                wp_nav_menu(array(
                    'theme_location' => 'footer_navigation',
                    'menu_class' => 'list-nav-line'
                ));
            endif;
        ?>
    </nav>
    <div class="showcode">
        <blockquote>
            <p>Talk is cheap. Show me the code.</p>
            <cite>Linus Torwalds</cite>
        </blockquote>
        <a href="https://github.com/simbo/simonlepel.de" class="btn btn-transparent-gray btn-xs">
            <span class="fa fa-code"></span>
            GitHub Repo
        </a>
    </div>
</footer>
