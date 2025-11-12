-- MySQL Database Schema for LnAnhStore

-- Bảng users (khách hàng và admin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  `status` ENUM('active', 'locked') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng categories (danh mục)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng subcategories (danh mục con)
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_category` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_category`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng brands (thương hiệu)
CREATE TABLE IF NOT EXISTS `brands` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng sizes (kích cỡ)
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `size` VARCHAR(10) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng products (sản phẩm)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_category` INT DEFAULT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  `id_subcategory` INT DEFAULT NULL,
  `model` VARCHAR(255) DEFAULT NULL,
  `id_brand` INT DEFAULT NULL,
  `code` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `visibility` INT DEFAULT 0,
  `img` VARCHAR(255) DEFAULT NULL,
  `details` TEXT DEFAULT NULL,
  `stock` INT DEFAULT 0,
  `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_category`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`id_subcategory`) REFERENCES `subcategories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`id_brand`) REFERENCES `brands`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng product_sizes (size của từng sản phẩm)
CREATE TABLE IF NOT EXISTS `product_sizes` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_product` INT NOT NULL,
  `id_size` INT NOT NULL,
  `quantity` INT DEFAULT 0,
  FOREIGN KEY (`id_product`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_size`) REFERENCES `sizes`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_product_size` (`id_product`, `id_size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng cart (giỏ hàng)
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `id_product` INT NOT NULL,
  `id_size` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_product`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_size`) REFERENCES `sizes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng orders (đơn hàng)
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `order_code` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng order_details (chi tiết đơn hàng)
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_order` INT NOT NULL,
  `id_product` INT NOT NULL,
  `id_size` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`id_order`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_product`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_size`) REFERENCES `sizes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng reviews (đánh giá sản phẩm)
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_product` INT NOT NULL,
  `id_user` INT NOT NULL,
  `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_product`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu

-- Insert admin account (password: admin123)
INSERT INTO `users` (`email`, `password`, `full_name`, `phone`, `address`, `role`, `status`) VALUES
('admin@gmail.com', '$2y$10$Nb3hMe7TxXWRZxU4lvJPQ.X924FotkQl5ZmTVGWRWlQ45kbx51yci', 'Quản trị viên', '0123456789', 'Hà Nội', 'admin', 'active')
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Insert sample customers (password: customer123)
INSERT INTO `users` (`email`, `password`, `full_name`, `phone`, `address`, `role`, `status`) VALUES
('minh.khoi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Minh Khôi', '0987123456', '12 Nguyễn Trãi, Hà Nội', 'customer', 'active'),
('thu.ha@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thu Hà', '0905123123', '89 Lý Tự Trọng, TP.HCM', 'customer', 'active'),
('anh.tuan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ngô Anh Tuấn', '0912345678', '45 Pasteur, Đà Nẵng', 'customer', 'active'),
('bao.an@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vũ Bảo An', '0978123987', '27 Lê Lợi, Huế', 'customer', 'active'),
('thanh.huyen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thanh Huyền', '0933778899', '102 Điện Biên Phủ, Cần Thơ', 'customer', 'active')
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Insert categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Nam', 'Giày thể thao nam'),
(2, 'Nữ', 'Giày thể thao nữ'),
(3, 'Trẻ em', 'Giày thể thao trẻ em'),
(4, 'Trẻ sơ sinh', 'Giày thể thao cho trẻ sơ sinh')
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `description` = VALUES(`description`);

-- Insert subcategories
INSERT INTO `subcategories` (`id`, `id_category`, `name`) VALUES
(1, 1, 'Chạy bộ'),
(2, 1, 'Bóng đá'),
(3, 1, 'Bóng rổ'),
(4, 2, 'Chạy bộ'),
(5, 2, 'Lifestyle')
ON DUPLICATE KEY UPDATE
  `id_category` = VALUES(`id_category`),
  `name` = VALUES(`name`);

-- Insert brands
INSERT INTO `brands` (`id`, `name`, `description`) VALUES
(1, 'Nike', 'Thương hiệu thể thao hàng đầu thế giới'),
(2, 'Adidas', 'Thương hiệu thể thao Đức nổi tiếng'),
(3, 'Puma', 'Thương hiệu thể thao cao cấp'),
(4, 'Li-Ning', 'Thương hiệu thể thao Trung Quốc'),
(5, 'New Balance', 'Thương hiệu giày chạy bộ chuyên nghiệp')
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `description` = VALUES(`description`);

-- Insert sizes
INSERT INTO `sizes` (`id`, `size`) VALUES
(1, '38'),
(2, '39'),
(3, '40'),
(4, '41'),
(5, '42'),
(6, '43'),
(7, '44'),
(8, '45')
ON DUPLICATE KEY UPDATE
  `size` = VALUES(`size`);

INSERT INTO `products` (`id_category`, `name`, `id_subcategory`, `model`, `id_brand`, `code`, `price`, `description`, `visibility`, `img`, `details`, `stock`) VALUES
(1, 'Giày thể thao nam', 1, '001 Moment ''Navy Grey''', 4, 'LN001NG', 2499000, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/083/395/810/original/664865_00.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 2,499,000 VNĐ', 50),
(1, 'Giày thể thao nam', 1, 'Li-Ning 001 Moment ''Prussian Blue Bright Eggplant Red''', 4, 'LN001PB', 2399000, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, '', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 2,399,000 VNĐ', 40),
(1, 'Giày thể thao nam', 1, 'Li-Ning 001 Moment ''White Red''', 4, 'LN001WR', 1799000, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/824/831/original/AGCP313_1K.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 1,799,000 VNĐ', 35),
(1, 'Giày thể thao nam', 1, '001 Newborn ''Antarctic Grey Deep Blue''', 4, 'LN001AG', 1699000, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/074/397/207/original/AGCR183_2.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 1,699,000 VNĐ', 30),
(1, 'Giày thể thao nam', 1, '001 Newborn ''White Cream Yellow''', 4, 'LN001WC', 1700999, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/074/397/208/original/AGCR183_5.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 1,700,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, '005 ''Navy''', 5, 'NB005NV', 1599000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/025/622/796/original/MRL005BN.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,599,000 VNĐ', 30),
(1, 'Giày thể thao nam', 1, '009 ''Grey Navy''', 5, 'NB009GN', 1199000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/022/605/279/original/MS009MP1D.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,199,000 VNĐ', 35),
(1, 'Giày thể thao nam', 1, 'New Balance 009 Black White', 5, 'NB009BW', 1200999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/013/818/833/original/ML009UTB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,200,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 009 Navy Lime', 5, 'NB009NL', 1499000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/009/786/832/original/ML009DME.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,499,000 VNĐ', 35),
(1, 'Giày thể thao nam', 1, '009 ''Nimbus Cloud Yellow''', 5, 'NB009NC', 1099000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/040/580/832/original/MS009MC1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,099,000 VNĐ', 35),
(1, 'Giày thể thao nam', 1, 'New Balance Lifestyle 009 White', 5, 'NB009LW', 1500999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/015/239/500/original/ML009DMB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,500,999 VNĐ', 35),
(1, 'Giày thể thao nam', 1, '009v1 ''White Navy''', 5, 'NB009WN', 1100999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/034/339/070/original/MS009WB1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,100,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, '068 Extra Wide ''Navy Eclipse''', 5, 'NB068NE', 1299000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/046/038/987/original/M068CN_4E.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,299,000 VNĐ', 25),
(1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Black', 5, 'NK12CB', 2799000, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/032/117/original/344646_001.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,799,000 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Black Green Spark', 5, 'NK12BG', 2800999, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/032/118/original/344646_002.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,800,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Silver', 5, 'NK12CS', 2798999, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/100/579/287/original/21956_00.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,798,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Royal', 5, 'NK12CR', 2797999, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/098/477/735/original/21878_00.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,797,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Cranberry', 5, 'NK12CC', 2796999, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/032/122/original/344646_600.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,796,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, '1 Rosherun Nm', 5, 'NK1RNM', 1399000, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/015/996/original/631749_003.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 1,399,000 VNĐ', 40),
(1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal ''Black White''', 5, 'NB100BW', 899000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/110/426/original/SUF100A1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,000 VNĐ', 35),
(1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal ''Brown Sea Salt''', 5, 'NB100BS', 899999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/110/428/original/SUF100M1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,999 VNĐ', 35),
(1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal ''Light Grey''', 5, 'NB100LG', 898999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/465/078/original/SUF100C1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 898,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal ''Team Royal''', 5, 'NB100TR', 899899, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/106/570/454/original/SUF100TB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,899 VNĐ', 25),
(1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal ''White''', 5, 'NB100WH', 899799, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/110/427/original/SUF100K1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,799 VNĐ', 35),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 Angora Moonrock', 5, 'NB1000AM', 2199000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/101/904/878/original/1338390_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,199,000 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Arid Stone Light Silver''', 5, 'NB1000AS', 2198999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/585/854/original/M1000N.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,198,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Arid Stone Slate Grey''', 5, 'NB1000SG', 2197999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/942/772/original/U1000DH.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,197,999 VNĐ', 30),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Arid Stone Light Silver''', 5, 'NB1000BK1', 1499000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/694/191/original/GC1000NK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,499,000 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Black Royal Blue'' JD Exclusive', 5, 'NB1000BK2', 1498999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/207/825/original/GC1000DC.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,498,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Bright Lavender''', 5, 'NB1000BK3', 1497999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/942/687/original/GC1000RK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,497,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Dragon Berry''', 5, 'NB1000BK4', 1496999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/931/499/original/GC1000SG.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,496,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Grey Red'' JD Exclusive', 5, 'NB1000BK5', 1495999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/207/824/original/GC1000DB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,495,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Metallic Gold'' Shoe Palace Exclusive', 5, 'NB1000BK6', 1494999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/299/760/original/GC1000PL.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,494,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Nautical Coral'' Footlocker Exclusive', 5, 'NB1000BK7', 1493999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/882/045/original/1575235_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,493,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Parchment''', 5, 'NB1000BK8', 1492999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/855/108/original/1569709_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,492,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Pearl Grey Sea Salt''', 5, 'NB1000BK9', 1491999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/171/548/original/GC1000SB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,491,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Sea Salt Lilac'' Footlocker Exclusive', 5, 'NB1000BK10', 1490999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/058/597/original/GC1000FM.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,490,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Slate Grey Black''', 5, 'NB1000BK11', 1489999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/694/189/original/GC1000AK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,489,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Triple Black''', 5, 'NB1000BK12', 1488999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/694/190/original/GC1000BK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,488,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid ''Vintage Indigo''', 5, 'NB1000BK13', 1487999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/942/685/original/GC1000PK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,487,999 VNĐ', 25),
(3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid Wide ''Parchment''', 5, 'NB1000BK14', 1486999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/931/500/original/GC1000SP_W.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,486,999 VNĐ', 25),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Black Grey Blue''', 5, 'NB1000BG', 2099000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/921/759/original/M1000ZDO.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,099,000 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Black Grey''', 5, 'NB1000BG2', 2199000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/011/445/original/M1000A.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,199,000 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Black Grey Red''', 5, 'NB1000BGR', 2098999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/921/758/original/M1000ZAL.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,098,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Black Royal Blue'' JD Exclusive', 5, 'NB1000BRB', 2198999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/207/901/original/M1000JDC.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,198,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Black Teal'' JD Exclusive', 5, 'NB1000BT', 2197999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/290/855/original/1593177_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,197,999 VNĐ', 30),
(1, 'Giày thể thao nam', 1, 'New Balance 1000 ''Lunar New Year''', 5, 'NB1000LNY', 2196999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/107/309/319/original/1532357_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,196,999 VNĐ', 30),
(4, 'Giày trẻ sơ sinh', 1, 'New Balance 1000 Bungee Lace Toddler ''Arid Stone''', 5, 'NB1000T1', 1199000, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/036/316/original/IV1000NK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ sơ sinh<br>Giá: 1,199,000 VNĐ', 20),
(4, 'Giày trẻ sơ sinh', 1, 'New Balance 1000 Bungee Lace Toddler ''Slate Grey Black''', 5, 'NB1000T2', 1198999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/036/314/original/IV1000AK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ sơ sinh<br>Giá: 1,198,999 VNĐ', 20),
(4, 'Giày trẻ sơ sinh', 1, 'New Balance 1000 Bungee Lace Toddler ''Triple Black''', 5, 'NB1000T3', 1197999, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/036/315/original/IV1000BK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ sơ sinh<br>Giá: 1,197,999 VNĐ', 20);

-- Insert product sizes (tất cả sản phẩm có size 38-45)
INSERT INTO `product_sizes` (`id_product`, `id_size`, `quantity`)
SELECT p.`id`, s.`id`, 10
FROM `products` p
CROSS JOIN `sizes` s;

-- Insert sample orders covering all statuses
INSERT INTO `orders` (
    `id_user`, `order_code`, `full_name`, `phone`, `address`,
    `payment_method`, `total_amount`, `status`, `created_at`, `updated_at`
) VALUES
(
    (SELECT `id` FROM `users` WHERE `email` = 'minh.khoi@gmail.com'),
    'ORD20250101',
    'Trần Minh Khôi',
    '0987123456',
    '12 Nguyễn Trãi, Hà Nội',
    'COD',
    3998000.00,
    'pending',
    NOW() - INTERVAL 1 DAY,
    NOW() - INTERVAL 1 DAY
),
(
    (SELECT `id` FROM `users` WHERE `email` = 'thu.ha@gmail.com'),
    'ORD20250102',
    'Trần Thu Hà',
    '0905123123',
    '89 Lý Tự Trọng, TP.HCM',
    'VNPay',
    2998000.00,
    'confirmed',
    NOW() - INTERVAL 3 DAY,
    NOW() - INTERVAL 2 DAY
),
(
    (SELECT `id` FROM `users` WHERE `email` = 'anh.tuan@gmail.com'),
    'ORD20250103',
    'Ngô Anh Tuấn',
    '0912345678',
    '45 Pasteur, Đà Nẵng',
    'MoMo',
    4299999.00,
    'shipping',
    NOW() - INTERVAL 5 DAY,
    NOW() - INTERVAL 1 DAY
),
(
    (SELECT `id` FROM `users` WHERE `email` = 'bao.an@gmail.com'),
    'ORD20250104',
    'Vũ Bảo An',
    '0978123987',
    '27 Lê Lợi, Huế',
    'COD',
    3600998.00,
    'completed',
    NOW() - INTERVAL 12 DAY,
    NOW() - INTERVAL 1 DAY
),
(
    (SELECT `id` FROM `users` WHERE `email` = 'thanh.huyen@gmail.com'),
    'ORD20250105',
    'Phạm Thanh Huyền',
    '0933778899',
    '102 Điện Biên Phủ, Cần Thơ',
    'Bank Transfer',
    2997000.00,
    'cancelled',
    NOW() - INTERVAL 8 DAY,
    NOW() - INTERVAL 6 DAY
)
ON DUPLICATE KEY UPDATE `order_code` = `order_code`;

-- Insert sample order details
INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'New Balance 009 Grey Navy', 1199000.00, 1, 1199000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NB009GN'
JOIN `sizes` s ON s.`size` = '42'
WHERE o.`order_code` = 'ORD20250101'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'Nike Air 1/2 Cent Black', 2799000.00, 1, 2799000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NK12CB'
JOIN `sizes` s ON s.`size` = '43'
WHERE o.`order_code` = 'ORD20250101'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'New Balance 009 Navy Lime', 1499000.00, 2, 2998000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NB009NL'
JOIN `sizes` s ON s.`size` = '38'
WHERE o.`order_code` = 'ORD20250102'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'New Balance 009 Lifestyle White', 1500999.00, 1, 1500999.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NB009LW'
JOIN `sizes` s ON s.`size` = '41'
WHERE o.`order_code` = 'ORD20250103'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'Nike Air 1/2 Cent Black', 2799000.00, 1, 2799000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NK12CB'
JOIN `sizes` s ON s.`size` = '42'
WHERE o.`order_code` = 'ORD20250103'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'Nike Rosherun NM', 1399000.00, 1, 1399000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NK1RNM'
JOIN `sizes` s ON s.`size` = '44'
WHERE o.`order_code` = 'ORD20250104'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'New Balance 009v1 White Navy', 1100999.00, 2, 2201998.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NB009WN'
JOIN `sizes` s ON s.`size` = '43'
WHERE o.`order_code` = 'ORD20250104'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'New Balance 009 Grey Navy', 1199000.00, 1, 1199000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NB009GN'
JOIN `sizes` s ON s.`size` = '40'
WHERE o.`order_code` = 'ORD20250105'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );

INSERT INTO `order_details` (`id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`)
SELECT o.`id`, p.`id`, s.`id`, 'New Balance 100 Sandal Black White', 899000.00, 2, 1798000.00
FROM `orders` o
JOIN `products` p ON p.`code` = 'NB100BW'
JOIN `sizes` s ON s.`size` = '39'
WHERE o.`order_code` = 'ORD20250105'
  AND NOT EXISTS (
    SELECT 1 FROM `order_details` od
    WHERE od.`id_order` = o.`id` AND od.`id_product` = p.`id` AND od.`id_size` = s.`id`
  );
