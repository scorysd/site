<?php do_action('envo_marketplace_construct_top_bar'); ?>
<div class="site-header container-fluid">
    <div class="<?php echo esc_attr(get_theme_mod('header_content_width', 'container')); ?>" >
        <div class="heading-row row" >
            <div class="site-heading <?php echo esc_attr(class_exists('WooCommerce') == true ? 'col-md-3' : 'col-md-4' ); ?>" >
                <?php envo_marketplace_title_logo(); ?>
            </div>
            <div class="menu-heading <?php echo esc_attr(class_exists('WooCommerce') == true ? 'col-md-6' : 'col-md-8' ); ?>">
                <nav id="site-navigation" class="navbar navbar-default">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'main_menu',
                        'depth' => 5,
                        'container_id' => 'my-menu',
                        'container' => 'div',
                        'container_class' => 'menu-container',
                        'menu_class' => 'nav navbar-nav navbar-left',
                        'fallback_cb' => 'Envo_Marketplace_WP_Bootstrap_Navwalker::fallback',
                        'walker' => new Envo_Marketplace_WP_Bootstrap_Navwalker(),
                    ));
                    ?>
                </nav>    
                <?php if (is_active_sidebar('envo-marketplace-header-area')) { ?>
                    <div class="site-heading-sidebar" >
                        <?php dynamic_sidebar('envo-marketplace-header-area'); ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (class_exists('WooCommerce')) { ?>
                <div class="header-right col-md-3" >
                    <?php do_action('envo_marketplace_header_right'); ?>
                </div>
            <?php } ?>
            <div class="header-right menu-button visible-xs" >
                <div class="navbar-header">
                    <?php if (function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled('main_menu')) :  // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
                        // do nothing 
                    else : ?>
                        <span class="navbar-brand brand-absolute visible-xs"><?php esc_html_e('Menu', 'envo-marketplace'); ?></span>
                        <a href="#" id="main-menu-panel" class="open-panel" data-panel="main-menu-panel">
                            <span></span>
                            <span></span>
                            <span></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
do_action('envo_marketplace_before_second_menu'); 
if (class_exists('WooCommerce')) { 
?>
    <div class="main-menu">
        <nav id="second-site-navigation" class="navbar navbar-default <?php envo_marketplace_second_menu(); ?>">
            <div class="container">   
                <?php do_action('envo_marketplace_header_bar'); ?>
            </div>
        </nav> 
    </div>
<?php 
do_action('envo_marketplace_after_second_menu');
}
