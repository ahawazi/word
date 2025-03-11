<?php

/**
 * مدل کاربر
 */
class User
{
    private $db;

    public function __construct()
    {
        $this->db = connectDB();
    }

    /**
     * ثبت نام کاربر جدید
     * 
     * @param string $name نام کاربر
     * @param string $email ایمیل کاربر
     * @param string $password رمز عبور کاربر
     * @param string $phone شماره تلفن کاربر (اختیاری)
     * @return int|false آیدی کاربر ثبت شده یا false در صورت خطا
     */
    public function register($name, $email, $password, $phone = null)
    {
        try {
            // بررسی وجود ایمیل
            if ($this->emailExists($email)) {
                return false;
            }

            // هش کردن رمز عبور
            $hashedPassword = hashPassword($password);

            // درج کاربر جدید
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, phone) 
                VALUES (:name, :email, :password, :phone)
            ");

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':phone', $phone);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در ثبت نام کاربر: " . $e->getMessage());
            return false;
        }
    }

    /**
     * بررسی وجود ایمیل در سیستم
     * 
     * @param string $email ایمیل کاربر
     * @return bool نتیجه بررسی
     */
    public function emailExists($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("خطا در بررسی وجود ایمیل: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ورود کاربر به سیستم
     * 
     * @param string $email ایمیل کاربر
     * @param string $password رمز عبور کاربر
     * @return array|false اطلاعات کاربر یا false در صورت خطا
     */
    public function login($email, $password)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, password, role 
                FROM users 
                WHERE email = :email
            ");

            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();

                // بررسی رمز عبور
                if (verifyPassword($password, $user['password'])) {
                    // حذف رمز عبور از آرایه برگشتی
                    unset($user['password']);
                    return $user;
                }
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در ورود کاربر: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت اطلاعات کاربر با آیدی
     * 
     * @param int $userId آیدی کاربر
     * @return array|false اطلاعات کاربر یا false در صورت خطا
     */
    public function getUserById($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, phone, address, postal_code, role, created_at 
                FROM users 
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch();
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در دریافت اطلاعات کاربر: " . $e->getMessage());
            return false;
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر
     * 
     * @param int $userId آیدی کاربر
     * @param array $userData اطلاعات کاربر
     * @return bool نتیجه به‌روزرسانی
     */
    public function updateUser($userId, $userData)
    {
        try {
            $updateFields = [];
            $params = [':id' => $userId];

            // تهیه رشته‌های مورد نیاز برای به‌روزرسانی
            foreach ($userData as $field => $value) {
                if (in_array($field, ['name', 'phone', 'address', 'postal_code'])) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $updateString = implode(', ', $updateFields);

            $stmt = $this->db->prepare("
                UPDATE users 
                SET $updateString 
                WHERE id = :id
            ");

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("خطا در به‌روزرسانی اطلاعات کاربر: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تغییر رمز عبور کاربر
     * 
     * @param int $userId آیدی کاربر
     * @param string $currentPassword رمز عبور فعلی
     * @param string $newPassword رمز عبور جدید
     * @return bool نتیجه تغییر رمز عبور
     */
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        try {
            // دریافت رمز عبور فعلی
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();

                // بررسی رمز عبور فعلی
                if (verifyPassword($currentPassword, $user['password'])) {
                    // هش کردن رمز عبور جدید
                    $hashedPassword = hashPassword($newPassword);

                    // به‌روزرسانی رمز عبور
                    $updateStmt = $this->db->prepare("
                        UPDATE users 
                        SET password = :password 
                        WHERE id = :id
                    ");

                    $updateStmt->bindParam(':password', $hashedPassword);
                    $updateStmt->bindParam(':id', $userId);

                    return $updateStmt->execute();
                }
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در تغییر رمز عبور: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت لیست آدرس‌های کاربر
     * 
     * @param int $userId آیدی کاربر
     * @return array|false لیست آدرس‌ها یا false در صورت خطا
     */
    public function getUserAddresses($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_addresses 
                WHERE user_id = :user_id 
                ORDER BY is_default DESC, id DESC
            ");

            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت آدرس‌های کاربر: " . $e->getMessage());
            return false;
        }
    }

    /**
     * افزودن آدرس جدید برای کاربر
     * 
     * @param int $userId آیدی کاربر
     * @param array $addressData اطلاعات آدرس
     * @return int|false آیدی آدرس یا false در صورت خطا
     */
    public function addAddress($userId, $addressData)
    {
        try {
            // اگر این آدرس، آدرس پیش‌فرض است، ابتدا همه آدرس‌ها را غیر پیش‌فرض می‌کنیم
            if (isset($addressData['is_default']) && $addressData['is_default'] == 1) {
                $this->resetDefaultAddresses($userId);
            }

            $stmt = $this->db->prepare("
                INSERT INTO user_addresses (
                    user_id, province, city, address, 
                    postal_code, receiver_name, receiver_phone, is_default
                ) 
                VALUES (
                    :user_id, :province, :city, :address, 
                    :postal_code, :receiver_name, :receiver_phone, :is_default
                )
            ");

            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':province', $addressData['province']);
            $stmt->bindParam(':city', $addressData['city']);
            $stmt->bindParam(':address', $addressData['address']);
            $stmt->bindParam(':postal_code', $addressData['postal_code']);
            $stmt->bindParam(':receiver_name', $addressData['receiver_name']);
            $stmt->bindParam(':receiver_phone', $addressData['receiver_phone']);
            $stmt->bindParam(':is_default', $addressData['is_default']);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("خطا در افزودن آدرس جدید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * به‌روزرسانی آدرس کاربر
     * 
     * @param int $addressId آیدی آدرس
     * @param int $userId آیدی کاربر
     * @param array $addressData اطلاعات آدرس
     * @return bool نتیجه به‌روزرسانی
     */
    public function updateAddress($addressId, $userId, $addressData)
    {
        try {
            // اگر این آدرس، آدرس پیش‌فرض است، ابتدا همه آدرس‌ها را غیر پیش‌فرض می‌کنیم
            if (isset($addressData['is_default']) && $addressData['is_default'] == 1) {
                $this->resetDefaultAddresses($userId);
            }

            $stmt = $this->db->prepare("
                UPDATE user_addresses 
                SET province = :province, 
                    city = :city, 
                    address = :address, 
                    postal_code = :postal_code, 
                    receiver_name = :receiver_name, 
                    receiver_phone = :receiver_phone, 
                    is_default = :is_default 
                WHERE id = :id AND user_id = :user_id
            ");

            $stmt->bindParam(':id', $addressId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':province', $addressData['province']);
            $stmt->bindParam(':city', $addressData['city']);
            $stmt->bindParam(':address', $addressData['address']);
            $stmt->bindParam(':postal_code', $addressData['postal_code']);
            $stmt->bindParam(':receiver_name', $addressData['receiver_name']);
            $stmt->bindParam(':receiver_phone', $addressData['receiver_phone']);
            $stmt->bindParam(':is_default', $addressData['is_default']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در به‌روزرسانی آدرس: " . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف آدرس کاربر
     * 
     * @param int $addressId آیدی آدرس
     * @param int $userId آیدی کاربر
     * @return bool نتیجه حذف
     */
    public function deleteAddress($addressId, $userId)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM user_addresses 
                WHERE id = :id AND user_id = :user_id
            ");

            $stmt->bindParam(':id', $addressId);
            $stmt->bindParam(':user_id', $userId);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در حذف آدرس: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تنظیم همه آدرس‌های کاربر به عنوان غیر پیش‌فرض
     * 
     * @param int $userId آیدی کاربر
     * @return bool نتیجه عملیات
     */
    private function resetDefaultAddresses($userId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE user_addresses 
                SET is_default = 0 
                WHERE user_id = :user_id
            ");

            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("خطا در تنظیم آدرس‌های پیش‌فرض: " . $e->getMessage());
            return false;
        }
    }
}
