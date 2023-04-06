<?php
/**
 * The current version of the theme.
 */
$the_theme = wp_get_theme();
define('ENVO_MARKETPLACE_VERSION', $the_theme->get( 'Version' ));

add_action('after_setup_theme', 'envo_marketplace_setup');

if (!function_exists('envo_marketplace_setup')) :

    /**
     * Global functions
     */
    function envo_marketplace_setup() {

        // Theme lang.
        load_theme_textdomain('envo-marketplace', get_template_directory() . '/languages');

        // Add Title Tag Support.
        add_theme_support('title-tag');
        $menus = array('main_menu' => esc_html__('Main Menu', 'envo-marketplace'));
        if (class_exists('WooCommerce')) {
            $woo_menus = array(
                'main_menu_right' => esc_html__('Menu Right', 'envo-marketplace'),
                'main_menu_cats' => esc_html__('Categories Menu', 'envo-marketplace'),
            );
        } else {
            $woo_menus = array(); // not displayed if Woo not installed
        }
        $all_menus = array_merge($menus, $woo_menus);

        // Register Menus.
        register_nav_menus($all_menus);

        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(300, 300, true);
        add_image_size('envo-marketplace-single', 1140, 641, true);
        add_image_size('envo-marketplace-med', 720, 405, true);

        // Add Custom Background Support.
        $args = array(
            'default-color' => 'ffffff',
        );
        add_theme_support('custom-background', $args);

        add_theme_support('custom-logo', array(
            'height' => 60,
            'width' => 200,
            'flex-height' => true,
            'flex-width' => true,
            'header-text' => array('site-title', 'site-description'),
        ));

        // Adds RSS feed links to for posts and comments.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         */
        add_theme_support('title-tag');

        // Set the default content width.
        $GLOBALS['content_width'] = 1140;

        add_theme_support('custom-header', apply_filters('envo_marketplace_custom_header_args', array(
            'width' => 2000,
            'height' => 200,
            'default-text-color' => '',
            'wp-head-callback' => 'envo_marketplace_header_style',
        )));

        // WooCommerce support.
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        add_theme_support('html5', array('search-form'));
        /*
         * This theme styles the visual editor to resemble the theme style,
         * specifically font, colors, icons, and column width.
         */
        add_editor_style(array('css/bootstrap.css', envo_marketplace_fonts_url(), 'css/editor-style.css'));

        // Recommend plugins.
        add_theme_support('recommend-plugins', array(
            'envo-extra' => array(
                'name' => 'Envo Extra',
                'active_filename' => 'envo-extra/envo-extra.php',
                'description' => esc_html__('Save time by importing our demo data: your website will be set up and ready to be customized in minutes.', 'envo-marketplace'),
            ),
            'woocommerce' => array(
                'name' => 'WooCommerce',
                'active_filename' => 'woocommerce/woocommerce.php',
                /* translators: %s plugin name string */
                'description' => sprintf(esc_attr__('To enable shop features, please install and activate the %s plugin.', 'envo-marketplace'), '<strong>WooCommerce</strong>'),
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'active_filename' => 'elementor/elementor.php',
                /* translators: %s plugin name string */
                'description' => sprintf(esc_attr__('The most advanced frontend drag & drop page builder.', 'envo-marketplace'), '<strong>Elementor</strong>'),
            ),
        ));
    }

endif;

if (!function_exists('envo_marketplace_header_style')) :

    /**
     * Styles the header image and text displayed on the blog.
     */
    function envo_marketplace_header_style() {
        $header_image = get_header_image();
        $header_text_color = get_header_textcolor();
        if (get_theme_support('custom-header', 'default-text-color') !== $header_text_color || !empty($header_image)) {
            ?>
            <style type="text/css" id="envo-marketplace-header-css">
            <?php
            // Has a Custom Header been added?
            if (!empty($header_image)) :
                ?>
                    .site-header {
                        background-image: url(<?php header_image(); ?>);
                        background-repeat: no-repeat;
                        background-position: 50% 50%;
                        -webkit-background-size: cover;
                        -moz-background-size:    cover;
                        -o-background-size:      cover;
                        background-size:         cover;
                    }
            <?php endif; ?>	
            <?php
            // Has the text been hidden?
            if ('blank' === $header_text_color) :
                ?>
                    .site-title,
                    .site-description {
                        position: absolute;
                        clip: rect(1px, 1px, 1px, 1px);
                    }
            <?php elseif ('' !== $header_text_color) : ?>
                    .site-title a, 
                    .site-title, 
                    .site-description {
                        color: #<?php echo esc_attr($header_text_color); ?>;
                    }
            <?php endif; ?>	
            </style>
            <?php
        }
    }

endif; // envo_marketplace_header_style

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function envo_marketplace_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}

add_action('wp_head', 'envo_marketplace_pingback_header');

/**
 * Set Content Width
 */
function envo_marketplace_content_width() {

    $content_width = $GLOBALS['content_width'];

    if (is_active_sidebar('envo-marketplace-right-sidebar')) {
        $content_width = 847;
    } else {
        $content_width = 1140;
    }

    /**
     * Filter content width of the theme.
     */
    $GLOBALS['content_width'] = apply_filters('envo_marketplace_content_width', $content_width);
}

add_action('template_redirect', 'envo_marketplace_content_width', 0);

/**
 * Register custom fonts.
 */
function envo_marketplace_fonts_url() {
    $fonts_url = '';

    /**
     * Translators: If there are characters in your language that are not
     * supported by Open Sans Condensed, translate this to 'off'. Do not translate
     * into your own language.
     */
    $font = _x('on', 'Open Sans Condensed font: on or off', 'envo-marketplace');

    if ('off' !== $font) {
        $font_families = array();

        $font_families[] = 'Open Sans Condensed:300,500,700';

        $query_args = array(
            'family' => urlencode(implode('|', $font_families)),
            'subset' => urlencode('cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese'),
        );

        $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }

    return esc_url_raw($fonts_url);
}

/**
 * Add preconnect for Google Fonts.
 */
function envo_marketplace_resource_hints($urls, $relation_type) {
    if (wp_style_is('envo-marketplace-fonts', 'queue') && 'preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }

    return $urls;
}

add_filter('wp_resource_hints', 'envo_marketplace_resource_hints', 10, 2);

/**
 * Enqueue Styles (normal style.css and bootstrap.css)
 */
function envo_marketplace_theme_stylesheets() {
    // Add custom fonts, used in the main stylesheet.
    wp_enqueue_style('envo-marketplace-fonts', envo_marketplace_fonts_url(), array(), null);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), '3.3.7');
    wp_enqueue_style('mmenu-light', get_template_directory_uri() . '/css/mmenu-light.min.css', array(), ENVO_MARKETPLACE_VERSION);
    // Theme stylesheet.
    wp_enqueue_style('envo-marketplace-stylesheet', get_stylesheet_uri(), array('bootstrap'), ENVO_MARKETPLACE_VERSION);
    // WooCommerce stylesheet.
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('envo-marketplace-woo-stylesheet', get_template_directory_uri() . '/css/woocommerce.css', array('envo-marketplace-stylesheet'), ENVO_MARKETPLACE_VERSION);
    }
    // Load Line Awesome css.
    wp_enqueue_style('line-awesome', get_template_directory_uri() . '/css/line-awesome.min.css', array(), '1.3.0');
}

add_action('wp_enqueue_scripts', 'envo_marketplace_theme_stylesheets');

/**
 * Register jquery
 */
function envo_marketplace_theme_js() {
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.3.7', true);
    wp_enqueue_script('envo-marketplace-theme-js', get_template_directory_uri() . '/js/customscript.js', array('jquery'), ENVO_MARKETPLACE_VERSION, true);
    wp_enqueue_script('mmenu', get_template_directory_uri() . '/js/mmenu-light.min.js', array('jquery'), ENVO_MARKETPLACE_VERSION, true);
}

add_action('wp_enqueue_scripts', 'envo_marketplace_theme_js');

if (!function_exists('envo_marketplace_is_pro_activated')) {

    /**
     * Query Envo Marketplace activation
     */
    function envo_marketplace_is_pro_activated() {
        return defined('ENVO_MARKETPLACE_PRO_CURRENT_VERSION') ? true : false;
    }

}

if (!function_exists('envo_marketplace_title_logo')) {

    /**
     * Title, logo code
     */
    function envo_marketplace_title_logo($h = 'h1') {
        ?>
        <div class="site-branding-logo">
            <?php the_custom_logo(); ?>
        </div>
        <div class="site-branding-text">
            <?php if (is_front_page()) : ?>
                <<?php echo esc_html($h) ?> class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></<?php echo esc_html($h) ?>>
            <?php else : ?>
                <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
            <?php endif; ?>

            <?php
            $description = get_bloginfo('description', 'display');
            if ($description || is_customize_preview()) :
                ?>
                <p class="site-description">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>
        </div><!-- .site-branding-text -->
        <?php
    }

}

/**
 * Register Custom Navigation Walker include custom menu widget to use walkerclass
 */
require_once( trailingslashit(get_template_directory()) . 'lib/wp_bootstrap_navwalker.php' );

/**
 * Register Theme Info Page
 */
require_once( trailingslashit(get_template_directory()) . 'lib/dashboard.php' );
/**
 * Customizer options
 */
require_once( trailingslashit(get_template_directory()) . 'lib/customizer.php' );

if (class_exists('WooCommerce')) {

    /**
     * WooCommerce options
     */
    require_once( trailingslashit(get_template_directory()) . 'lib/woocommerce.php' );
}

add_action('widgets_init', 'envo_marketplace_widgets_init');

/**
 * Register the Sidebar(s)
 */
function envo_marketplace_widgets_init() {
    register_sidebar(
            array(
                'name' => esc_html__('Sidebar', 'envo-marketplace'),
                'id' => 'envo-marketplace-right-sidebar',
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<div class="widget-title"><h3>',
                'after_title' => '</h3></div>',
            )
    );
    register_sidebar(
            array(
                'name' => esc_html__('Top Bar Section', 'envo-marketplace'),
                'id' => 'envo-marketplace-top-bar-area',
                'before_widget' => '<div id="%1$s" class="widget %2$s col-sm-4">',
                'after_widget' => '</div>',
                'before_title' => '<div class="widget-title"><h3>',
                'after_title' => '</h3></div>',
            )
    );
    register_sidebar(
            array(
                'name' => esc_html__('Header Section', 'envo-marketplace'),
                'id' => 'envo-marketplace-header-area',
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<div class="widget-title"><h3>',
                'after_title' => '</h3></div>',
            )
    );
    register_sidebar(
            array(
                'name' => esc_html__('Footer Section', 'envo-marketplace'),
                'id' => 'envo-marketplace-footer-area',
                'before_widget' => '<div id="%1$s" class="widget %2$s col-md-3">',
                'after_widget' => '</div>',
                'before_title' => '<div class="widget-title"><h3>',
                'after_title' => '</h3></div>',
            )
    );
}

/**
 * Set the content width based on enabled sidebar
 */
function envo_marketplace_main_content_width_columns() {

    $columns = '12';

    if (is_active_sidebar('envo-marketplace-right-sidebar')) {
        $columns = $columns - 3;
    }

    echo absint($columns);
}

if (!function_exists('envo_marketplace_entry_footer')) :

    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    add_action('envo_marketplace_construct_entry_footer', 'envo_marketplace_entry_footer');

    function envo_marketplace_entry_footer() {

        // Get Categories for posts.
        $categories_list = get_the_category_list(' ');

        // Get Tags for posts.
        $tags_list = get_the_tag_list('', ' ');

        // We don't want to output .entry-footer if it will be empty, so make sure its not.
        if ($categories_list || $tags_list || get_edit_post_link()) {

            echo '<div class="entry-footer">';

            if ('post' === get_post_type()) {
                if ($categories_list || $tags_list) {

                    // Make sure there's more than one category before displaying.
                    if ($categories_list) {
                        echo '<div class="cat-links"><span class="space-right">' . esc_html__('Category', 'envo-marketplace') . '</span>' . wp_kses_data($categories_list) . '</div>';
                    }

                    if ($tags_list) {
                        echo '<div class="tags-links"><span class="space-right">' . esc_html__('Tags', 'envo-marketplace') . '</span>' . wp_kses_data($tags_list) . '</div>';
                    }
                }
            }

            edit_post_link();

            echo '</div>';
        }
    }

endif;

if (!function_exists('envo_marketplace_generate_construct_footer_widgets')) :
    /**
     * Build footer widgets
     */
    add_action('envo_marketplace_generate_footer', 'envo_marketplace_generate_construct_footer_widgets', 10);

    function envo_marketplace_generate_construct_footer_widgets() {
        if (is_active_sidebar('envo-marketplace-footer-area')) {
            ?>  				
            <div id="content-footer-section" class="container-fluid clearfix">
                <div class="container">
                    <?php dynamic_sidebar('envo-marketplace-footer-area'); ?>
                </div>	
            </div>		
        <?php
        }
    }

endif;

if (!function_exists('envo_marketplace_generate_construct_footer')) :
    /**
     * Build footer
     */
    add_action('envo_marketplace_generate_footer', 'envo_marketplace_generate_construct_footer', 20);

    function envo_marketplace_generate_construct_footer() {
        ?>
        <footer id="colophon" class="footer-credits container-fluid">
            <div class="container">    
                <div class="footer-credits-text text-center">
                    <?php
                    /* translators: %s: WordPress name with wordpress.org URL */
                    printf(esc_html__('Proudly powered by %s', 'envo-marketplace'), '<a href="' . esc_url(__('https://wordpress.org/', 'envo-marketplace')) . '">' . esc_html__('WordPress', 'envo-marketplace') . '</a>');
                    ?>
                    <span class="sep"> | </span>
                    <?php
                    /* translators: %1$s: Envo Marketplace theme name (do not translate) with envothemes.com URL */
                    printf(esc_html__('Theme: %1$s', 'envo-marketplace'), '<a href="' . esc_url('https://envothemes.com/free-envo-marketplace/') . '">' . esc_html_x('Envo Marketplace', 'Theme name, do not translate', 'envo-marketplace') . '</a>');
                    ?>
                </div>
            </div>	
        </footer>
        <?php
    }

endif;

if (!function_exists('envo_marketplace_date')) :

    /**
     * Returns date.
     */
    add_action('envo_marketplace_after_title', 'envo_marketplace_date', 10);

    function envo_marketplace_date() {
        ?>
        <span class="posted-date">
            <?php echo esc_html(get_the_date()); ?>
        </span>
        <?php
    }

endif;

if (!function_exists('envo_marketplace_author_meta')) :

    /**
     * Post author meta funciton
     */
    add_action('envo_marketplace_after_title', 'envo_marketplace_author_meta', 20);

    function envo_marketplace_author_meta() {
        ?>
        <span class="author-meta">
            <span class="author-meta-by"><?php esc_html_e('By', 'envo-marketplace'); ?></span>
            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename'))); ?>">
                <?php the_author(); ?>
            </a>
        </span>
        <?php
    }

endif;

if (!function_exists('envo_marketplace_comments')) :

    /**
     * Returns comments.
     */
    add_action('envo_marketplace_after_title', 'envo_marketplace_comments', 30);

    function envo_marketplace_comments() {
        ?>
        <span class="comments-meta">
            <?php
            if (!comments_open()) {
                esc_html_e('Off', 'envo-marketplace');
            } else {
                ?>
                <a href="<?php the_permalink(); ?>#comments" rel="nofollow" title="<?php esc_attr_e('Comment on ', 'envo-marketplace') . the_title_attribute(); ?>">
                <?php echo absint(get_comments_number()); ?>
                </a>
            <?php } ?>
            <i class="la la-comments-o"></i>
        </span>
        <?php
    }

endif;

if (!function_exists('envo_marketplace_post_author')) :

    /**
     * Returns post author
     */
    add_action('envo_marketplace_construct_post_author', 'envo_marketplace_post_author');

    function envo_marketplace_post_author() {
        ?>
        <div class="postauthor-container">			  
            <div class="postauthor-title">
                <h4 class="about">
                    <?php esc_html_e('About The Author', 'envo-marketplace'); ?>
                </h4>
                <div class="">
                    <span class="fn">
                        <?php the_author_posts_link(); ?>
                    </span>
                </div> 				
            </div>        	
            <div class="postauthor-content">	             						           
                <p>
                    <?php the_author_meta('description') ?>
                </p>					
            </div>	 		
        </div>
        <?php
    }

endif;

if (!function_exists('envo_marketplace_breadcrumbs')) :

    /**
     * Returns yoast breadcrumbs
     */
    add_action('envo_marketplace_page_area', 'envo_marketplace_breadcrumbs');

    function envo_marketplace_breadcrumbs() {
        if (function_exists('yoast_breadcrumb') && (!is_home() && !is_front_page() )) {
            yoast_breadcrumb('<p id="breadcrumbs" class="text-left">', '</p>');
        }
    }

endif;

if (!function_exists('envo_marketplace_top_bar')) :

    /**
     * Returns top bar
     */
    add_action('envo_marketplace_construct_top_bar', 'envo_marketplace_top_bar');

    function envo_marketplace_top_bar() {
        if (is_active_sidebar('envo-marketplace-top-bar-area')) { ?>
            <div class="top-bar-section container-fluid">
                <div class="<?php echo esc_attr(get_theme_mod('top_bar_content_width', 'container')); ?>">
                    <div class="row">
                        <?php dynamic_sidebar('envo-marketplace-top-bar-area'); ?>
                    </div>
                </div>
            </div>
        <?php }
    }

endif;

if (!function_exists('envo_marketplace_generate_construct_the_content')) :
    /**
     * Build footer widgets
     */
    add_action('envo_marketplace_generate_the_content', 'envo_marketplace_generate_construct_the_content');

    function envo_marketplace_generate_construct_the_content() {
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('content', get_post_format());
            endwhile;
            the_posts_pagination();
        else :
            get_template_part('content', 'none');
        endif;
    }

endif;

if (!function_exists('envo_marketplace_generate_construct_author_comments')) :
    /**
     * Build author and comments area
     */
    add_action('envo_marketplace_after_single_post', 'envo_marketplace_generate_construct_author_comments');

    function envo_marketplace_generate_construct_author_comments() {
        $authordesc = get_the_author_meta('description');
        if (!empty($authordesc)) {
            ?>
            <div class="single-footer row">
                <div class="col-md-4">
                    <?php do_action('envo_marketplace_construct_post_author'); ?> 
                </div>
                <div class="col-md-8">
                    <?php comments_template(); ?> 
                </div>
            </div>
        <?php } else { ?>
            <div class="single-footer">
                <?php comments_template(); ?> 
            </div>
        <?php }
    }

endif;

if (!function_exists('envo_marketplace_excerpt_length')) :

    /**
     * Excerpt limit.
     */
    function envo_marketplace_excerpt_length($length) {
        return 45;
    }

    add_filter('excerpt_length', 'envo_marketplace_excerpt_length', 999);

endif;

if (!function_exists('envo_marketplace_excerpt_more')) :

    /**
     * Excerpt more.
     */
    function envo_marketplace_excerpt_more($more) {
        return '&hellip;';
    }

    add_filter('excerpt_more', 'envo_marketplace_excerpt_more');

endif;

if (!function_exists('envo_marketplace_thumb_img')) :

    /**
     * Returns widget thumbnail.
     */
    function envo_marketplace_thumb_img($img = 'full', $col = '', $link = true, $single = false) {
        if (function_exists('envo_marketplace_pro_thumb_img')) {
            envo_marketplace_pro_thumb_img($img, $col, $link, $single);
        } elseif (( has_post_thumbnail() && $link == true)) {
            ?>
            <div class="news-thumb <?php echo esc_attr($col); ?>">
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
            <?php the_post_thumbnail($img); ?>
                </a>
            </div><!-- .news-thumb -->
            <?php } elseif (has_post_thumbnail()) { ?>
            <div class="news-thumb <?php echo esc_attr($col); ?>">
            <?php the_post_thumbnail($img); ?>
            </div><!-- .news-thumb -->	
            <?php
        }
    }

endif;

/**
 * Single previous next links
 */
if (!function_exists('envo_marketplace_prev_next_links')) :

    function envo_marketplace_prev_next_links() {
        the_post_navigation(
            array(
                'prev_text' => '<span class="screen-reader-text">' . __('Previous Post', 'envo-marketplace') . '</span><span aria-hidden="true" class="nav-subtitle">' . __('Previous', 'envo-marketplace') . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper"><i class="la la-angle-double-left" aria-hidden="true"></i></span>%title</span>',
                'next_text' => '<span class="screen-reader-text">' . __('Next Post', 'envo-marketplace') . '</span><span aria-hidden="true" class="nav-subtitle">' . __('Next', 'envo-marketplace') . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper"><i class="la la-angle-double-right" aria-hidden="true"></i></span></span>',
            )
        );
    }

endif;

if (!function_exists('wp_body_open')) :

    /**
     * Fire the wp_body_open action.
     *
     * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
     *
     */
    function wp_body_open() {
        /**
         * Triggered after the opening <body> tag.
         *
         */
        do_action('wp_body_open');
    }

endif;

/**
 * Skip to content link
 */
function envo_marketplace_skip_link() {
    echo '<a class="skip-link screen-reader-text" href="#site-content">' . esc_html__('Skip to the content', 'envo-marketplace') . '</a>';
}

add_action('wp_body_open', 'envo_marketplace_skip_link', 5);

function envo_marketplace_second_menu() {
    $class = '';
    if (class_exists('WooCommerce')) {
        $class .= 'search-on ';
    }
    if (has_nav_menu('main_menu_cats')) {
        $class .= 'menu-cats-on ';
    }
    if (has_nav_menu('main_menu_right')) {
        $class .= 'menu-right-on ';
    }
    echo esc_html($class);
}
