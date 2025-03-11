<?php

/**
 * The front page template file
 *
 * If the user has selected a static page for their homepage,
 * this is what will appear.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tasmeh_Shop
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php if (function_exists('tasmeh_shop_home_slider')) : ?>
            <section class="home-slider">
                <?php tasmeh_shop_home_slider(); ?>
            </section>
        <?php endif; ?>

        <section class="home-features">
            <div class="container">
                <div class="features-wrapper">
                    <div class="feature-item">
                        <i class="fas fa-truck"></i>
                        <h3><?php esc_html_e('ارسال سریع', 'tasmeh-shop'); ?></h3>
                        <p><?php esc_html_e('تحویل اکسپرس در کمترین زمان', 'tasmeh-shop'); ?></p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <h3><?php esc_html_e('ضمانت اصالت', 'tasmeh-shop'); ?></h3>
                        <p><?php esc_html_e('تضمین اصالت کالا', 'tasmeh-shop'); ?></p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-headset"></i>
                        <h3><?php esc_html_e('پشتیبانی', 'tasmeh-shop'); ?></h3>
                        <p><?php esc_html_e('پشتیبانی ۲۴ ساعته', 'tasmeh-shop'); ?></p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-undo"></i>
                        <h3><?php esc_html_e('گارانتی بازگشت', 'tasmeh-shop'); ?></h3>
                        <p><?php esc_html_e('۷ روز ضمانت بازگشت', 'tasmeh-shop'); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-categories">
            <div class="container">
                <h2 class="section-title"><?php esc_html_e('دسته‌بندی محصولات', 'tasmeh-shop'); ?></h2>

                <div class="categories-wrapper">
                    <?php
                    if (function_exists('tasmeh_shop_display_featured_categories')) {
                        tasmeh_shop_display_featured_categories();
                    } else {
                        // Fallback if the function doesn't exist
                        $featured_categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 6,
                        ));

                        if (!empty($featured_categories) && !is_wp_error($featured_categories)) {
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
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="featured-products">
            <div class="container">
                <h2 class="section-title"><?php esc_html_e('محصولات ویژه', 'tasmeh-shop'); ?></h2>

                <?php
                if (function_exists('tasmeh_shop_display_featured_products')) {
                    tasmeh_shop_display_featured_products();
                } else {
                    if (class_exists('WooCommerce')) {
                        echo do_shortcode('[products limit="8" columns="4" visibility="featured" orderby="date" order="DESC"]');
                    }
                }
                ?>
            </div>
        </section>

        <section class="special-offer">
            <div class="container">
                <div class="special-offer-wrapper">
                    <div class="special-offer-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/special-offer.jpg'); ?>" alt="<?php esc_attr_e('پیشنهاد ویژه', 'tasmeh-shop'); ?>">
                    </div>
                    <div class="special-offer-content">
                        <h2><?php esc_html_e('پیشنهاد ویژه', 'tasmeh-shop'); ?></h2>
                        <h3><?php esc_html_e('تا ۳۰٪ تخفیف', 'tasmeh-shop'); ?></h3>
                        <p><?php esc_html_e('جدیدترین گوشی‌های هوشمند با کیفیت بالا و گارانتی اصل', 'tasmeh-shop'); ?></p>
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-primary"><?php esc_html_e('مشاهده محصولات', 'tasmeh-shop'); ?></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="latest-products">
            <div class="container">
                <h2 class="section-title"><?php esc_html_e('جدیدترین محصولات', 'tasmeh-shop'); ?></h2>

                <?php
                if (function_exists('tasmeh_shop_display_latest_products')) {
                    tasmeh_shop_display_latest_products();
                } else {
                    if (class_exists('WooCommerce')) {
                        echo do_shortcode('[products limit="8" columns="4" orderby="date" order="DESC"]');
                    }
                }
                ?>
            </div>
        </section>

        <section class="testimonials">
            <div class="container">
                <h2 class="section-title"><?php esc_html_e('نظرات مشتریان', 'tasmeh-shop'); ?></h2>

                <div class="testimonials-wrapper">
                    <?php
                    if (function_exists('tasmeh_shop_display_testimonials')) {
                        tasmeh_shop_display_testimonials();
                    } else {
                        // Fallback testimonials if the function doesn't exist
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
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="brands">
            <div class="container">
                <h2 class="section-title"><?php esc_html_e('برندهای محبوب', 'tasmeh-shop'); ?></h2>

                <div class="brands-wrapper">
                    <?php
                    if (function_exists('tasmeh_shop_display_brands')) {
                        tasmeh_shop_display_brands();
                    } else {
                        // Fallback brands if the function doesn't exist
                        $brands = array(
                            array('name' => 'Apple', 'image' => get_template_directory_uri() . '/assets/images/brand-apple.png'),
                            array('name' => 'Samsung', 'image' => get_template_directory_uri() . '/assets/images/brand-samsung.png'),
                            array('name' => 'Xiaomi', 'image' => get_template_directory_uri() . '/assets/images/brand-xiaomi.png'),
                            array('name' => 'Huawei', 'image' => get_template_directory_uri() . '/assets/images/brand-huawei.png'),
                            array('name' => 'Nokia', 'image' => get_template_directory_uri() . '/assets/images/brand-nokia.png'),
                            array('name' => 'Sony', 'image' => get_template_directory_uri() . '/assets/images/brand-sony.png'),
                        );

                        foreach ($brands as $brand) {
                    ?>
                            <div class="brand-item">
                                <img src="<?php echo esc_url($brand['image']); ?>" alt="<?php echo esc_attr($brand['name']); ?>">
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="newsletter">
            <div class="container">
                <div class="newsletter-wrapper">
                    <div class="newsletter-content">
                        <h2><?php esc_html_e('عضویت در خبرنامه', 'tasmeh-shop'); ?></h2>
                        <p><?php esc_html_e('برای اطلاع از آخرین محصولات و تخفیف‌های ویژه در خبرنامه ما عضو شوید.', 'tasmeh-shop'); ?></p>
                    </div>
                    <div class="newsletter-form">
                        <form action="#" method="post" class="newsletter-subscribe-form">
                            <input type="email" name="email" placeholder="<?php esc_attr_e('ایمیل خود را وارد کنید', 'tasmeh-shop'); ?>" required>
                            <button type="submit" class="btn btn-primary"><?php esc_html_e('عضویت', 'tasmeh-shop'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
?>