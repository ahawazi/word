<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tasmeh_Shop
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;

        if ('post' === get_post_type()) :
        ?>
            <div class="entry-meta">
                <?php
                tasmeh_shop_posted_on();
                tasmeh_shop_posted_by();
                ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php tasmeh_shop_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __('ادامه مطلب<span class="screen-reader-text"> "%s"</span>', 'tasmeh-shop'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );

            wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . esc_html__('صفحات:', 'tasmeh-shop'),
                    'after'  => '</div>',
                )
            );
        else :
            the_excerpt();
        ?>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more">
                <?php esc_html_e('ادامه مطلب', 'tasmeh-shop'); ?>
            </a>
        <?php
        endif;
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php tasmeh_shop_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->