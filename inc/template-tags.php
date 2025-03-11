<?php

/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Tasmeh_Shop
 */

if (!function_exists('tasmeh_shop_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function tasmeh_shop_posted_on()
    {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x('تاریخ انتشار: %s', 'post date', 'tasmeh-shop'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    }
endif;

if (!function_exists('tasmeh_shop_posted_by')) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function tasmeh_shop_posted_by()
    {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x('نویسنده: %s', 'post author', 'tasmeh-shop'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    }
endif;

if (!function_exists('tasmeh_shop_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function tasmeh_shop_entry_footer()
    {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'tasmeh-shop'));
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . esc_html__('دسته‌بندی‌ها: %1$s', 'tasmeh-shop') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html__(', ', 'tasmeh-shop'));
            if ($tags_list) {
                /* translators: 1: list of tags. */
                printf('<span class="tags-links">' . esc_html__('برچسب‌ها: %1$s', 'tasmeh-shop') . '</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }

        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('دیدگاه برای %s <span class="screen-reader-text">%s</span>', 'tasmeh-shop'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title()),
                    wp_kses_post(get_the_title())
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('ویرایش <span class="screen-reader-text">%s</span>', 'tasmeh-shop'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;

if (!function_exists('tasmeh_shop_post_thumbnail')) :
    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function tasmeh_shop_post_thumbnail()
    {
        if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
            return;
        }

        if (is_singular()) :
?>

            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div><!-- .post-thumbnail -->

        <?php else : ?>

            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    'post-thumbnail',
                    array(
                        'alt' => the_title_attribute(
                            array(
                                'echo' => false,
                            )
                        ),
                    )
                );
                ?>
            </a>

<?php
        endif; // End is_singular().
    }
endif;

if (!function_exists('wp_body_open')) :
    /**
     * Shim for sites older than 5.2.
     *
     * @link https://core.trac.wordpress.org/ticket/12563
     */
    function wp_body_open()
    {
        do_action('wp_body_open');
    }
endif;

if (!function_exists('tasmeh_shop_breadcrumbs')) :
    /**
     * Display breadcrumbs
     */
    function tasmeh_shop_breadcrumbs()
    {
        // Return if WooCommerce breadcrumbs are enabled
        if (function_exists('woocommerce_breadcrumb') && (is_woocommerce() || is_cart() || is_checkout())) {
            return;
        }

        // Return if on front page
        if (is_front_page()) {
            return;
        }

        $separator = '<i class="fas fa-angle-left"></i>';
        $home_text = esc_html__('خانه', 'tasmeh-shop');

        echo '<div class="breadcrumbs">';
        echo '<div class="container">';

        echo '<a href="' . esc_url(home_url('/')) . '">' . $home_text . '</a>' . $separator;

        if (is_category() || is_single()) {
            if (is_single()) {
                if (get_post_type() === 'post') {
                    $categories = get_the_category();
                    if (!empty($categories)) {
                        echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>' . $separator;
                    }
                } elseif (get_post_type() !== 'page') {
                    $post_type = get_post_type_object(get_post_type());
                    echo '<a href="' . esc_url(get_post_type_archive_link(get_post_type())) . '">' . esc_html($post_type->labels->name) . '</a>' . $separator;
                }
                the_title('<span class="current">', '</span>');
            } else {
                single_cat_title('<span class="current">', '</span>');
            }
        } elseif (is_page()) {
            if ($post->post_parent) {
                $parents = get_post_ancestors($post->ID);
                $parents = array_reverse($parents);
                foreach ($parents as $parent) {
                    echo '<a href="' . esc_url(get_permalink($parent)) . '">' . get_the_title($parent) . '</a>' . $separator;
                }
            }
            the_title('<span class="current">', '</span>');
        } elseif (is_tag()) {
            single_tag_title(esc_html__('برچسب: ', 'tasmeh-shop') . '<span class="current">', '</span>');
        } elseif (is_author()) {
            echo '<span class="current">' . esc_html__('نویسنده: ', 'tasmeh-shop') . get_the_author() . '</span>';
        } elseif (is_search()) {
            echo '<span class="current">' . esc_html__('نتایج جستجو برای: ', 'tasmeh-shop') . get_search_query() . '</span>';
        } elseif (is_day()) {
            echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a>' . $separator;
            echo '<a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . get_the_time('F') . '</a>' . $separator;
            echo '<span class="current">' . get_the_time('d') . '</span>';
        } elseif (is_month()) {
            echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a>' . $separator;
            echo '<span class="current">' . get_the_time('F') . '</span>';
        } elseif (is_year()) {
            echo '<span class="current">' . get_the_time('Y') . '</span>';
        } elseif (is_archive()) {
            echo '<span class="current">' . get_the_archive_title() . '</span>';
        } elseif (is_404()) {
            echo '<span class="current">' . esc_html__('صفحه پیدا نشد', 'tasmeh-shop') . '</span>';
        }

        echo '</div>'; // .container
        echo '</div>'; // .breadcrumbs
    }
endif;
