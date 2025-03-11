<?php

/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tasmeh_Shop
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-item'); ?>>
    <div class="row">
        <?php if (has_post_thumbnail()) : ?>
            <div class="col-md-4">
                <div class="search-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                </div>
            </div>
            <div class="col-md-8">
            <?php else : ?>
                <div class="col-md-12">
                <?php endif; ?>

                <header class="entry-header">
                    <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

                    <?php if ('post' === get_post_type()) : ?>
                        <div class="entry-meta">
                            <?php
                            tasmeh_shop_posted_on();
                            tasmeh_shop_posted_by();
                            ?>
                        </div><!-- .entry-meta -->
                    <?php endif; ?>
                </header><!-- .entry-header -->

                <div class="entry-summary">
                    <?php the_excerpt(); ?>
                </div><!-- .entry-summary -->

                <footer class="entry-footer">
                    <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('ادامه مطلب', 'tasmeh-shop'); ?></a>
                </footer><!-- .entry-footer -->
                </div>
            </div>
</article><!-- #post-<?php the_ID(); ?> -->