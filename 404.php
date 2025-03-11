<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Tasmeh_Shop
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <section class="error-404 not-found">
            <div class="container">
                <div class="error-404-content">
                    <header class="page-header">
                        <h1 class="page-title"><?php esc_html_e('صفحه مورد نظر پیدا نشد!', 'tasmeh-shop'); ?></h1>
                    </header><!-- .page-header -->

                    <div class="page-content">
                        <p><?php esc_html_e('متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد. ممکن است این صفحه حذف شده یا آدرس آن تغییر کرده باشد.', 'tasmeh-shop'); ?></p>

                        <div class="error-404-image">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/404.png'); ?>" alt="<?php esc_attr_e('صفحه پیدا نشد', 'tasmeh-shop'); ?>">
                        </div>

                        <div class="error-404-actions">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-primary"><?php esc_html_e('بازگشت به صفحه اصلی', 'tasmeh-shop'); ?></a>

                            <?php if (class_exists('WooCommerce')) : ?>
                                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="button"><?php esc_html_e('مشاهده محصولات', 'tasmeh-shop'); ?></a>
                            <?php endif; ?>
                        </div>

                        <div class="error-404-search">
                            <h3><?php esc_html_e('جستجو در سایت', 'tasmeh-shop'); ?></h3>
                            <?php get_search_form(); ?>
                        </div>

                        <?php
                        if (class_exists('WooCommerce')) :
                        ?>
                            <div class="error-404-products">
                                <h3><?php esc_html_e('محصولات پیشنهادی', 'tasmeh-shop'); ?></h3>
                                <?php
                                $args = array(
                                    'post_type'      => 'product',
                                    'posts_per_page' => 4,
                                    'orderby'        => 'rand',
                                );
                                $loop = new WP_Query($args);
                                if ($loop->have_posts()) :
                                ?>
                                    <ul class="products columns-4">
                                        <?php
                                        while ($loop->have_posts()) :
                                            $loop->the_post();
                                            wc_get_template_part('content', 'product');
                                        endwhile;
                                        ?>
                                    </ul>
                                <?php
                                    wp_reset_postdata();
                                endif;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div><!-- .page-content -->
                </div>
            </div>
        </section><!-- .error-404 -->

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
?>