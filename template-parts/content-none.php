<?php

/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tasmeh_Shop
 */

?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('چیزی یافت نشد', 'tasmeh-shop'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :

            printf(
                '<p>' . wp_kses(
                    /* translators: 1: link to WP admin new post page. */
                    __('آیا می‌خواهید اولین نوشته خود را منتشر کنید؟ <a href="%1$s">از اینجا شروع کنید</a>.', 'tasmeh-shop'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ) . '</p>',
                esc_url(admin_url('post-new.php'))
            );

        elseif (is_search()) :
        ?>

            <p><?php esc_html_e('متاسفانه هیچ نتیجه‌ای با عبارت جستجو شده مطابقت ندارد. لطفا با کلمات کلیدی دیگری جستجو کنید.', 'tasmeh-shop'); ?></p>
        <?php
            get_search_form();

        else :
        ?>

            <p><?php esc_html_e('به نظر می‌رسد نمی‌توانیم آنچه را که به دنبال آن هستید پیدا کنیم. شاید جستجو بتواند کمک کند.', 'tasmeh-shop'); ?></p>
        <?php
            get_search_form();

        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->