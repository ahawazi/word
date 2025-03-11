<?php

/**
 * تنظیمات اصلی سایت
 */

// تنظیم منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// آدرس پایه سایت
define('BASE_URL', 'http://localhost/word');

// مسیر پوشه‌های اصلی
define('ROOT_PATH', dirname(__DIR__) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'uploads/');
define('PRODUCT_IMG_PATH', UPLOAD_PATH . 'products/');

// تنظیمات امنیتی
define('SALT', 'tasmeh_salt_for_password_encryption_@2024');

// شامل کردن فایل اتصال به پایگاه داده
require_once(ROOT_PATH . 'config/database.php');

/**
 * توابع کمکی
 */

/**
 * تبدیل رشته به URL سازگار
 *
 * @param string $text متن ورودی
 * @return string متن تبدیل شده به slug
 */
function slugify($text)
{
    // حذف کاراکترهای غیر الفبایی و جایگزینی با خط تیره
    // برای زبان فارسی باید الگوریتم پیچیده‌تری استفاده کرد،
    // اما برای سادگی یک نسخه ساده را پیاده‌سازی می‌کنیم

    // تبدیل به انگلیسی (توابعی برای ترجمه به انگلیسی نیاز است)
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text;
}

/**
 * هش کردن رمز عبور با روش امن
 *
 * @param string $password رمز عبور
 * @return string رمز عبور هش شده
 */
function hashPassword($password)
{
    return password_hash($password . SALT, PASSWORD_BCRYPT);
}

/**
 * بررسی صحت رمز عبور
 *
 * @param string $password رمز عبور ورودی
 * @param string $hash رمز عبور هش شده ذخیره شده
 * @return bool نتیجه بررسی
 */
function verifyPassword($password, $hash)
{
    return password_verify($password . SALT, $hash);
}

/**
 * خروجی امن برای نمایش در HTML
 *
 * @param string $str رشته ورودی
 * @return string رشته امن برای نمایش
 */
function escape($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * تولید توکن CSRF برای امنیت فرم‌ها
 *
 * @return string توکن تولید شده
 */
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * بررسی صحت توکن CSRF
 *
 * @param string $token توکن دریافتی
 * @return bool نتیجه بررسی
 */
function validateCSRFToken($token)
{
    if (isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token']) {
        return true;
    }
    return false;
}

/**
 * تبدیل قیمت به فرمت فارسی
 * 
 * @param int $price قیمت به عدد
 * @return string قیمت فرمت‌بندی شده
 */
function formatPrice($price)
{
    // تبدیل اعداد به فارسی
    $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $formattedPrice = number_format($price);
    $result = '';

    for ($i = 0; $i < strlen($formattedPrice); $i++) {
        if (is_numeric($formattedPrice[$i])) {
            $result .= $persianDigits[(int)$formattedPrice[$i]];
        } else {
            $result .= $formattedPrice[$i];
        }
    }

    return $result . ' تومان';
}

/**
 * ایجاد پیغام خطا یا موفقیت
 * 
 * @param string $message متن پیغام
 * @param string $type نوع پیغام (success, error, warning, info)
 * @return void
 */
function setMessage($message, $type = 'info')
{
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

/**
 * نمایش پیغام ها
 * 
 * @return string کد HTML برای نمایش پیغام
 */
function showMessage()
{
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $output = '<div class="alert alert-' . $message['type'] . '">';
        $output .= escape($message['text']);
        $output .= '</div>';

        // حذف پیغام بعد از نمایش
        unset($_SESSION['message']);

        return $output;
    }
    return '';
}
