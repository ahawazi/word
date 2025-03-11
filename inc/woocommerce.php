<?php

/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Tasmeh_Shop
 */

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function tasmeh_shop_woocommerce_scripts()
{
    wp_enqueue_style('tasmeh-shop-woocommerce-style', get_template_directory_uri() . '/assets/css/woocommerce.css', array(), TASMEH_SHOP_VERSION);

    $font_path   = WC()->plugin_url() . '/assets/fonts/';
    $inline_font = '@font-face {
            font-family: "star";
            src: url("' . $font_path . 'star.eot");
            src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
                url("' . $font_path . 'star.woff") format("woff"),
                url("' . $font_path . 'star.ttf") format("truetype"),
                url("' . $font_path . 'star.svg#star") format("svg");
            font-weight: normal;
            font-style: normal;
        }';

    wp_add_inline_style('tasmeh-shop-woocommerce-style', $inline_font);
}
add_action('wp_enqueue_scripts', 'tasmeh_shop_woocommerce_scripts');

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function tasmeh_shop_woocommerce_active_body_class($classes)
{
    $classes[] = 'woocommerce-active';

    return $classes;
}
add_filter('body_class', 'tasmeh_shop_woocommerce_active_body_class');

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function tasmeh_shop_woocommerce_related_products_args($args)
{
    $defaults = array(
        'posts_per_page' => 4,
        'columns'        => 4,
    );

    $args = wp_parse_args($defaults, $args);

    return $args;
}
add_filter('woocommerce_output_related_products_args', 'tasmeh_shop_woocommerce_related_products_args');

/**
 * Remove default WooCommerce wrapper.
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

if (!function_exists('tasmeh_shop_woocommerce_wrapper_before')) {
    /**
     * Before Content.
     *
     * Wraps all WooCommerce content in wrappers which match the theme markup.
     *
     * @return void
     */
    function tasmeh_shop_woocommerce_wrapper_before()
    {
?>
        <div id="primary" class="content-area">
            <main id="main" class="site-main">
            <?php
        }
    }
    add_action('woocommerce_before_main_content', 'tasmeh_shop_woocommerce_wrapper_before');

    if (!function_exists('tasmeh_shop_woocommerce_wrapper_after')) {
        /**
         * After Content.
         *
         * Closes the wrapping divs.
         *
         * @return void
         */
        function tasmeh_shop_woocommerce_wrapper_after()
        {
            ?>
            </main><!-- #main -->
        </div><!-- #primary -->
    <?php
        }
    }
    add_action('woocommerce_after_main_content', 'tasmeh_shop_woocommerce_wrapper_after');

    /**
     * Sample implementation of the WooCommerce Mini Cart.
     *
     * You can add the WooCommerce Mini Cart to header.php like so ...
     *
     * <?php
     * if ( function_exists( 'tasmeh_shop_woocommerce_header_cart' ) ) {
     * tasmeh_shop_woocommerce_header_cart();
     * }
     * ?>
     */

    if (!function_exists('tasmeh_shop_woocommerce_header_cart')) {
        /**
         * Display Header Cart.
         *
         * @return void
         */
        function tasmeh_shop_woocommerce_header_cart()
        {
            if (is_cart()) {
                $class = 'current-menu-item';
            } else {
                $class = '';
            }
    ?>
        <div class="site-header-cart">
            <div class="<?php echo esc_attr($class); ?>">
                <?php tasmeh_shop_woocommerce_cart_link(); ?>
            </div>
            <div class="cart-dropdown widget_shopping_cart">
                <div class="widget_shopping_cart_content">
                    <?php
                    $instance = array(
                        'title' => '',
                    );
                    the_widget('WC_Widget_Cart', $instance);
                    ?>
                </div>
            </div>
        </div>
    <?php
        }
    }

    if (!function_exists('tasmeh_shop_woocommerce_cart_link')) {
        /**
         * Cart Link.
         *
         * Displayed a link to the cart including the number of items present and the cart total.
         *
         * @return void
         */
        function tasmeh_shop_woocommerce_cart_link()
        {
    ?>
        <a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_attr_e('مشاهده سبد خرید', 'tasmeh-shop'); ?>">
            <?php
            $item_count_text = sprintf(
                /* translators: number of items in the mini cart. */
                _n('%d آیتم', '%d آیتم', WC()->cart->get_cart_contents_count(), 'tasmeh-shop'),
                WC()->cart->get_cart_contents_count()
            );
            ?>
            <i class="fas fa-shopping-cart"></i>
            <span class="count"><?php echo esc_html($item_count_text); ?></span>
            <span class="amount"><?php echo wp_kses_data(WC()->cart->get_cart_subtotal()); ?></span>
        </a>
    <?php
        }
    }

    /**
     * Add Persian currency support
     */
    function tasmeh_shop_add_persian_currency($currencies)
    {
        $currencies['IRR'] = 'ریال ایران';
        $currencies['IRT'] = 'تومان ایران';
        return $currencies;
    }
    add_filter('woocommerce_currencies', 'tasmeh_shop_add_persian_currency');

    /**
     * Add Persian currency symbol
     */
    function tasmeh_shop_add_persian_currency_symbol($currency_symbol, $currency)
    {
        switch ($currency) {
            case 'IRR':
                $currency_symbol = 'ریال';
                break;
            case 'IRT':
                $currency_symbol = 'تومان';
                break;
        }
        return $currency_symbol;
    }
    add_filter('woocommerce_currency_symbol', 'tasmeh_shop_add_persian_currency_symbol', 10, 2);

    /**
     * Format Persian price
     */
    function tasmeh_shop_persian_price_format($price, $args)
    {
        $currency = get_woocommerce_currency();
        if (in_array($currency, array('IRR', 'IRT'))) {
            $formatted_price = rtrim(str_replace(',', '،', number_format($price, ($args['decimals'] > 0 ? $args['decimals'] : 0))), '.00');
            $formatted_price = tasmeh_shop_en_to_persian_num($formatted_price);
            return $formatted_price . ' ' . $args['currency'];
        }
        return $price;
    }
    add_filter('woocommerce_get_price_html', 'tasmeh_shop_persian_price_format', 10, 2);

    /**
     * Customize WooCommerce product columns
     */
    function tasmeh_shop_change_number_related_products($args)
    {
        $args['posts_per_page'] = 4; // 4 related products
        $args['columns'] = 4; // arranged in 4 columns
        return $args;
    }
    add_filter('woocommerce_output_related_products_args', 'tasmeh_shop_change_number_related_products', 20);

    /**
     * Update cart count on AJAX add to cart
     */
    function tasmeh_shop_add_to_cart_fragment($fragments)
    {
        ob_start();
    ?>
    <span class="cart-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
<?php
        $fragments['span.cart-count'] = ob_get_clean();
        return $fragments;
    }
    add_filter('woocommerce_add_to_cart_fragments', 'tasmeh_shop_add_to_cart_fragment');

    /**
     * Customize checkout fields
     */
    function tasmeh_shop_checkout_fields($fields)
    {
        // Rearrange billing fields
        $fields['billing']['billing_first_name']['priority'] = 10;
        $fields['billing']['billing_last_name']['priority'] = 20;
        $fields['billing']['billing_phone']['priority'] = 30;
        $fields['billing']['billing_email']['priority'] = 40;
        $fields['billing']['billing_address_1']['priority'] = 50;
        $fields['billing']['billing_address_2']['priority'] = 60;
        $fields['billing']['billing_postcode']['priority'] = 70;
        $fields['billing']['billing_city']['priority'] = 80;
        $fields['billing']['billing_country']['priority'] = 90;
        $fields['billing']['billing_state']['priority'] = 100;

        // Rearrange shipping fields
        $fields['shipping']['shipping_first_name']['priority'] = 10;
        $fields['shipping']['shipping_last_name']['priority'] = 20;
        $fields['shipping']['shipping_address_1']['priority'] = 30;
        $fields['shipping']['shipping_address_2']['priority'] = 40;
        $fields['shipping']['shipping_postcode']['priority'] = 50;
        $fields['shipping']['shipping_city']['priority'] = 60;
        $fields['shipping']['shipping_country']['priority'] = 70;
        $fields['shipping']['shipping_state']['priority'] = 80;

        // Remove company field
        unset($fields['billing']['billing_company']);
        unset($fields['shipping']['shipping_company']);

        // Translate field labels for Persian
        $fields['billing']['billing_first_name']['label'] = 'نام';
        $fields['billing']['billing_last_name']['label'] = 'نام خانوادگی';
        $fields['billing']['billing_phone']['label'] = 'تلفن';
        $fields['billing']['billing_email']['label'] = 'ایمیل';
        $fields['billing']['billing_address_1']['label'] = 'آدرس';
        $fields['billing']['billing_address_2']['label'] = 'آدرس (ادامه)';
        $fields['billing']['billing_postcode']['label'] = 'کد پستی';
        $fields['billing']['billing_city']['label'] = 'شهر';
        $fields['billing']['billing_country']['label'] = 'کشور';
        $fields['billing']['billing_state']['label'] = 'استان';

        $fields['shipping']['shipping_first_name']['label'] = 'نام';
        $fields['shipping']['shipping_last_name']['label'] = 'نام خانوادگی';
        $fields['shipping']['shipping_address_1']['label'] = 'آدرس';
        $fields['shipping']['shipping_address_2']['label'] = 'آدرس (ادامه)';
        $fields['shipping']['shipping_postcode']['label'] = 'کد پستی';
        $fields['shipping']['shipping_city']['label'] = 'شهر';
        $fields['shipping']['shipping_country']['label'] = 'کشور';
        $fields['shipping']['shipping_state']['label'] = 'استان';

        return $fields;
    }
    add_filter('woocommerce_checkout_fields', 'tasmeh_shop_checkout_fields');

    /**
     * Customize product tabs
     */
    function tasmeh_shop_rename_product_tabs($tabs)
    {
        // Change tab titles
        if (isset($tabs['description'])) {
            $tabs['description']['title'] = 'توضیحات محصول';
        }

        if (isset($tabs['additional_information'])) {
            $tabs['additional_information']['title'] = 'مشخصات فنی';
        }

        if (isset($tabs['reviews'])) {
            $tabs['reviews']['title'] = 'نظرات کاربران';
        }

        return $tabs;
    }
    add_filter('woocommerce_product_tabs', 'tasmeh_shop_rename_product_tabs');
