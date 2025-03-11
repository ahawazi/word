<?php

/**
 * Custom shortcodes for Tasmeh Shop theme
 *
 * @package Tasmeh_Shop
 */

/**
 * Featured Products Shortcode
 * Usage: [tasmeh_featured_products count="4" columns="4"]
 */
function tasmeh_shop_featured_products_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'count'   => 4,
            'columns' => 4,
        ),
        $atts,
        'tasmeh_featured_products'
    );

    $count   = intval($atts['count']);
    $columns = intval($atts['columns']);

    ob_start();

    if (class_exists('WooCommerce')) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $count,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                    'operator' => 'IN',
                ),
            ),
        );

        $loop = new WP_Query($args);

        if ($loop->have_posts()) {
            echo '<div class="products-grid columns-' . esc_attr($columns) . '">';

            while ($loop->have_posts()) {
                $loop->the_post();
                wc_get_template_part('content', 'product');
            }

            echo '</div>';

            wp_reset_postdata();
        } else {
            echo '<p class="no-products">' . esc_html__('هیچ محصول ویژه‌ای یافت نشد.', 'tasmeh-shop') . '</p>';
        }
    } else {
        echo '<p>' . esc_html__('برای استفاده از این شورت‌کد، افزونه ووکامرس باید فعال باشد.', 'tasmeh-shop') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('tasmeh_featured_products', 'tasmeh_shop_featured_products_shortcode');

/**
 * Latest Products Shortcode
 * Usage: [tasmeh_latest_products count="4" columns="4"]
 */
function tasmeh_shop_latest_products_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'count'   => 4,
            'columns' => 4,
        ),
        $atts,
        'tasmeh_latest_products'
    );

    $count   = intval($atts['count']);
    $columns = intval($atts['columns']);

    ob_start();

    if (class_exists('WooCommerce')) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $count,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $loop = new WP_Query($args);

        if ($loop->have_posts()) {
            echo '<div class="products-grid columns-' . esc_attr($columns) . '">';

            while ($loop->have_posts()) {
                $loop->the_post();
                wc_get_template_part('content', 'product');
            }

            echo '</div>';

            wp_reset_postdata();
        } else {
            echo '<p class="no-products">' . esc_html__('هیچ محصولی یافت نشد.', 'tasmeh-shop') . '</p>';
        }
    } else {
        echo '<p>' . esc_html__('برای استفاده از این شورت‌کد، افزونه ووکامرس باید فعال باشد.', 'tasmeh-shop') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('tasmeh_latest_products', 'tasmeh_shop_latest_products_shortcode');

/**
 * Product Categories Shortcode
 * Usage: [tasmeh_product_categories count="4" columns="4"]
 */
function tasmeh_shop_product_categories_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'count'   => 4,
            'columns' => 4,
            'parent'  => 0,
        ),
        $atts,
        'tasmeh_product_categories'
    );

    $count   = intval($atts['count']);
    $columns = intval($atts['columns']);
    $parent  = intval($atts['parent']);

    ob_start();

    if (class_exists('WooCommerce')) {
        $args = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $parent,
            'number'     => $count,
        );

        $categories = get_terms($args);

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<div class="categories-grid columns-' . esc_attr($columns) . '">';

            foreach ($categories as $category) {
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
        } else {
            echo '<p class="no-categories">' . esc_html__('هیچ دسته‌بندی محصولی یافت نشد.', 'tasmeh-shop') . '</p>';
        }
    } else {
        echo '<p>' . esc_html__('برای استفاده از این شورت‌کد، افزونه ووکامرس باید فعال باشد.', 'tasmeh-shop') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('tasmeh_product_categories', 'tasmeh_shop_product_categories_shortcode');

/**
 * Product Slider Shortcode
 * Usage: [tasmeh_product_slider count="8" category="mobile-phones"]
 */
function tasmeh_shop_product_slider_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'count'    => 8,
            'category' => '',
            'orderby'  => 'date',
            'order'    => 'DESC',
        ),
        $atts,
        'tasmeh_product_slider'
    );

    $count    = intval($atts['count']);
    $category = sanitize_text_field($atts['category']);
    $orderby  = sanitize_text_field($atts['orderby']);
    $order    = sanitize_text_field($atts['order']);

    ob_start();

    if (class_exists('WooCommerce')) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $count,
            'orderby'        => $orderby,
            'order'          => $order,
        );

        if (!empty($category)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $category,
                ),
            );
        }

        $loop = new WP_Query($args);

        if ($loop->have_posts()) {
            ?>
            <div class="product-slider">
                <div class="product-slider-wrapper">
                    <?php
                    while ($loop->have_posts()) {
                        $loop->the_post();
                        global $product;
                    ?>
                        <div class="product-slide">
                            <div class="product-card">
                                <div class="product-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            the_post_thumbnail('woocommerce_thumbnail');
                                        } else {
                                            echo wc_placeholder_img('woocommerce_thumbnail');
                                        }
                                        ?>
                                    </a>
                                    <?php if ($product->is_on_sale()) : ?>
                                        <span class="onsale"><?php echo esc_html__('حراج', 'tasmeh-shop'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-details">
                                    <h3 class="product-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <?php if ($price_html = $product->get_price_html()) : ?>
                                        <span class="price"><?php echo $price_html; ?></span>
                                    <?php endif; ?>
                                    <div class="product-actions">
                                        <?php
                                        echo apply_filters(
                                            'woocommerce_loop_add_to_cart_link',
                                            sprintf(
                                                '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
                                                esc_url($product->add_to_cart_url()),
                                                esc_attr(1),
                                                esc_attr('button add_to_cart_button'),
                                                wc_implode_html_attributes(array(
                                                    'data-product_id'  => $product->get_id(),
                                                    'data-product_sku' => $product->get_sku(),
                                                    'aria-label'       => $product->add_to_cart_description(),
                                                    'rel'              => 'nofollow',
                                                )),
                                                esc_html__('افزودن به سبد', 'tasmeh-shop')
                                            ),
                                            $product
                                        );
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="slider-nav">
                    <button class="slider-prev"><i class="fas fa-chevron-right"></i></button>
                    <button class="slider-next"><i class="fas fa-chevron-left"></i></button>
                </div>
            </div>
    <?php
            wp_reset_postdata();
        } else {
            echo '<p class="no-products">' . esc_html__('هیچ محصولی یافت نشد.', 'tasmeh-shop') . '</p>';
        }
    } else {
        echo '<p>' . esc_html__('برای استفاده از این شورت‌کد، افزونه ووکامرس باید فعال باشد.', 'tasmeh-shop') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('tasmeh_product_slider', 'tasmeh_shop_product_slider_shortcode');

/**
 * Call to Action Shortcode
 * Usage: [tasmeh_cta title="عنوان" description="توضیحات" button_text="متن دکمه" button_url="#" background_image="URL تصویر"]
 */
function tasmeh_shop_cta_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'title'            => esc_html__('عنوان فراخوان', 'tasmeh-shop'),
            'description'      => esc_html__('توضیحات فراخوان در اینجا قرار می‌گیرد.', 'tasmeh-shop'),
            'button_text'      => esc_html__('کلیک کنید', 'tasmeh-shop'),
            'button_url'       => '#',
            'background_image' => '',
            'text_color'       => '#ffffff',
        ),
        $atts,
        'tasmeh_cta'
    );

    $title            = sanitize_text_field($atts['title']);
    $description      = wp_kses_post($atts['description']);
    $button_text      = sanitize_text_field($atts['button_text']);
    $button_url       = esc_url($atts['button_url']);
    $background_image = esc_url($atts['background_image']);
    $text_color       = sanitize_hex_color($atts['text_color']);

    $style = '';
    if (!empty($background_image)) {
        $style .= 'background-image: url(' . $background_image . ');';
    }
    if (!empty($text_color)) {
        $style .= 'color: ' . $text_color . ';';
    }

    ob_start();
    ?>
    <div class="cta-banner" style="<?php echo esc_attr($style); ?>">
        <div class="cta-content">
            <h2 class="cta-title"><?php echo esc_html($title); ?></h2>
            <div class="cta-description"><?php echo wp_kses_post($description); ?></div>
            <a href="<?php echo esc_url($button_url); ?>" class="cta-button"><?php echo esc_html($button_text); ?></a>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('tasmeh_cta', 'tasmeh_shop_cta_shortcode');
