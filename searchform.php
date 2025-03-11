<?php

/**
 * The template for displaying search forms
 *
 * @package Tasmeh_Shop
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="s"><?php esc_html_e('جستجو برای:', 'tasmeh-shop'); ?></label>
    <div class="search-form-inner">
        <input type="search" id="s" class="search-field" placeholder="<?php echo esc_attr__('جستجو...', 'tasmeh-shop'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <button type="submit" class="search-submit">
            <i class="fas fa-search"></i>
            <span class="screen-reader-text"><?php esc_html_e('جستجو', 'tasmeh-shop'); ?></span>
        </button>
    </div>
</form>