<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Tasmeh_Shop
 */

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function tasmeh_shop_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'tasmeh_shop_pingback_header');

/**
 * Add custom classes to the body tag
 */
function tasmeh_shop_body_classes($classes)
{
    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if (!is_active_sidebar('sidebar-1')) {
        $classes[] = 'no-sidebar';
    }

    // Add a class if it is the WooCommerce shop page
    if (function_exists('is_shop') && is_shop()) {
        $classes[] = 'woocommerce-shop-page';
    }

    // Add a class if it is a product page
    if (function_exists('is_product') && is_product()) {
        $classes[] = 'woocommerce-product-page';
    }

    // Add a class for RTL language support
    if (is_rtl()) {
        $classes[] = 'rtl';
    }

    return $classes;
}
add_filter('body_class', 'tasmeh_shop_body_classes');

/**
 * Home Slider Function
 */
function tasmeh_shop_home_slider()
{
    // Check if using a slider plugin like Smart Slider, Otherwise use custom slider
    if (function_exists('smartslider3')) {
        echo smartslider3(1); // assuming slider ID is 1
    } else {
        // Custom slider implementation
        $slider_items = array(
            array(
                'title' => 'گوشی‌های هوشمند جدید',
                'subtitle' => 'با قیمت استثنایی',
                'description' => 'جدیدترین محصولات با گارانتی اصل و قیمت مناسب',
                'button_text' => 'خرید کنید',
                'button_url' => get_permalink(wc_get_page_id('shop')),
                'image' => get_template_directory_uri() . '/assets/images/slider1.jpg',
            ),
            array(
                'title' => 'تخفیف ویژه',
                'subtitle' => 'تا ۴۰٪ تخفیف',
                'description' => 'فروش ویژه انواع گوشی و لوازم جانبی به مناسبت عید',
                'button_text' => 'مشاهده محصولات',
                'button_url' => get_permalink(wc_get_page_id('shop')),
                'image' => get_template_directory_uri() . '/assets/images/slider2.jpg',
            ),
            array(
                'title' => 'لوازم جانبی موبایل',
                'subtitle' => 'کیفیت برتر',
                'description' => 'انواع کیف، قاب، هندزفری و شارژر اصل با گارانتی',
                'button_text' => 'خرید کنید',
                'button_url' => get_permalink(wc_get_page_id('shop')),
                'image' => get_template_directory_uri() . '/assets/images/slider3.jpg',
            ),
        );
?>
        <div class="home-slider-wrapper">
            <div class="slider-items">
                <?php foreach ($slider_items as $index => $item) : ?>
                    <div class="slider-item <?php echo $index === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo esc_url($item['image']); ?>');">
                        <div class="container">
                            <div class="slider-content">
                                <h2 class="slider-title"><?php echo esc_html($item['title']); ?></h2>
                                <h3 class="slider-subtitle"><?php echo esc_html($item['subtitle']); ?></h3>
                                <p class="slider-description"><?php echo esc_html($item['description']); ?></p>
                                <a href="<?php echo esc_url($item['button_url']); ?>" class="btn btn-primary slider-button"><?php echo esc_html($item['button_text']); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="slider-nav">
                <button class="slider-prev"><i class="fas fa-chevron-right"></i></button>
                <div class="slider-dots">
                    <?php foreach ($slider_items as $index => $item) : ?>
                        <button class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></button>
                    <?php endforeach; ?>
                </div>
                <button class="slider-next"><i class="fas fa-chevron-left"></i></button>
            </div>
        </div>
        <?php
    }
}

/**
 * Display Featured Categories
 */
function tasmeh_shop_display_featured_categories()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Get featured categories (either from theme option or top level categories)
    $featured_category_ids = get_theme_mod('tasmeh_shop_featured_categories');

    if (empty($featured_category_ids)) {
        // If no featured categories are set, get top level categories
        $args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => 0,
            'number' => 6,
        );
        $featured_categories = get_terms($args);
    } else {
        // Get the specifically set featured categories
        $args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'include' => $featured_category_ids,
        );
        $featured_categories = get_terms($args);
    }

    if (!empty($featured_categories) && !is_wp_error($featured_categories)) {
        echo '<div class="categories-wrapper">';

        foreach ($featured_categories as $category) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image = wp_get_attachment_url($thumbnail_id);
            $category_link = get_term_link($category);
        ?>
            <div class="category-item">
                <a href="<?php echo esc_url($category_link); ?>" class="category-link">
                    <div class="category-image">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                        <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/placeholder.png'); ?>" alt="<?php echo esc_attr($category->name); ?>">
                        <?php endif; ?>
                    </div>
                    <h3 class="category-title"><?php echo esc_html($category->name); ?></h3>
                    <span class="product-count"><?php printf(_n('%s محصول', '%s محصول', $category->count, 'tasmeh-shop'), $category->count); ?></span>
                </a>
            </div>
            <?php
        }

        echo '</div>';
    }
}

/**
 * Display Featured Products
 */
function tasmeh_shop_display_featured_products()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 8,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'featured',
                'operator' => 'IN',
            ),
        ),
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()) {
        echo '<div class="products-grid">';

        while ($loop->have_posts()) {
            $loop->the_post();
            wc_get_template_part('content', 'product');
        }

        echo '</div>';

        wp_reset_postdata();
    } else {
        echo '<p class="no-products">' . __('هیچ محصول ویژه‌ای یافت نشد.', 'tasmeh-shop') . '</p>';
    }
}

/**
 * Display Latest Products
 */
function tasmeh_shop_display_latest_products()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 8,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()) {
        echo '<div class="products-grid">';

        while ($loop->have_posts()) {
            $loop->the_post();
            wc_get_template_part('content', 'product');
        }

        echo '</div>';

        wp_reset_postdata();
    } else {
        echo '<p class="no-products">' . __('هیچ محصولی یافت نشد.', 'tasmeh-shop') . '</p>';
    }
}

/**
 * Display Testimonials
 */
function tasmeh_shop_display_testimonials()
{
    // Get testimonials from custom post type if it exists
    if (post_type_exists('testimonial')) {
        $args = array(
            'post_type' => 'testimonial',
            'posts_per_page' => 3,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $loop = new WP_Query($args);

        if ($loop->have_posts()) {
            echo '<div class="testimonials-wrapper">';

            while ($loop->have_posts()) {
                $loop->the_post();
                $rating = get_post_meta(get_the_ID(), 'testimonial_rating', true);
            ?>
                <div class="testimonial-item">
                    <div class="testimonial-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail'); ?>
                        <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/testimonial-placeholder.jpg'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="testimonial-content">
                        <p class="testimonial-comment"><?php echo get_the_content(); ?></p>
                        <div class="testimonial-rating">
                            <?php
                            $rating = intval($rating);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <h4 class="testimonial-name"><?php the_title(); ?></h4>
                    </div>
                </div>
            <?php
            }

            echo '</div>';

            wp_reset_postdata();
        }
    } else {
        // Fallback testimonials if post type doesn't exist
        $testimonials = array(
            array(
                'name' => 'علی محمدی',
                'image' => get_template_directory_uri() . '/assets/images/testimonial1.jpg',
                'comment' => 'تجربه خرید از این فروشگاه عالی بود. محصول با کیفیت و اصل رو با قیمت مناسب خریداری کردم. ارسال هم سریع انجام شد.',
                'rating' => 5,
            ),
            array(
                'name' => 'سارا احمدی',
                'image' => get_template_directory_uri() . '/assets/images/testimonial2.jpg',
                'comment' => 'از نحوه پاسخگویی پشتیبانی و کیفیت محصولات راضی هستم. قطعا خرید بعدی رو هم از اینجا انجام میدم.',
                'rating' => 4,
            ),
            array(
                'name' => 'محمد رضایی',
                'image' => get_template_directory_uri() . '/assets/images/testimonial3.jpg',
                'comment' => 'تحویل سریع و بسته‌بندی مناسب. محصول دقیقا همونی بود که توی سایت معرفی شده بود. پیشنهاد میکنم از این فروشگاه خرید کنید.',
                'rating' => 5,
            ),
        );

        echo '<div class="testimonials-wrapper">';

        foreach ($testimonials as $testimonial) {
            ?>
            <div class="testimonial-item">
                <div class="testimonial-image">
                    <img src="<?php echo esc_url($testimonial['image']); ?>" alt="<?php echo esc_attr($testimonial['name']); ?>">
                </div>
                <div class="testimonial-content">
                    <p class="testimonial-comment"><?php echo esc_html($testimonial['comment']); ?></p>
                    <div class="testimonial-rating">
                        <?php
                        $rating = intval($testimonial['rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <h4 class="testimonial-name"><?php echo esc_html($testimonial['name']); ?></h4>
                </div>
            </div>
        <?php
        }

        echo '</div>';
    }
}

/**
 * Display Brands
 */
function tasmeh_shop_display_brands()
{
    // Default brands
    $brands = array(
        array('name' => 'Apple', 'image' => get_template_directory_uri() . '/assets/images/brand-apple.png'),
        array('name' => 'Samsung', 'image' => get_template_directory_uri() . '/assets/images/brand-samsung.png'),
        array('name' => 'Xiaomi', 'image' => get_template_directory_uri() . '/assets/images/brand-xiaomi.png'),
        array('name' => 'Huawei', 'image' => get_template_directory_uri() . '/assets/images/brand-huawei.png'),
        array('name' => 'Nokia', 'image' => get_template_directory_uri() . '/assets/images/brand-nokia.png'),
        array('name' => 'Sony', 'image' => get_template_directory_uri() . '/assets/images/brand-sony.png'),
    );

    echo '<div class="brands-wrapper">';

    foreach ($brands as $brand) {
        ?>
        <div class="brand-item">
            <img src="<?php echo esc_url($brand['image']); ?>" alt="<?php echo esc_attr($brand['name']); ?>">
        </div>
<?php
    }

    echo '</div>';
}

/**
 * Convert English numbers to Persian
 */
function tasmeh_shop_en_to_persian_num($number)
{
    $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($english_digits, $persian_digits, $number);
}

/**
 * Format price in Persian
 */
function tasmeh_shop_format_price($price)
{
    $formatted_price = number_format($price, 0, '.', ',');
    $formatted_price = tasmeh_shop_en_to_persian_num($formatted_price);

    return $formatted_price . ' تومان';
}

/**
 * Calculate discount percentage
 */
function tasmeh_shop_get_discount_percentage($product)
{
    if (!$product->is_on_sale() || $product->is_type('variable')) {
        return 0;
    }

    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();

    if ($regular_price == 0) {
        return 0;
    }

    $percentage = round(100 - ($sale_price / $regular_price * 100));

    return $percentage;
}
