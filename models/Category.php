<?php

/**
 * مدل دسته‌بندی
 */
class Category
{
    private $db;

    public function __construct()
    {
        $this->db = connectDB();
    }

    /**
     * دریافت همه دسته‌بندی‌ها
     * 
     * @return array|false لیست دسته‌بندی‌ها یا false در صورت خطا
     */
    public function getAllCategories()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM categories
                ORDER BY name
            ");

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت لیست دسته‌بندی‌ها: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت دسته‌بندی‌های اصلی
     * 
     * @return array|false لیست دسته‌بندی‌های اصلی یا false در صورت خطا
     */
    public function getMainCategories()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM categories
                WHERE parent_id IS NULL
                ORDER BY name
            ");

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت دسته‌بندی‌های اصلی: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت زیردسته‌ها
     * 
     * @param int $parentId آیدی دسته‌بندی والد
     * @return array|false لیست زیردسته‌ها یا false در صورت خطا
     */
    public function getSubCategories($parentId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM categories
                WHERE parent_id = :parent_id
                ORDER BY name
            ");

            $stmt->bindParam(':parent_id', $parentId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("خطا در دریافت زیردسته‌ها: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت دسته‌بندی با آیدی
     * 
     * @param int $categoryId آیدی دسته‌بندی
     * @return array|false اطلاعات دسته‌بندی یا false در صورت خطا
     */
    public function getCategoryById($categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM categories
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return false;
            }

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("خطا در دریافت اطلاعات دسته‌بندی: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت دسته‌بندی با اسلاگ
     * 
     * @param string $slug اسلاگ دسته‌بندی
     * @return array|false اطلاعات دسته‌بندی یا false در صورت خطا
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM categories
                WHERE slug = :slug
            ");

            $stmt->bindParam(':slug', $slug);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return false;
            }

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("خطا در دریافت اطلاعات دسته‌بندی با اسلاگ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت ساختار درختی دسته‌بندی‌ها
     * 
     * @return array|false ساختار درختی دسته‌بندی‌ها یا false در صورت خطا
     */
    public function getCategoriesTree()
    {
        try {
            // دریافت همه دسته‌بندی‌ها
            $categories = $this->getAllCategories();

            if ($categories === false) {
                return false;
            }

            // تبدیل به ساختار درختی
            $tree = [];
            $categoriesById = [];

            // مرتب‌سازی بر اساس آی‌دی
            foreach ($categories as $category) {
                $categoriesById[$category['id']] = [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                    'image' => $category['image'],
                    'children' => []
                ];
            }

            // ساخت درخت
            foreach ($categories as $category) {
                if ($category['parent_id'] === null) {
                    $tree[$category['id']] = &$categoriesById[$category['id']];
                } else if (isset($categoriesById[$category['parent_id']])) {
                    $categoriesById[$category['parent_id']]['children'][$category['id']] = &$categoriesById[$category['id']];
                }
            }

            return $tree;
        } catch (Exception $e) {
            error_log("خطا در دریافت ساختار درختی دسته‌بندی‌ها: " . $e->getMessage());
            return false;
        }
    }

    /**
     * دریافت مسیر دسته‌بندی
     * 
     * @param int $categoryId آیدی دسته‌بندی
     * @return array|false مسیر دسته‌بندی یا false در صورت خطا
     */
    public function getCategoryPath($categoryId)
    {
        try {
            $path = [];
            $currentId = $categoryId;

            while ($currentId) {
                $category = $this->getCategoryById($currentId);

                if ($category === false) {
                    break;
                }

                array_unshift($path, $category);
                $currentId = $category['parent_id'];
            }

            return $path;
        } catch (Exception $e) {
            error_log("خطا در دریافت مسیر دسته‌بندی: " . $e->getMessage());
            return false;
        }
    }
}
