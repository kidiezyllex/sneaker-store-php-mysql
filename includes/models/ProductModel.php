<?php

class ProductModel extends Model
{
    public function getFeatured(int $limit = 8): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, b.name AS brand_name, c.name AS category_name
            FROM products p
            LEFT JOIN brands b ON p.id_brand = b.id
            LEFT JOIN categories c ON p.id_category = c.id
            WHERE p.visibility = 1
            ORDER BY p.date_add DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBestSellers(int $limit = 4): array
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.img, p.price, COALESCE(SUM(od.quantity), 0) AS total_sold
            FROM products p
            LEFT JOIN order_details od ON od.id_product = p.id
            WHERE p.visibility = 1
            GROUP BY p.id, p.name, p.img, p.price
            ORDER BY total_sold DESC, p.date_add DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopCategories(int $limit = 4): array
    {
        $stmt = $this->db->prepare("
            SELECT c.id, c.name, c.description, COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.id_category = c.id AND p.visibility = 1
            GROUP BY c.id, c.name, c.description
            ORDER BY product_count DESC, c.name ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopBrands(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT b.id, b.name, b.description, COUNT(p.id) AS product_count
            FROM brands b
            LEFT JOIN products p ON p.id_brand = b.id AND p.visibility = 1
            GROUP BY b.id, b.name, b.description
            ORDER BY product_count DESC, b.name ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search(array $filters, int $perPage, int $page): array
    {
        $where = ["p.visibility = 1"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE :q OR p.description LIKE :q OR p.model LIKE :q)";
            $params[':q'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['category'])) {
            $where[] = "p.id_category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['brand'])) {
            $where[] = "p.id_brand = :brand";
            $params[':brand'] = $filters['brand'];
        }
        if (!empty($filters['min_price'])) {
            $where[] = "p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = "p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM products p WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "
            SELECT p.*, b.name as brand_name, c.name as category_name
            FROM products p
            LEFT JOIN brands b ON p.id_brand = b.id
            LEFT JOIN categories c ON p.id_category = c.id
            WHERE $whereClause
            ORDER BY p.date_add DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, b.name as brand_name, c.name as category_name, sc.name as subcategory_name
            FROM products p
            LEFT JOIN brands b ON p.id_brand = b.id
            LEFT JOIN categories c ON p.id_category = c.id
            LEFT JOIN subcategories sc ON p.id_subcategory = sc.id
            WHERE p.id = :id AND p.visibility = 1
        ");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function getSizes(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, ps.quantity
            FROM product_sizes ps
            JOIN sizes s ON ps.id_size = s.id
            WHERE ps.id_product = :id AND ps.quantity > 0
            ORDER BY s.size
        ");
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll();
    }
}

