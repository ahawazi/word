<?php

/**
 * Custom functions for Tasmeh Shop theme
 *
 * @package Tasmeh_Shop
 */

/**
 * Get theme option
 *
 * @param string $option Option name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function tasmeh_shop_get_option($option, $default = '')
{
    $value = get_theme_mod($option, $default);
    return $value;
}

/**
 * Display social media icons
 */
function tasmeh_shop_social_icons()
{
    $instagram = tasmeh_shop_get_option('tasmeh_shop_instagram', '#');
    $telegram  = tasmeh_shop_get_option('tasmeh_shop_telegram', '#');
    $twitter   = tasmeh_shop_get_option('tasmeh_shop_twitter', '#');
    $linkedin  = tasmeh_shop_get_option('tasmeh_shop_linkedin', '#');

    if (!empty($instagram) || !empty($telegram) || !empty($twitter) || !empty($linkedin)) {
        echo '<div class="social-icons">';

        if (!empty($instagram)) {
            echo '<a href="' . esc_url($instagram) . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>';
        }

        if (!empty($telegram)) {
            echo '<a href="' . esc_url($telegram) . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-telegram"></i></a>';
        }

        if (!empty($twitter)) {
            echo '<a href="' . esc_url($twitter) . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>';
        }

        if (!empty($linkedin)) {
            echo '<a href="' . esc_url($linkedin) . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin"></i></a>';
        }

        echo '</div>';
    }
}

/**
 * Display copyright text
 */
function tasmeh_shop_copyright_text()
{
    $default = sprintf(esc_html__('© %s فروشگاه تسمه. تمامی حقوق محفوظ است.', 'tasmeh-shop'), date('Y'));
    $copyright = tasmeh_shop_get_option('tasmeh_shop_copyright_text', $default);

    echo '<div class="copyright">';
    echo wp_kses_post($copyright);
    echo '</div>';
}

/**
 * Display payment methods
 */
function tasmeh_shop_payment_methods()
{
    echo '<div class="payment-methods">';
    echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/payment-methods.png') . '" alt="' . esc_attr__('روش‌های پرداخت', 'tasmeh-shop') . '">';
    echo '</div>';
}

/**
 * Display mobile menu toggle button
 */
function tasmeh_shop_mobile_menu_toggle()
{
?>
    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
        <span class="menu-toggle-icon">
            <span class="line line-1"></span>
            <span class="line line-2"></span>
            <span class="line line-3"></span>
        </span>
        <span class="screen-reader-text"><?php esc_html_e('منو', 'tasmeh-shop'); ?></span>
    </button>
<?php
}

/**
 * Display search form toggle button
 */
function tasmeh_shop_search_toggle()
{
?>
    <button class="search-toggle">
        <i class="fas fa-search"></i>
        <span class="screen-reader-text"><?php esc_html_e('جستجو', 'tasmeh-shop'); ?></span>
    </button>
<?php
}

/**
 * Display header contact information
 */
function tasmeh_shop_header_contact()
{
    $phone = tasmeh_shop_get_option('tasmeh_shop_phone', '021-12345678');
    $email = tasmeh_shop_get_option('tasmeh_shop_email', 'info@example.com');

    if (!empty($phone) || !empty($email)) {
        echo '<div class="header-contact">';

        if (!empty($phone)) {
            echo '<span><i class="fas fa-phone-alt"></i> ' . esc_html($phone) . '</span>';
        }

        if (!empty($email)) {
            echo '<span><i class="fas fa-envelope"></i> ' . esc_html($email) . '</span>';
        }

        echo '</div>';
    }
}

/**
 * Display header user actions
 */
function tasmeh_shop_header_user_actions()
{
    echo '<div class="header-user-actions">';

    if (class_exists('WooCommerce')) {
        if (is_user_logged_in()) {
            echo '<a href="' . esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))) . '"><i class="fas fa-user"></i> ' . esc_html__('حساب کاربری', 'tasmeh-shop') . '</a>';
            echo '<a href="' . esc_url(wp_logout_url(home_url())) . '"><i class="fas fa-sign-out-alt"></i> ' . esc_html__('خروج', 'tasmeh-shop') . '</a>';
        } else {
            echo '<a href="' . esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))) . '"><i class="fas fa-user"></i> ' . esc_html__('ورود / ثبت نام', 'tasmeh-shop') . '</a>';
        }
    } else {
        if (is_user_logged_in()) {
            echo '<a href="' . esc_url(admin_url('profile.php')) . '"><i class="fas fa-user"></i> ' . esc_html__('پروفایل', 'tasmeh-shop') . '</a>';
            echo '<a href="' . esc_url(wp_logout_url(home_url())) . '"><i class="fas fa-sign-out-alt"></i> ' . esc_html__('خروج', 'tasmeh-shop') . '</a>';
        } else {
            echo '<a href="' . esc_url(wp_login_url()) . '"><i class="fas fa-user"></i> ' . esc_html__('ورود', 'tasmeh-shop') . '</a>';
            echo '<a href="' . esc_url(wp_registration_url()) . '"><i class="fas fa-user-plus"></i> ' . esc_html__('ثبت نام', 'tasmeh-shop') . '</a>';
        }
    }

    echo '</div>';
}

/**
 * Display product price with discount percentage
 *
 * @param string $price HTML price.
 * @param object $product WC_Product object.
 * @return string
 */
function tasmeh_shop_price_with_discount($price, $product)
{
    if (is_admin() || !$product->is_on_sale() || $product->is_type('variable')) {
        return $price;
    }

    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();

    if ($regular_price == 0) {
        return $price;
    }

    $percentage = round(100 - ($sale_price / $regular_price * 100));

    return $price . '<span class="discount-percentage">٪' . tasmeh_shop_en_to_persian_num($percentage) . ' تخفیف</span>';
}
add_filter('woocommerce_get_price_html', 'tasmeh_shop_price_with_discount', 10, 2);

/**
 * Add custom image sizes for theme
 */
function tasmeh_shop_add_image_sizes()
{
    add_image_size('tasmeh-shop-slider', 1600, 800, true);
    add_image_size('tasmeh-shop-featured', 800, 600, true);
    add_image_size('tasmeh-shop-square', 600, 600, true);
    add_image_size('tasmeh-shop-medium', 400, 300, true);
}
add_action('after_setup_theme', 'tasmeh_shop_add_image_sizes');

/**
 * Add custom image sizes to media library
 *
 * @param array $sizes Image sizes.
 * @return array
 */
function tasmeh_shop_custom_image_sizes($sizes)
{
    return array_merge($sizes, array(
        'tasmeh-shop-slider'   => esc_html__('اسلایدر تسمه', 'tasmeh-shop'),
        'tasmeh-shop-featured' => esc_html__('ویژه تسمه', 'tasmeh-shop'),
        'tasmeh-shop-square'   => esc_html__('مربع تسمه', 'tasmeh-shop'),
        'tasmeh-shop-medium'   => esc_html__('متوسط تسمه', 'tasmeh-shop'),
    ));
}
add_filter('image_size_names_choose', 'tasmeh_shop_custom_image_sizes');

/**
 * Add custom body classes
 *
 * @param array $classes Body classes.
 * @return array
 */
function tasmeh_shop_custom_body_classes($classes)
{
    // Add a class if RTL
    if (is_rtl()) {
        $classes[] = 'rtl';
    }

    // Add a class for the home page
    if (is_front_page()) {
        $classes[] = 'home-page';
    }

    // Add a class for WooCommerce pages
    if (class_exists('WooCommerce')) {
        if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
            $classes[] = 'woocommerce-page';
        }

        if (is_shop()) {
            $classes[] = 'shop-page';
        }

        if (is_product()) {
            $classes[] = 'single-product-page';
        }

        if (is_product_category()) {
            $classes[] = 'product-category-page';
        }
    }

    return $classes;
}
add_filter('body_class', 'tasmeh_shop_custom_body_classes');

/**
 * Modify the excerpt length
 *
 * @param int $length Excerpt length.
 * @return int
 */
function tasmeh_shop_excerpt_length($length)
{
    return 20;
}
add_filter('excerpt_length', 'tasmeh_shop_excerpt_length');

/**
 * Modify the excerpt more string
 *
 * @param string $more More string.
 * @return string
 */
function tasmeh_shop_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'tasmeh_shop_excerpt_more');

/**
 * Add custom styles to TinyMCE editor
 *
 * @param array $settings TinyMCE settings.
 * @return array
 */
function tasmeh_shop_mce_css($settings)
{
    $font_url = get_template_directory_uri() . '/fonts/iransans.css';

    if (!empty($settings['content_css'])) {
        $content_css = $settings['content_css'] . ',' . $font_url;
    } else {
        $content_css = $font_url;
    }

    $settings['content_css'] = $content_css;

    return $settings;
}
add_filter('tiny_mce_before_init', 'tasmeh_shop_mce_css');

/**
 * Add custom styles to login page
 */
function tasmeh_shop_login_styles()
{
    wp_enqueue_style('tasmeh-shop-login', get_template_directory_uri() . '/assets/css/login.css', array(), TASMEH_SHOP_VERSION);
    wp_enqueue_style('iransans-font', get_template_directory_uri() . '/fonts/iransans.css', array(), TASMEH_SHOP_VERSION);
}
add_action('login_enqueue_scripts', 'tasmeh_shop_login_styles');

/**
 * Change login logo URL
 *
 * @return string
 */
function tasmeh_shop_login_logo_url()
{
    return home_url('/');
}
add_filter('login_headerurl', 'tasmeh_shop_login_logo_url');

/**
 * Change login logo title
 *
 * @return string
 */
function tasmeh_shop_login_logo_title()
{
    return get_bloginfo('name');
}
add_filter('login_headertext', 'tasmeh_shop_login_logo_title');
