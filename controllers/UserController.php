<?php

/**
 * کنترلر کاربران
 */
class UserController
{
    private $userModel;

    public function __construct()
    {
        // شروع جلسه
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new User();
    }

    /**
     * نمایش فرم ثبت نام
     */
    public function register()
    {
        // بررسی ورود کاربر
        if ($this->isLoggedIn()) {
            // اگر قبلاً وارد شده باشد، به صفحه حساب کاربری هدایت می‌شود
            header('Location: profile.php');
            exit;
        }

        // در صورت ارسال فرم
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // بررسی توکن CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                setMessage('خطا در اعتبارسنجی فرم. لطفاً دوباره تلاش کنید.', 'error');
                header('Location: register.php');
                exit;
            }

            // دریافت اطلاعات فرم
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

            // اعتبارسنجی اطلاعات
            $errors = [];

            if (empty($name)) {
                $errors[] = 'نام و نام خانوادگی الزامی است.';
            }

            if (empty($email)) {
                $errors[] = 'آدرس ایمیل الزامی است.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'فرمت ایمیل نامعتبر است.';
            } elseif ($this->userModel->emailExists($email)) {
                $errors[] = 'این ایمیل قبلاً ثبت شده است.';
            }

            if (empty($password)) {
                $errors[] = 'رمز عبور الزامی است.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'رمز عبور باید حداقل 6 کاراکتر باشد.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'تکرار رمز عبور مطابقت ندارد.';
            }

            // اگر خطایی وجود نداشت، ثبت نام انجام شود
            if (empty($errors)) {
                $userId = $this->userModel->register($name, $email, $password, $phone);

                if ($userId) {
                    // ثبت نام موفق
                    setMessage('ثبت نام با موفقیت انجام شد. اکنون می‌توانید وارد شوید.', 'success');
                    header('Location: login.php');
                    exit;
                } else {
                    setMessage('خطا در ثبت نام. لطفاً دوباره تلاش کنید.', 'error');
                }
            } else {
                // نمایش خطاها
                $_SESSION['register_errors'] = $errors;

                // حفظ مقادیر فرم
                $_SESSION['register_form'] = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone
                ];
            }
        }

        // نمایش فرم ثبت نام
        include 'views/user/register.php';
    }

    /**
     * نمایش فرم ورود
     */
    public function login()
    {
        // بررسی ورود کاربر
        if ($this->isLoggedIn()) {
            // اگر قبلاً وارد شده باشد، به صفحه حساب کاربری هدایت می‌شود
            header('Location: profile.php');
            exit;
        }

        // در صورت ارسال فرم
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // بررسی توکن CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                setMessage('خطا در اعتبارسنجی فرم. لطفاً دوباره تلاش کنید.', 'error');
                header('Location: login.php');
                exit;
            }

            // دریافت اطلاعات فرم
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $remember = isset($_POST['remember']) ? true : false;

            // اعتبارسنجی اطلاعات
            $errors = [];

            if (empty($email)) {
                $errors[] = 'آدرس ایمیل الزامی است.';
            }

            if (empty($password)) {
                $errors[] = 'رمز عبور الزامی است.';
            }

            // اگر خطایی وجود نداشت، ورود انجام شود
            if (empty($errors)) {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // ورود موفق
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    // اگر سبد خرید در session موجود است با سبد خرید کاربر ادغام کن
                    if (isset($_SESSION['cart_id']) && $_SESSION['cart_id']) {
                        $sessionCartId = $_SESSION['cart_id'];
                        $cartModel = new Cart();
                        $userCartId = $cartModel->getCart($user['id']);
                        if ($userCartId) {
                            $cartModel->mergeCarts($sessionCartId, $userCartId);
                        }
                        unset($_SESSION['cart_id']);
                    }

                    // اگر مسیر بازگشت وجود داشت، به آن مسیر برگردد
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirectUrl = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header("Location: $redirectUrl");
                        exit;
                    }

                    // در غیر این صورت به صفحه حساب کاربری برود
                    header('Location: profile.php');
                    exit;
                } else {
                    setMessage('ایمیل یا رمز عبور اشتباه است.', 'error');
                }
            } else {
                // نمایش خطاها
                $_SESSION['login_errors'] = $errors;

                // حفظ مقادیر فرم
                $_SESSION['login_form'] = [
                    'email' => $email
                ];
            }
        }

        // نمایش فرم ورود
        include 'views/user/login.php';
    }

    /**
     * خروج کاربر
     */
    public function logout()
    {
        // حذف اطلاعات کاربر از جلسه
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);

        // انتقال به صفحه اصلی
        setMessage('با موفقیت خارج شدید.', 'success');
        header('Location: index.php');
        exit;
    }

    /**
     * نمایش پروفایل کاربر
     */
    public function profile()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به حساب کاربری، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        // دریافت اطلاعات کاربر
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            setMessage('خطا در دریافت اطلاعات کاربر.', 'error');
            header('Location: index.php');
            exit;
        }

        // نمایش صفحه پروفایل
        include 'views/user/profile.php';
    }

    /**
     * ویرایش پروفایل کاربر
     */
    public function editProfile()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            setMessage('خطا در دریافت اطلاعات کاربر.', 'error');
            header('Location: profile.php');
            exit;
        }

        // در صورت ارسال فرم
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // بررسی توکن CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                setMessage('خطا در اعتبارسنجی فرم. لطفاً دوباره تلاش کنید.', 'error');
                header('Location: edit-profile.php');
                exit;
            }

            // دریافت اطلاعات فرم
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $postalCode = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';

            // اعتبارسنجی اطلاعات
            $errors = [];

            if (empty($name)) {
                $errors[] = 'نام و نام خانوادگی الزامی است.';
            }

            // اگر خطایی وجود نداشت، به‌روزرسانی انجام شود
            if (empty($errors)) {
                $userData = [
                    'name' => $name,
                    'phone' => $phone,
                    'address' => $address,
                    'postal_code' => $postalCode
                ];

                $result = $this->userModel->updateUser($userId, $userData);

                if ($result) {
                    // به‌روزرسانی نام کاربر در جلسه
                    $_SESSION['user_name'] = $name;

                    setMessage('اطلاعات حساب کاربری با موفقیت به‌روزرسانی شد.', 'success');
                    header('Location: profile.php');
                    exit;
                } else {
                    setMessage('خطا در به‌روزرسانی اطلاعات. لطفاً دوباره تلاش کنید.', 'error');
                }
            } else {
                // نمایش خطاها
                $_SESSION['edit_profile_errors'] = $errors;

                // حفظ مقادیر فرم
                $_SESSION['edit_profile_form'] = [
                    'name' => $name,
                    'phone' => $phone,
                    'address' => $address,
                    'postal_code' => $postalCode
                ];
            }
        }

        // نمایش فرم ویرایش پروفایل
        include 'views/user/edit_profile.php';
    }

    /**
     * تغییر رمز عبور
     */
    public function changePassword()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        // در صورت ارسال فرم
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // بررسی توکن CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                setMessage('خطا در اعتبارسنجی فرم. لطفاً دوباره تلاش کنید.', 'error');
                header('Location: change-password.php');
                exit;
            }

            // دریافت اطلاعات فرم
            $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // اعتبارسنجی اطلاعات
            $errors = [];

            if (empty($currentPassword)) {
                $errors[] = 'رمز عبور فعلی الزامی است.';
            }

            if (empty($newPassword)) {
                $errors[] = 'رمز عبور جدید الزامی است.';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'رمز عبور جدید باید حداقل 6 کاراکتر باشد.';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'تکرار رمز عبور جدید مطابقت ندارد.';
            }

            // اگر خطایی وجود نداشت، تغییر رمز عبور انجام شود
            if (empty($errors)) {
                $userId = $_SESSION['user_id'];
                $result = $this->userModel->changePassword($userId, $currentPassword, $newPassword);

                if ($result) {
                    setMessage('رمز عبور با موفقیت تغییر یافت.', 'success');
                    header('Location: profile.php');
                    exit;
                } else {
                    setMessage('رمز عبور فعلی اشتباه است یا خطایی رخ داده است.', 'error');
                }
            } else {
                // نمایش خطاها
                $_SESSION['change_password_errors'] = $errors;
            }
        }

        // نمایش فرم تغییر رمز عبور
        include 'views/user/change_password.php';
    }

    /**
     * مدیریت آدرس‌ها
     */
    public function addresses()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // دریافت لیست آدرس‌ها
        $addresses = $this->userModel->getUserAddresses($userId);

        // نمایش صفحه مدیریت آدرس‌ها
        include 'views/user/addresses.php';
    }

    /**
     * افزودن یا ویرایش آدرس
     */
    public function editAddress()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $addressId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $address = null;

        // اگر ویرایش است، اطلاعات آدرس را دریافت کنیم
        if ($addressId > 0) {
            $addresses = $this->userModel->getUserAddresses($userId);
            foreach ($addresses as $addr) {
                if ($addr['id'] == $addressId) {
                    $address = $addr;
                    break;
                }
            }

            // اگر آدرس موجود نبود یا متعلق به کاربر نبود
            if (!$address) {
                setMessage('آدرس موردنظر یافت نشد.', 'error');
                header('Location: addresses.php');
                exit;
            }
        }

        // در صورت ارسال فرم
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // بررسی توکن CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                setMessage('خطا در اعتبارسنجی فرم. لطفاً دوباره تلاش کنید.', 'error');
                header('Location: edit-address.php' . ($addressId ? "?id=$addressId" : ''));
                exit;
            }

            // دریافت اطلاعات فرم
            $province = isset($_POST['province']) ? trim($_POST['province']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $addressText = isset($_POST['address']) ? trim($_POST['address']) : '';
            $postalCode = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
            $receiverName = isset($_POST['receiver_name']) ? trim($_POST['receiver_name']) : '';
            $receiverPhone = isset($_POST['receiver_phone']) ? trim($_POST['receiver_phone']) : '';
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            // اعتبارسنجی اطلاعات
            $errors = [];

            if (empty($province)) $errors[] = 'استان الزامی است.';
            if (empty($city)) $errors[] = 'شهر الزامی است.';
            if (empty($addressText)) $errors[] = 'آدرس الزامی است.';
            if (empty($postalCode)) $errors[] = 'کد پستی الزامی است.';
            if (empty($receiverName)) $errors[] = 'نام گیرنده الزامی است.';
            if (empty($receiverPhone)) $errors[] = 'شماره تماس گیرنده الزامی است.';

            // اگر خطایی وجود نداشت، عملیات ذخیره انجام شود
            if (empty($errors)) {
                $addressData = [
                    'province' => $province,
                    'city' => $city,
                    'address' => $addressText,
                    'postal_code' => $postalCode,
                    'receiver_name' => $receiverName,
                    'receiver_phone' => $receiverPhone,
                    'is_default' => $isDefault
                ];

                if ($addressId > 0) {
                    // ویرایش آدرس
                    $result = $this->userModel->updateAddress($addressId, $userId, $addressData);
                    $message = 'آدرس با موفقیت ویرایش شد.';
                } else {
                    // افزودن آدرس جدید
                    $result = $this->userModel->addAddress($userId, $addressData);
                    $message = 'آدرس جدید با موفقیت ذخیره شد.';
                }

                if ($result) {
                    setMessage($message, 'success');
                    header('Location: addresses.php');
                    exit;
                } else {
                    setMessage('خطا در ذخیره اطلاعات. لطفاً دوباره تلاش کنید.', 'error');
                }
            } else {
                // نمایش خطاها
                $_SESSION['address_errors'] = $errors;

                // حفظ مقادیر فرم
                $_SESSION['address_form'] = [
                    'province' => $province,
                    'city' => $city,
                    'address' => $addressText,
                    'postal_code' => $postalCode,
                    'receiver_name' => $receiverName,
                    'receiver_phone' => $receiverPhone,
                    'is_default' => $isDefault
                ];
            }
        }

        // نمایش فرم افزودن/ویرایش آدرس
        include 'views/user/edit_address.php';
    }

    /**
     * حذف آدرس
     */
    public function deleteAddress()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        // بررسی روش درخواست
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: addresses.php');
            exit;
        }

        // بررسی توکن CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            setMessage('خطا در اعتبارسنجی فرم. لطفاً دوباره تلاش کنید.', 'error');
            header('Location: addresses.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $addressId = isset($_POST['address_id']) ? (int)$_POST['address_id'] : 0;

        if ($addressId > 0) {
            $result = $this->userModel->deleteAddress($addressId, $userId);

            if ($result) {
                setMessage('آدرس با موفقیت حذف شد.', 'success');
            } else {
                setMessage('خطا در حذف آدرس. لطفاً دوباره تلاش کنید.', 'error');
            }
        }

        header('Location: addresses.php');
        exit;
    }

    /**
     * نمایش سفارش‌های کاربر
     */
    public function orders()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // دریافت لیست سفارش‌ها
        $orderModel = new Order();
        $orders = $orderModel->getUserOrders($userId);

        // نمایش صفحه سفارش‌ها
        include 'views/user/orders.php';
    }

    /**
     * نمایش جزئیات سفارش
     */
    public function orderDetail()
    {
        // بررسی ورود کاربر
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            setMessage('برای دسترسی به این صفحه، لطفاً وارد شوید.', 'info');
            header('Location: login.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($orderId <= 0) {
            setMessage('سفارش نامعتبر است.', 'error');
            header('Location: orders.php');
            exit;
        }

        // دریافت اطلاعات سفارش
        $orderModel = new Order();
        $order = $orderModel->getOrderById($orderId, $userId);

        if (!$order) {
            setMessage('سفارش موردنظر یافت نشد.', 'error');
            header('Location: orders.php');
            exit;
        }

        // نمایش صفحه جزئیات سفارش
        include 'views/user/order_detail.php';
    }

    /**
     * بررسی وضعیت ورود کاربر
     * 
     * @return bool نتیجه بررسی
     */
    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}
