<?php

/**
 * مدل سفارش
 */
class Order
{
    private $db;

    public function __construct()
    {
        $this->db = connectDB();
    }

    /**
     * ایجاد سفارش جدید
     * 
     * @param int $userId آیدی کاربر
     * @param int $cartId آیدی سبد خرید
     * @param array $orderData اطلاعات سفارش
     * @return int|false آیدی سفارش یا false در صورت خطا
     */
    public function createOrder($userId, $cartId, $orderData)
    {
        try {
            $this->db->beginTransaction();

            // دریافت آیتم‌های سبد خرید
            $cart = new Cart();
            $cartItems = $cart->getCartItems($cartId);

            if (!$cartItems || empty($cartItems)) {
                $this->db->rollBack();
                return false;
            }

            // محاسبه مجموع قیمت
            $cartTotal = $cart->calculateCartTotal($cartId);
            if (!$cartTotal) {
                $this->db->rollBack();
                return false;
            }

            // شروع با فرض صفر برای هزینه ارسال و تخفیف
            $shippingPrice = isset($orderData['shipping_price']) ? $orderData['shipping_price'] : 0;
            $discountAmount = isset($orderData['discount_amount']) ? $orderData['discount_amount'] : 0;

            // محاسبه قیمت نهایی
            $finalPrice = $cartTotal['total'] + $shippingPrice - $discountAmount;

            // درج سفارش
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, total_price, shipping_price, discount_amount, 
                    final_price, shipping_address_id, notes
                ) 
                VALUES (
                    :user_id, :total_price, :shipping_price, :discount_amount, 
                    :final_price, :shipping_address_id, :notes
                )
            ");

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':total_price', $cartTotal['total'], PDO::PARAM_INT);
            $stmt->bindParam(':shipping_price', $shippingPrice, PDO::PARAM_INT);
            $stmt->bindParam(':discount_amount', $discountAmount, PDO::PARAM_INT);
            $stmt->bindParam(':final_price', $finalPrice, PDO::PARAM_INT);
            $stmt->bindParam(':shipping_address_id', $orderData['shipping_address_id'], PDO::PARAM_INT);
            $stmt->bindParam(':notes', $orderData['notes']);

            if (!$stmt->execute()) {
                $this->db->rollBack();
                return false;
            }

            $orderId = $this->db->lastInsertId();

            // درج آیتم‌های سفارش
            foreach ($cartItems as $item) {
                $itemPrice = $item['price'];
                $discountPrice = $item['discount_price'];
                $finalItemPrice = $discountPrice ? $discountPrice : $itemPrice;

                $stmt = $this->db->prepare("
                    INSERT INTO order_items (
                        order_id, product_id, quantity, 
                        price, discount_price, final_price
                    ) 
                    VALUES (
                        :order_id, :product_id, :quantity, 
                        :price, :discount_price, :final_price
                    )
                ");

                $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $stmt->bindParam(':price', $itemPrice, PDO::PARAM_INT);
                $stmt->bindParam(':discount_price', $discountPrice, PDO::PARAM_INT);
                $stmt->bindParam(':final_price', $finalItemPrice, PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $this->db->rollBack();
                    return false;
                }
            }

            // خالی کردن سبد خرید
            $cart->emptyCart($cartId);

            $this->db->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("خطا در ایجاد سفارش جدید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت سفارش با آیدی
     * 
     * @param int $orderId آیدی سفارش
     * @param int $userId آیدی کاربر (اختیاری - برای بررسی مالکیت)
     * @return array|false اطلاعات سفارش یا false در صورت خطا
     */
    public function getOrderById($orderId, $userId = null)
    {
        try {
            $query = "
                SELECT o.*, ua.* 
                FROM orders o
                LEFT JOIN user_addresses ua ON o.shipping_address_id = ua.id
                WHERE o.id = :id
            ";

            if ($userId !== null) {
                $query .= " AND o.user_id = :user_id";
            }

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

            if ($userId !== null) {
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            }

            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return false;
            }

            $order = $stmt->fetch();

            // دریافت آیتم‌های سفارش
            $order['items'] = $this->getOrderItems($orderId);

            // دریافت پرداخت‌های سفارش
            $order['payments'] = $this->getOrderPayments($orderId);

            return $order;
        } catch (PDOException $e) {
            error_log("خطا در دریافت اطلاعات سفارش: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت آیتم‌های سفارش
     * 
     * @param int $orderId آیدی سفارش
     * @return array|false لیست آیتم‌ها یا false در صورت خطا
     */
    public function getOrderItems($orderId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT oi.*, p.name, p.slug,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");

            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت آیتم‌های سفارش: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت پرداخت‌های سفارش
     * 
     * @param int $orderId آیدی سفارش
     * @return array|false لیست پرداخت‌ها یا false در صورت خطا
     */
    public function getOrderPayments($orderId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payments
                WHERE order_id = :order_id
                ORDER BY created_at DESC
            ");

            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت پرداخت‌های سفارش: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت سفارش‌های کاربر
     * 
     * @param int $userId آیدی کاربر
     * @return array|false لیست سفارش‌ها یا false در صورت خطا
     */
    public function getUserOrders($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, 
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                FROM orders o
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC
            ");

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت سفارش‌های کاربر: " . $e->getMessage());
            return false;
        }
    }

    /**
     * به‌روزرسانی وضعیت سفارش
     * 
     * @param int $orderId آیدی سفارش
     * @param string $status وضعیت جدید
     * @return bool نتیجه به‌روزرسانی
     */
    public function updateOrderStatus($orderId, $status)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE orders
                SET order_status = :status
                WHERE id = :id
            ");

            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در به‌روزرسانی وضعیت سفارش: " . $e->getMessage());
            return false;
        }
    }

    /**
     * به‌روزرسانی وضعیت پرداخت سفارش
     * 
     * @param int $orderId آیدی سفارش
     * @param string $status وضعیت جدید
     * @return bool نتیجه به‌روزرسانی
     */
    public function updatePaymentStatus($orderId, $status)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE orders
                SET payment_status = :status
                WHERE id = :id
            ");

            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در به‌روزرسانی وضعیت پرداخت سفارش: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ثبت پرداخت جدید
     * 
     * @param int $orderId آیدی سفارش
     * @param array $paymentData اطلاعات پرداخت
     * @return int|false آیدی پرداخت یا false در صورت خطا
     */
    public function addPayment($orderId, $paymentData)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    order_id, amount, transaction_id, 
                    payment_method, status
                ) 
                VALUES (
                    :order_id, :amount, :transaction_id, 
                    :payment_method, :status
                )
            ");

            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $paymentData['amount'], PDO::PARAM_INT);
            $stmt->bindParam(':transaction_id', $paymentData['transaction_id']);
            $stmt->bindParam(':payment_method', $paymentData['payment_method']);
            $stmt->bindParam(':status', $paymentData['status']);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در ثبت پرداخت جدید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * به‌روزرسانی اطلاعات پرداخت
     * 
     * @param int $paymentId آیدی پرداخت
     * @param array $paymentData اطلاعات پرداخت
     * @return bool نتیجه به‌روزرسانی
     */
    public function updatePayment($paymentId, $paymentData)
    {
        try {
            $updateFields = [];
            $params = [':id' => $paymentId];

            // تهیه رشته‌های مورد نیاز برای به‌روزرسانی
            foreach ($paymentData as $field => $value) {
                if (in_array($field, ['transaction_id', 'status', 'payment_date'])) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $updateString = implode(', ', $updateFields);

            $stmt = $this->db->prepare("
                UPDATE payments 
                SET $updateString 
                WHERE id = :id
            ");

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("خطا در به‌روزرسانی اطلاعات پرداخت: " . $e->getMessage());
            return false;
        }
    }
}
