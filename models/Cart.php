<?php

/**
 * مدل سبد خرید
 */
class Cart
{
    private $db;

    public function __construct()
    {
        $this->db = connectDB();
    }

    /**
     * ایجاد یا دریافت سبد خرید
     * 
     * @param int|null $userId آیدی کاربر (null برای کاربران مهمان)
     * @param string|null $sessionId شناسه جلسه (برای کاربران مهمان)
     * @return int|false آیدی سبد خرید یا false در صورت خطا
     */
    public function getCart($userId = null, $sessionId = null)
    {
        try {
            // جستجوی سبد خرید موجود
            if ($userId !== null) {
                // کاربران ثبت نام شده
                $stmt = $this->db->prepare("SELECT id FROM cart WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
            } else if ($sessionId !== null) {
                // کاربران مهمان
                $stmt = $this->db->prepare("SELECT id FROM cart WHERE session_id = :session_id");
                $stmt->bindParam(':session_id', $sessionId);
                $stmt->execute();
            } else {
                return false;
            }

            if ($stmt->rowCount() > 0) {
                $cart = $stmt->fetch();
                return $cart['id'];
            }

            // ایجاد سبد خرید جدید
            $stmt = $this->db->prepare("
                INSERT INTO cart (user_id, session_id) 
                VALUES (:user_id, :session_id)
            ");

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':session_id', $sessionId);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در ایجاد یا دریافت سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ادغام سبدهای خرید (برای زمان ورود کاربران)
     * 
     * @param int $sessionCartId آیدی سبد خرید جلسه
     * @param int $userCartId آیدی سبد خرید کاربر
     * @return bool نتیجه ادغام
     */
    public function mergeCarts($sessionCartId, $userCartId)
    {
        try {
            // انتقال آیتم‌ها از سبد جلسه به سبد کاربر
            $stmt = $this->db->prepare("
                INSERT INTO cart_items (cart_id, product_id, quantity)
                SELECT :user_cart_id, product_id, quantity
                FROM cart_items
                WHERE cart_id = :session_cart_id
                ON DUPLICATE KEY UPDATE 
                    quantity = cart_items.quantity + VALUES(quantity)
            ");

            $stmt->bindParam(':session_cart_id', $sessionCartId, PDO::PARAM_INT);
            $stmt->bindParam(':user_cart_id', $userCartId, PDO::PARAM_INT);
            $stmt->execute();

            // حذف سبد جلسه
            $deleteStmt = $this->db->prepare("DELETE FROM cart WHERE id = :cart_id");
            $deleteStmt->bindParam(':cart_id', $sessionCartId, PDO::PARAM_INT);

            return $deleteStmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در ادغام سبدهای خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * افزودن محصول به سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @param int $productId آیدی محصول
     * @param int $quantity تعداد
     * @return bool نتیجه افزودن
     */
    public function addToCart($cartId, $productId, $quantity = 1)
    {
        try {
            // بررسی وجود محصول در سبد خرید
            $stmt = $this->db->prepare("
                SELECT id, quantity FROM cart_items
                WHERE cart_id = :cart_id AND product_id = :product_id
            ");

            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // به‌روزرسانی تعداد
                $item = $stmt->fetch();
                $newQuantity = $item['quantity'] + $quantity;

                $updateStmt = $this->db->prepare("
                    UPDATE cart_items
                    SET quantity = :quantity
                    WHERE id = :id
                ");

                $updateStmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
                $updateStmt->bindParam(':id', $item['id'], PDO::PARAM_INT);

                return $updateStmt->execute();
            } else {
                // افزودن آیتم جدید
                $insertStmt = $this->db->prepare("
                    INSERT INTO cart_items (cart_id, product_id, quantity)
                    VALUES (:cart_id, :product_id, :quantity)
                ");

                $insertStmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
                $insertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $insertStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                return $insertStmt->execute();
            }
        } catch (PDOException $e) {
            error_log("خطا در افزودن محصول به سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * به‌روزرسانی تعداد محصول در سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @param int $productId آیدی محصول
     * @param int $quantity تعداد جدید
     * @return bool نتیجه به‌روزرسانی
     */
    public function updateCartItem($cartId, $productId, $quantity)
    {
        try {
            if ($quantity <= 0) {
                // حذف محصول از سبد خرید
                return $this->removeFromCart($cartId, $productId);
            }

            $stmt = $this->db->prepare("
                UPDATE cart_items
                SET quantity = :quantity
                WHERE cart_id = :cart_id AND product_id = :product_id
            ");

            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در به‌روزرسانی تعداد محصول در سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف محصول از سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @param int $productId آیدی محصول
     * @return bool نتیجه حذف
     */
    public function removeFromCart($cartId, $productId)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM cart_items
                WHERE cart_id = :cart_id AND product_id = :product_id
            ");

            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در حذف محصول از سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * خالی کردن سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @return bool نتیجه خالی کردن
     */
    public function emptyCart($cartId)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM cart_items
                WHERE cart_id = :cart_id
            ");

            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در خالی کردن سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محتوای سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @return array|false محتوای سبد خرید یا false در صورت خطا
     */
    public function getCartItems($cartId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT ci.*, p.name, p.slug, p.price, p.discount_price,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image
                FROM cart_items ci
                INNER JOIN products p ON ci.product_id = p.id
                WHERE ci.cart_id = :cart_id
            ");

            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت محتوای سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * محاسبه تعداد آیتم‌های سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @return int|false تعداد آیتم‌ها یا false در صورت خطا
     */
    public function countCartItems($cartId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT SUM(quantity) as count
                FROM cart_items
                WHERE cart_id = :cart_id
            ");

            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result['count'] ? (int)$result['count'] : 0;
        } catch (PDOException $e) {
            error_log("خطا در محاسبه تعداد آیتم‌های سبد خرید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * محاسبه مجموع قیمت سبد خرید
     * 
     * @param int $cartId آیدی سبد خرید
     * @return array|false مجموع قیمت یا false در صورت خطا
     */
    public function calculateCartTotal($cartId)
    {
        try {
            $items = $this->getCartItems($cartId);

            if ($items === false) {
                return false;
            }

            $total = 0;
            $discountTotal = 0;

            foreach ($items as $item) {
                $itemPrice = $item['discount_price'] ? $item['discount_price'] : $item['price'];
                $itemTotal = $itemPrice * $item['quantity'];
                $total += $itemTotal;

                if ($item['discount_price']) {
                    $discountTotal += ($item['price'] - $item['discount_price']) * $item['quantity'];
                }
            }

            return [
                'total' => $total,
                'discount' => $discountTotal,
                'original_total' => $total + $discountTotal
            ];
        } catch (Exception $e) {
            error_log("خطا در محاسبه مجموع قیمت سبد خرید: " . $e->getMessage());
            return false;
        }
    }
}
