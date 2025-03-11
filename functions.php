<?php

/**
 * Tasmeh Shop functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Tasmeh_Shop
 */

if (!defined('TASMEH_SHOP_VERSION')) {
    // Replace the version number of the theme on each release.
    define('TASMEH_SHOP_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function tasmeh_shop_setup()
{
    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_theme_textdomain('tasmeh-shop', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support('title-tag');

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    // Custom image sizes
    add_image_size('tasmeh-shop-featured', 1200, 600, true);  // Featured image size
    add_image_size('tasmeh-shop-product-thumb', 600, 600, true); // Product thumbnail
    add_image_size('tasmeh-shop-product-gallery', 800, 800, true); // Product gallery 
    add_image_size('tasmeh-shop-slider', 1600, 800, true); // Slider image size

    // This theme uses wp_nav_menu() in several locations.
    register_nav_menus(
        array(
            'primary' => esc_html__('منوی اصلی', 'tasmeh-shop'),
            'categories' => esc_html__('منوی دسته‌بندی', 'tasmeh-shop'),
            'footer' => esc_html__('منوی فوتر', 'tasmeh-shop'),
        )
    );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'tasmeh_shop_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );

    /**
     * WooCommerce support.
     */
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'tasmeh_shop_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function tasmeh_shop_content_width()
{
    $GLOBALS['content_width'] = apply_filters('tasmeh_shop_content_width', 1140);
}
add_action('after_setup_theme', 'tasmeh_shop_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function tasmeh_shop_widgets_init()
{
    register_sidebar(
        array(
            'name'          => esc_html__('سایدبار', 'tasmeh-shop'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('ابزارک‌ها را اینجا اضافه کنید.', 'tasmeh-shop'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html__('سایدبار فروشگاه', 'tasmeh-shop'),
            'id'            => 'shop-sidebar',
            'description'   => esc_html__('ابزارک‌های مربوط به فروشگاه را اینجا اضافه کنید.', 'tasmeh-shop'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html__('سایدبار محصول', 'tasmeh-shop'),
            'id'            => 'product-sidebar',
            'description'   => esc_html__('ابزارک‌های مربوط به صفحه محصول را اینجا اضافه کنید.', 'tasmeh-shop'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Footer Widget Areas
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(
            array(
                'name'          => sprintf(esc_html__('فوتر %d', 'tasmeh-shop'), $i),
                'id'            => 'footer-' . $i,
                'description'   => sprintf(esc_html__('ابزارک‌های ناحیه %d فوتر را اینجا اضافه کنید.', 'tasmeh-shop'), $i),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );
    }
}
add_action('widgets_init', 'tasmeh_shop_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function tasmeh_shop_scripts()
{
    wp_enqueue_style('tasmeh-shop-style', get_stylesheet_uri(), array(), TASMEH_SHOP_VERSION);
    wp_enqueue_style('fontawesome', get_template_directory_uri() . '/assets/css/fontawesome.min.css', array(), '5.15.3');
    wp_enqueue_style('tasmeh-shop-main', get_template_directory_uri() . '/assets/css/main.css', array(), TASMEH_SHOP_VERSION);

    // Add IRANSans font
    wp_enqueue_style('iransans-font', get_template_directory_uri() . '/fonts/iransans.css', array(), TASMEH_SHOP_VERSION);

    wp_enqueue_script('tasmeh-shop-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), TASMEH_SHOP_VERSION, true);
    wp_enqueue_script('tasmeh-shop-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), TASMEH_SHOP_VERSION, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Home page slider script
    if (is_front_page()) {
        wp_enqueue_script('tasmeh-shop-slider', get_template_directory_uri() . '/assets/js/slider.js', array('jquery'), TASMEH_SHOP_VERSION, true);
    }

    // WooCommerce pages scripts
    if (class_exists('WooCommerce') && (is_shop() || is_product() || is_product_category() || is_product_tag() || is_cart() || is_checkout())) {
        wp_enqueue_script('tasmeh-shop-woocommerce', get_template_directory_uri() . '/assets/js/woocommerce.js', array('jquery'), TASMEH_SHOP_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'tasmeh_shop_scripts');

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * WooCommerce compatibility functions.
 */
if (class_exists('WooCommerce')) {
    require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Custom shortcodes
 */
require get_template_directory() . '/inc/shortcodes.php';

/**
 * Theme custom functions
 */
require get_template_directory() . '/inc/theme-functions.php';

/**
 * Set products per page in shop
 */
function tasmeh_shop_products_per_page()
{
    return 12;
}
add_filter('loop_shop_per_page', 'tasmeh_shop_products_per_page', 20);

/**
 * Change WooCommerce product columns in shop
 */
function tasmeh_shop_loop_columns()
{
    return 4;
}
add_filter('loop_shop_columns', 'tasmeh_shop_loop_columns');

/**
 * Load a custom template for single posts and pages.
 */
function tasmeh_shop_load_custom_templates($template)
{
    if (is_single() && 'post' == get_post_type()) {
        $new_template = locate_template(array('single-post.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }

    if (is_page() && !is_page_template()) {
        $new_template = locate_template(array('page-default.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }

    return $template;
}
add_filter('template_include', 'tasmeh_shop_load_custom_templates');
