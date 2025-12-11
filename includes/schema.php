<?php
/**
 * Tệp schema đơn giản để bổ sung các bảng/cột mới nếu chưa có.
 * Chỉ chạy cho MySQL/MariaDB (XAMPP); các môi trường khác cần tự migrate thủ công.
 */

if (!isset($db_conn)) {
    return;
}

$driver = $db_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
if ($driver !== 'mysql') {
    // Tránh lỗi trên PostgreSQL; cung cấp DDL trong database.sql để tự migrate.
    return;
}

/**
 * Kiểm tra xem cột có tồn tại hay chưa.
 */
function column_exists(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
    );
    $stmt->execute([$table, $column]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Kiểm tra xem bảng có tồn tại hay chưa.
 */
function table_exists(PDO $db, string $table): bool
{
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?"
    );
    $stmt->execute([$table]);
    return (bool) $stmt->fetchColumn();
}

try {
    // Wishlist
    if (!table_exists($db_conn, 'wishlists')) {
        $db_conn->exec("
            CREATE TABLE wishlists (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                id_product INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_user_product (id_user, id_product),
                KEY idx_wishlist_user (id_user),
                KEY idx_wishlist_product (id_product)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    // Coupons / promotions
    if (!table_exists($db_conn, 'coupons')) {
        $db_conn->exec("
            CREATE TABLE coupons (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) NOT NULL UNIQUE,
                description TEXT NULL,
                scope_type ENUM('product','category','order') NOT NULL DEFAULT 'order',
                scope_id INT NULL,
                discount_type ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
                discount_value DECIMAL(10,2) NOT NULL DEFAULT 0,
                usage_limit INT NULL,
                used_count INT NOT NULL DEFAULT 0,
                start_at DATETIME NULL,
                end_at DATETIME NULL,
                min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                status ENUM('active','inactive') NOT NULL DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    // Bổ sung cột vào orders
    if (!column_exists($db_conn, 'orders', 'coupon_code')) {
        $db_conn->exec("ALTER TABLE orders ADD COLUMN coupon_code VARCHAR(50) NULL AFTER payment_method;");
    }
    if (!column_exists($db_conn, 'orders', 'discount_amount')) {
        $db_conn->exec("ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER total_amount;");
    }
} catch (Throwable $e) {
    // Không phá vỡ luồng ứng dụng nếu DDL thất bại; log tạm bằng error_log.
    error_log('Schema migration skipped: ' . $e->getMessage());
}

