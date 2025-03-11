-- ایجاد پایگاه داده
CREATE DATABASE IF NOT EXISTS `tasmeh_shop` CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci;

-- استفاده از پایگاه داده
USE `tasmeh_shop`;

-- درج جداول کاربران
SOURCE users.sql;

-- درج جداول محصولات
SOURCE products.sql;

-- درج جداول سفارش‌ها
SOURCE orders.sql;

-- افزودن کاربر ادمین پیش‌فرض
INSERT INTO `users` (`name`, `email`, `password`, `role`) 
VALUES ('مدیر سایت', 'admin@example.com', '$2y$10$XkNDlYPKnXxRgV7JvZzw.ecBKVYGDmh7.Tg5HbxOZ5ztOoJQKfeuS', 'admin');
-- رمز عبور: Admin@123 