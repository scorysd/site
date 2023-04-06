<?php get_header(); ?>

<!-- start content container -->
<div class="row">      
    <article class="col-md-<?php envo_marketplace_main_content_width_columns(); ?>">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>                         
                <div <?php post_class(); ?>>
                    <?php envo_marketplace_thumb_img('envo-marketplace-single', '', false, true); ?>
                    <div class="single-head <?php echo esc_attr(has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail' ) ?>">
                        <?php the_title('<h1 class="single-title">', '</h1>'); ?>
                        <?php do_action('envo_marketplace_after_title'); ?>
                    </div>
                    <div class="single-content">
                        <div class="single-entry-summary">
                            <?php do_action('envo_marketplace_before_content'); ?> 
                            <?php the_content(); ?>
                            <?php do_action('envo_marketplace_after_content'); ?> 
                        </div><!-- .single-entry-summary -->
                        <?php wp_link_pages(); ?>
                        <?php do_action('envo_marketplace_construct_entry_footer'); ?>
                    </div>
                    <?php envo_marketplace_prev_next_links(); ?>
                    <?php do_action('envo_marketplace_after_single_post'); ?>
                </div>        
            <?php endwhile; ?>        
        <?php else : ?>            
            <?php get_template_part('content', 'none'); ?>        
        <?php endif; ?>    
    </article> 
    <?php get_sidebar('right'); ?>
</div>
<!-- end content container -->

<?php 
get_footer();
