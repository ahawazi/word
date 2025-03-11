<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tasmeh_Shop
 */

?>
</div><!-- .content-wrapper -->
</div><!-- .container -->
</div><!-- #content -->

<footer id="colophon" class="site-footer">
    <div class="footer-widgets">
        <div class="container">
            <div class="footer-widgets-wrapper">
                <div class="footer-widget footer-widget-1">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php else : ?>
                        <h3 class="widget-title">درباره فروشگاه</h3>
                        <div class="textwidget">
                            <p>فروشگاه اینترنتی تسمه، ارائه‌دهنده انواع گوشی‌های موبایل و لوازم جانبی. همراه با گارانتی اصل و خدمات پس از فروش.</p>
                        </div>
                        <div class="footer-contact">
                            <p><i class="fas fa-map-marker-alt"></i> تهران، خیابان ولیعصر، پلاک 1234</p>
                            <p><i class="fas fa-phone"></i> 021-12345678</p>
                            <p><i class="fas fa-envelope"></i> info@example.com</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="footer-widget footer-widget-2">
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <?php dynamic_sidebar('footer-2'); ?>
                    <?php else : ?>
                        <h3 class="widget-title">دسترسی سریع</h3>
                        <ul class="menu">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>">صفحه اصلی</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">فروشگاه</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>">حساب کاربری</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('cart'))); ?>">سبد خرید</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('checkout'))); ?>">تسویه حساب</a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="footer-widget footer-widget-3">
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php else : ?>
                        <h3 class="widget-title">خدمات مشتریان</h3>
                        <ul class="menu">
                            <li><a href="#">سوالات متداول</a></li>
                            <li><a href="#">شرایط و قوانین</a></li>
                            <li><a href="#">حریم خصوصی</a></li>
                            <li><a href="#">نحوه ارسال</a></li>
                            <li><a href="#">گارانتی محصولات</a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="footer-widget footer-widget-4">
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <?php dynamic_sidebar('footer-4'); ?>
                    <?php else : ?>
                        <h3 class="widget-title">دانلود اپلیکیشن</h3>
                        <div class="app-download">
                            <p>با دانلود اپلیکیشن فروشگاه، تجربه خرید آسان‌تری داشته باشید.</p>
                            <div class="app-buttons">
                                <a href="#" class="app-button">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/google-play.png'); ?>" alt="Google Play">
                                </a>
                                <a href="#" class="app-button">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/app-store.png'); ?>" alt="App Store">
                                </a>
                            </div>
                            <div class="social-icons">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-telegram"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> | <?php esc_html_e('تمامی حقوق محفوظ است.', 'tasmeh-shop'); ?></p>
                </div>
                <div class="payment-methods">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/payment-methods.png'); ?>" alt="<?php esc_attr_e('روش‌های پرداخت', 'tasmeh-shop'); ?>">
                </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>