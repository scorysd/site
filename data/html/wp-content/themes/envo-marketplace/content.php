<article class="row content-article">
    <div <?php post_class(); ?>>                    
        <div class="news-item <?php echo esc_attr(has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail' ) ?>">
            <?php envo_marketplace_thumb_img('envo-marketplace-med', 'col-md-6'); ?>
            <div class="news-text-wrap <?php echo esc_attr(has_post_thumbnail() ? 'col-md-6' : 'col-md-12' ) ?>">
                <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
                <?php do_action('envo_marketplace_after_title'); ?>
                <div class="post-excerpt">
                    <?php the_excerpt(); ?>
                </div><!-- .post-excerpt -->
            </div><!-- .news-text-wrap -->
        </div><!-- .news-item -->
    </div>
</article>
