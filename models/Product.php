<?php

/**
 * مدل محصول
 */
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = connectDB();
    }

    /**
     * دریافت تمام محصولات
     * 
     * @param int $limit تعداد محصولات
     * @param int $offset شروع از
     * @param string $orderBy ترتیب
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getAllProducts($limit = 12, $offset = 0, $orderBy = 'id DESC')
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                WHERE p.status = 'active'
                ORDER BY p.$orderBy
                LIMIT :offset, :limit
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت لیست محصولات: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محصولات ویژه
     * 
     * @param int $limit تعداد محصولات
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getFeaturedProducts($limit = 8)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                WHERE p.status = 'active' AND p.featured = 1
                ORDER BY RAND()
                LIMIT :limit
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت محصولات ویژه: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محصولات جدید
     * 
     * @param int $limit تعداد محصولات
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getNewProducts($limit = 8)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC
                LIMIT :limit
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت محصولات جدید: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محصولات تخفیف‌دار
     * 
     * @param int $limit تعداد محصولات
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getDiscountedProducts($limit = 8)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                WHERE p.status = 'active' AND p.discount_price IS NOT NULL AND p.discount_price > 0
                ORDER BY (p.price - p.discount_price) DESC
                LIMIT :limit
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت محصولات تخفیف‌دار: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محصولات مرتبط
     * 
     * @param int $productId آیدی محصول فعلی
     * @param int $limit تعداد محصولات
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getRelatedProducts($productId, $limit = 4)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                INNER JOIN product_categories pc1 ON p.id = pc1.product_id
                INNER JOIN product_categories pc2 ON pc1.category_id = pc2.category_id
                WHERE p.status = 'active' 
                    AND pc2.product_id = :product_id 
                    AND p.id != :product_id
                GROUP BY p.id
                ORDER BY RAND()
                LIMIT :limit
            ");

            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت محصولات مرتبط: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محصول با آیدی
     * 
     * @param int $productId آیدی محصول
     * @return array|false اطلاعات محصول یا false در صورت خطا
     */
    public function getProductById($productId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM products
                WHERE id = :id AND status = 'active'
            ");

            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return false;
            }

            $product = $stmt->fetch();

            // دریافت تصاویر محصول
            $product['images'] = $this->getProductImages($productId);

            // دریافت دسته‌بندی‌های محصول
            $product['categories'] = $this->getProductCategories($productId);

            // دریافت ویژگی‌های محصول
            $product['attributes'] = $this->getProductAttributes($productId);

            return $product;
        } catch (PDOException $e) {
            error_log("خطا در دریافت اطلاعات محصول: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت محصول با اسلاگ
     * 
     * @param string $slug اسلاگ محصول
     * @return array|false اطلاعات محصول یا false در صورت خطا
     */
    public function getProductBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM products
                WHERE slug = :slug AND status = 'active'
            ");

            $stmt->bindParam(':slug', $slug);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return false;
            }

            $product = $stmt->fetch();

            // دریافت تصاویر محصول
            $product['images'] = $this->getProductImages($product['id']);

            // دریافت دسته‌بندی‌های محصول
            $product['categories'] = $this->getProductCategories($product['id']);

            // دریافت ویژگی‌های محصول
            $product['attributes'] = $this->getProductAttributes($product['id']);

            return $product;
        } catch (PDOException $e) {
            error_log("خطا در دریافت اطلاعات محصول با اسلاگ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت تصاویر محصول
     * 
     * @param int $productId آیدی محصول
     * @return array|false لیست تصاویر یا false در صورت خطا
     */
    public function getProductImages($productId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM product_images
                WHERE product_id = :product_id
                ORDER BY is_main DESC, sort_order ASC
            ");

            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت تصاویر محصول: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت دسته‌بندی‌های محصول
     * 
     * @param int $productId آیدی محصول
     * @return array|false لیست دسته‌بندی‌ها یا false در صورت خطا
     */
    public function getProductCategories($productId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.* FROM categories c
                INNER JOIN product_categories pc ON c.id = pc.category_id
                WHERE pc.product_id = :product_id
            ");

            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت دسته‌بندی‌های محصول: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت ویژگی‌های محصول
     * 
     * @param int $productId آیدی محصول
     * @return array|false لیست ویژگی‌ها یا false در صورت خطا
     */
    public function getProductAttributes($productId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.name, av.value
                FROM attributes a
                INNER JOIN attribute_values av ON a.id = av.attribute_id
                INNER JOIN product_attributes pa ON av.id = pa.attribute_value_id
                WHERE pa.product_id = :product_id
                ORDER BY a.name
            ");

            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            $result = [];
            while ($row = $stmt->fetch()) {
                if (!isset($result[$row['name']])) {
                    $result[$row['name']] = [];
                }
                $result[$row['name']][] = $row['value'];
            }

            return $result;
        } catch (PDOException $e) {
            error_log("خطا در دریافت ویژگی‌های محصول: " . $e->getMessage());
            return false;
        }
    }

    /**
     * جستجوی محصولات
     * 
     * @param string $keyword کلمه کلیدی
     * @param int $limit تعداد محصولات
     * @param int $offset شروع از
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function searchProducts($keyword, $limit = 12, $offset = 0)
    {
        try {
            $keyword = "%$keyword%";

            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                WHERE p.status = 'active' AND (p.name LIKE :keyword OR p.description LIKE :keyword)
                ORDER BY p.name
                LIMIT :offset, :limit
            ");

            $stmt->bindParam(':keyword', $keyword);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در جستجوی محصولات: " . $e->getMessage());
            return false;
        }
    }

    /**
     * فیلتر محصولات بر اساس دسته‌بندی
     * 
     * @param int $categoryId آیدی دسته‌بندی
     * @param int $limit تعداد محصولات
     * @param int $offset شروع از
     * @param string $orderBy ترتیب
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getProductsByCategory($categoryId, $limit = 12, $offset = 0, $orderBy = 'id DESC')
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                WHERE p.status = 'active' AND pc.category_id = :category_id
                ORDER BY p.$orderBy
                LIMIT :offset, :limit
            ");

            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت محصولات دسته‌بندی: " . $e->getMessage());
            return false;
        }
    }

    /**
     * فیلتر محصولات بر اساس قیمت
     * 
     * @param int $minPrice حداقل قیمت
     * @param int $maxPrice حداکثر قیمت
     * @param int $limit تعداد محصولات
     * @param int $offset شروع از
     * @return array|false لیست محصولات یا false در صورت خطا
     */
    public function getProductsByPrice($minPrice, $maxPrice, $limit = 12, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*, 
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                WHERE p.status = 'active' 
                    AND (
                        (p.discount_price IS NOT NULL AND p.discount_price BETWEEN :min_price AND :max_price)
                        OR (p.discount_price IS NULL AND p.price BETWEEN :min_price AND :max_price)
                    )
                ORDER BY p.price
                LIMIT :offset, :limit
            ");

            $stmt->bindParam(':min_price', $minPrice, PDO::PARAM_INT);
            $stmt->bindParam(':max_price', $maxPrice, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در فیلتر محصولات بر اساس قیمت: " . $e->getMessage());
            return false;
        }
    }

    /**
     * شمارش تعداد کل محصولات
     * 
     * @return int|false تعداد محصولات یا false در صورت خطا
     */
    public function countProducts()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM products
                WHERE status = 'active'
            ");

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['total'];
        } catch (PDOException $e) {
            error_log("خطا در شمارش تعداد محصولات: " . $e->getMessage());
            return false;
        }
    }

    /**
     * شمارش تعداد محصولات در دسته‌بندی
     * 
     * @param int $categoryId آیدی دسته‌بندی
     * @return int|false تعداد محصولات یا false در صورت خطا
     */
    public function countProductsByCategory($categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM products p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                WHERE p.status = 'active' AND pc.category_id = :category_id
            ");

            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();

            return $result['total'];
        } catch (PDOException $e) {
            error_log("خطا در شمارش تعداد محصولات دسته‌بندی: " . $e->getMessage());
            return false;
        }
    }

    /**
     * شمارش تعداد محصولات جستجو شده
     * 
     * @param string $keyword کلمه کلیدی
     * @return int|false تعداد محصولات یا false در صورت خطا
     */
    public function countSearchProducts($keyword)
    {
        try {
            $keyword = "%$keyword%";

            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM products p
                WHERE p.status = 'active' AND (p.name LIKE :keyword OR p.description LIKE :keyword)
            ");

            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();
            $result = $stmt->fetch();

            return $result['total'];
        } catch (PDOException $e) {
            error_log("خطا در شمارش تعداد محصولات جستجو شده: " . $e->getMessage());
            return false;
        }
    }
}
