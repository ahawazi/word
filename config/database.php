<?php

/**
 * تنظیمات اتصال به پایگاه داده
 */

// پارامترهای اتصال به پایگاه داده
define('DB_HOST', 'localhost');      // آدرس سرور پایگاه داده
define('DB_NAME', 'tasmeh_shop');    // نام پایگاه داده
define('DB_USER', 'root');           // نام کاربری پایگاه داده
define('DB_PASS', '');               // رمز عبور پایگاه داده

// برقراری اتصال به پایگاه داده
function connectDB()
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // در صورت خطا در اتصال
        error_log("خطا در اتصال به پایگاه داده: " . $e->getMessage());
        return false;
    }
}

/**
 * فانکشن برای اجرای کوئری‌های SQL و بازگرداندن نتیجه
 * 
 * @param string $sql کوئری SQL
 * @param array $params پارامترهای کوئری
 * @return mixed نتیجه کوئری یا false در صورت خطا
 */
function executeQuery($sql, $params = [])
{
    $conn = connectDB();
    if ($conn) {
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("خطا در اجرای کوئری: " . $e->getMessage());
            return false;
        }
    }
    return false;
}

/**
 * فانکشن برای دریافت یک ردیف از نتیجه کوئری
 * 
 * @param string $sql کوئری SQL
 * @param array $params پارامترهای کوئری
 * @return array|bool نتیجه کوئری یا false در صورت خطا
 */
function getRow($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    if ($stmt) {
        return $stmt->fetch();
    }
    return false;
}

/**
 * فانکشن برای دریافت همه ردیف‌های نتیجه کوئری
 * 
 * @param string $sql کوئری SQL
 * @param array $params پارامترهای کوئری
 * @return array|bool نتیجه کوئری یا false در صورت خطا
 */
function getAllRows($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    if ($stmt) {
        return $stmt->fetchAll();
    }
    return false;
}

/**
 * فانکشن برای اجرای کوئری‌های INSERT و بازگرداندن آخرین ID
 * 
 * @param string $sql کوئری SQL
 * @param array $params پارامترهای کوئری
 * @return int|bool آخرین ID یا false در صورت خطا
 */
function insertRow($sql, $params = [])
{
    $conn = connectDB();
    if ($conn) {
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("خطا در اجرای کوئری: " . $e->getMessage());
            return false;
        }
    }
    return false;
}
