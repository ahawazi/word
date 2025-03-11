<?php

/**
 * Template part for displaying products
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tasmeh_Shop
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<li <?php wc_product_class('product-card', $product); ?>>
    <div class="product-card-inner">
        <div class="product-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('woocommerce_thumbnail');
                } else {
                    echo wc_placeholder_img('woocommerce_thumbnail');
                }

                // Show second image on hover if available
                $attachment_ids = $product->get_gallery_image_ids();
                if (!empty($attachment_ids[0])) {
                    echo wp_get_attachment_image($attachment_ids[0], 'woocommerce_thumbnail', false, array('class' => 'product-image-hover'));
                }
                ?>
            </a>

            <?php if ($product->is_on_sale()) : ?>
                <span class="onsale"><?php echo esc_html__('حراج', 'tasmeh-shop'); ?></span>
            <?php endif; ?>

            <?php if (!$product->is_in_stock()) : ?>
                <span class="out-of-stock"><?php echo esc_html__('ناموجود', 'tasmeh-shop'); ?></span>
            <?php endif; ?>

            <div class="product-actions">
                <?php if ($product->is_in_stock()) : ?>
                    <?php if ($product->supports('ajax_add_to_cart') && $product->is_purchasable()) : ?>
                        <?php echo apply_filters(
                            'woocommerce_loop_add_to_cart_link',
                            sprintf(
                                '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
                                esc_url($product->add_to_cart_url()),
                                esc_attr(1),
                                esc_attr('button add_to_cart_button ajax_add_to_cart'),
                                wc_implode_html_attributes(array(
                                    'data-product_id'  => $product->get_id(),
                                    'data-product_sku' => $product->get_sku(),
                                    'aria-label'       => $product->add_to_cart_description(),
                                    'rel'              => 'nofollow',
                                )),
                                '<i class="fas fa-shopping-cart"></i>'
                            ),
                            $product
                        ); ?>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="<?php the_permalink(); ?>" class="view-product" title="<?php esc_attr_e('مشاهده محصول', 'tasmeh-shop'); ?>">
                    <i class="fas fa-eye"></i>
                </a>

                <?php if (defined('YITH_WCWL') && function_exists('YITH_WCWL')) : ?>
                    <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
                <?php endif; ?>

                <?php if (defined('YITH_WOOCOMPARE') && function_exists('YITH_WOOCOMPARE')) : ?>
                    <?php echo do_shortcode('[yith_compare_button]'); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="product-details">
            <div class="product-category">
                <?php echo wc_get_product_category_list($product->get_id(), ', '); ?>
            </div>

            <h2 class="woocommerce-loop-product__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>

            <?php if ($price_html = $product->get_price_html()) : ?>
                <span class="price"><?php echo $price_html; ?></span>
            <?php endif; ?>

            <?php if ($product->get_rating_count() > 0) : ?>
                <div class="star-rating-wrapper">
                    <?php echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
                    <span class="rating-count">(<?php echo $product->get_rating_count(); ?>)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</li>