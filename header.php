<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tasmeh_Shop
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'tasmeh-shop'); ?></a>

        <header id="masthead" class="site-header">
            <div class="header-top">
                <div class="container">
                    <div class="header-top-content">
                        <div class="header-contact">
                            <span><i class="fas fa-phone-alt"></i> <?php echo esc_html(get_theme_mod('tasmeh_shop_phone', '021-12345678')); ?></span>
                            <span><i class="fas fa-envelope"></i> <?php echo esc_html(get_theme_mod('tasmeh_shop_email', 'info@example.com')); ?></span>
                        </div>
                        <div class="header-user-actions">
                            <?php if (is_user_logged_in()) : ?>
                                <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>"><i class="fas fa-user"></i> حساب کاربری</a>
                                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>"><i class="fas fa-sign-out-alt"></i> خروج</a>
                            <?php else : ?>
                                <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>"><i class="fas fa-user"></i> ورود / ثبت نام</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="header-main">
                    <div class="site-branding">
                        <?php
                        the_custom_logo();
                        if (is_front_page() && is_home()) :
                        ?>
                            <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                        <?php
                        else :
                        ?>
                            <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
                        <?php
                        endif;
                        $tasmeh_shop_description = get_bloginfo('description', 'display');
                        if ($tasmeh_shop_description || is_customize_preview()) :
                        ?>
                            <p class="site-description"><?php echo $tasmeh_shop_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                                        ?></p>
                        <?php endif; ?>
                    </div><!-- .site-branding -->

                    <div class="search-form">
                        <?php get_search_form(); ?>
                    </div>

                    <div class="header-cart">
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                        </a>
                    </div>
                </div><!-- .header-main -->
            </div><!-- .container -->

            <nav id="site-navigation" class="main-navigation">
                <div class="container nav-container">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'menu_class'     => 'main-menu',
                            'fallback_cb'    => false,
                        )
                    );
                    ?>
                    <?php if (get_theme_mod('tasmeh_shop_show_categories_menu', true)) : ?>
                        <div class="categories-menu-wrapper">
                            <button class="categories-toggle"><i class="fas fa-bars"></i> دسته‌بندی‌ها</button>
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'categories',
                                    'menu_id'        => 'categories-menu',
                                    'container'      => false,
                                    'menu_class'     => 'categories-menu',
                                    'fallback_cb'    => false,
                                )
                            );
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </nav><!-- #site-navigation -->
        </header><!-- #masthead -->

        <div id="content" class="site-content">
            <div class="container">
                <div class="content-wrapper"><?php // The page content will be added here 
                                                ?>