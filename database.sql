-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Sneakerdata: https://thesneakerdatabase.com/sneakers
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 08, 2025 lúc 05:02 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Cơ sở dữ liệu: `sneakerdb2`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Nike', 'Thương hiệu thể thao hàng đầu thế giới', '2025-11-12 16:03:04'),
(2, 'Adidas', 'Thương hiệu thể thao Đức nổi tiếng', '2025-11-12 16:03:04'),
(3, 'Puma', 'Thương hiệu thể thao cao cấp', '2025-11-12 16:03:04'),
(4, 'Li-Ning', 'Thương hiệu thể thao Trung Quốc', '2025-11-12 16:03:04'),
(5, 'New Balance', 'Thương hiệu giày chạy bộ chuyên nghiệp', '2025-11-12 16:03:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_size` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Nam', 'Giày thể thao nam', '2025-11-12 16:03:04'),
(2, 'Nữ', 'Giày thể thao nữ', '2025-11-12 16:03:04'),
(3, 'Trẻ em', 'Giày thể thao trẻ em', '2025-11-12 16:03:04'),
(4, 'Trẻ sơ sinh', 'Giày thể thao cho trẻ sơ sinh', '2025-11-12 16:03:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `id_user`, `order_code`, `full_name`, `phone`, `address`, `payment_method`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'ORD20250101', 'Trần Minh Khôi', '0987123456', '12 Nguyễn Trãi, Hà Nội', 'COD', 3998000.00, 'pending', '2025-11-11 16:03:04', '2025-11-11 16:03:04'),
(2, 3, 'ORD20250102', 'Trần Thu Hà', '0905123123', '89 Lý Tự Trọng, TP.HCM', 'VNPay', 2998000.00, 'confirmed', '2025-11-09 16:03:04', '2025-11-10 16:03:04'),
(3, 4, 'ORD20250103', 'Ngô Anh Tuấn', '0912345678', '45 Pasteur, Đà Nẵng', 'MoMo', 4299999.00, 'shipping', '2025-11-07 16:03:04', '2025-11-11 16:03:04'),
(4, 5, 'ORD20250104', 'Vũ Bảo An', '0978123987', '27 Lê Lợi, Huế', 'COD', 3600998.00, 'completed', '2025-10-31 16:03:04', '2025-11-11 16:03:04'),
(5, 6, 'ORD20250105', 'Phạm Thanh Huyền', '0933778899', '102 Điện Biên Phủ, Cần Thơ', 'Bank Transfer', 2997000.00, 'cancelled', '2025-11-04 16:03:04', '2025-11-06 16:03:04'),
(6, 7, 'ORD202511122429', 'An Bui', '0446735384', 'dsdsdfd', 'cod', 2499000.00, 'pending', '2025-11-12 16:11:01', '2025-11-12 16:11:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_size` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `id_order`, `id_product`, `id_size`, `product_name`, `product_price`, `quantity`, `subtotal`) VALUES
(1, 1, 7, 5, 'New Balance 009 Grey Navy', 1199000.00, 1, 1199000.00),
(2, 1, 14, 6, 'Nike Air 1/2 Cent Black', 2799000.00, 1, 2799000.00),
(3, 2, 9, 1, 'New Balance 009 Navy Lime', 1499000.00, 2, 2998000.00),
(4, 3, 11, 4, 'New Balance 009 Lifestyle White', 1500999.00, 1, 1500999.00),
(5, 3, 14, 5, 'Nike Air 1/2 Cent Black', 2799000.00, 1, 2799000.00),
(6, 4, 19, 7, 'Nike Rosherun NM', 1399000.00, 1, 1399000.00),
(7, 4, 12, 6, 'New Balance 009v1 White Navy', 1100999.00, 2, 2201998.00),
(8, 5, 7, 3, 'New Balance 009 Grey Navy', 1199000.00, 1, 1199000.00),
(9, 5, 20, 2, 'New Balance 100 Sandal Black White', 899000.00, 2, 1798000.00),
(10, 6, 1, 3, 'Giày thể thao nam', 2499000.00, 1, 2499000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `id_category` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `id_subcategory` int(11) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `id_brand` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `visibility` int(11) DEFAULT 0,
  `img` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `date_add` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `id_category`, `name`, `id_subcategory`, `model`, `id_brand`, `code`, `price`, `description`, `visibility`, `img`, `details`, `stock`, `date_add`) VALUES
(1, 1, 'Giày thể thao nam', 1, '001 Moment \'Navy Grey\'', 4, 'LN001NG', 2499000.00, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/083/395/810/original/664865_00.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 2,499,000 VNĐ', 50, '2025-11-12 16:03:04'),
(3, 1, 'Giày thể thao nam', 1, 'Li-Ning 001 Moment \'White Red\'', 4, 'LN001WR', 1799000.00, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/824/831/original/AGCP313_1K.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 1,799,000 VNĐ', 35, '2025-11-12 16:03:04'),
(4, 1, 'Giày thể thao nam', 1, '001 Newborn \'Antarctic Grey Deep Blue\'', 4, 'LN001AG', 1699000.00, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/074/397/207/original/AGCR183_2.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 1,699,000 VNĐ', 30, '2025-11-12 16:03:04'),
(5, 1, 'Giày thể thao nam', 1, '001 Newborn \'White Cream Yellow\'', 4, 'LN001WC', 1700999.00, 'Sản phẩm chất lượng cao từ thương hiệu Li-Ning', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/074/397/208/original/AGCR183_5.png.png', 'Thương hiệu: Li-Ning<br>Danh mục: Nam<br>Giá: 1,700,999 VNĐ', 30, '2025-11-12 16:03:04'),
(6, 1, 'Giày thể thao nam', 1, '005 \'Navy\'', 5, 'NB005NV', 1599000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/025/622/796/original/MRL005BN.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,599,000 VNĐ', 30, '2025-11-12 16:03:04'),
(7, 1, 'Giày thể thao nam', 1, '009 \'Grey Navy\'', 5, 'NB009GN', 1199000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/022/605/279/original/MS009MP1D.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,199,000 VNĐ', 35, '2025-11-12 16:03:04'),
(8, 1, 'Giày thể thao nam', 1, 'New Balance 009 Black White', 5, 'NB009BW', 1200999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/013/818/833/original/ML009UTB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,200,999 VNĐ', 30, '2025-11-12 16:03:04'),
(9, 1, 'Giày thể thao nam', 1, 'New Balance 009 Navy Lime', 5, 'NB009NL', 1499000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/009/786/832/original/ML009DME.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,499,000 VNĐ', 35, '2025-11-12 16:03:04'),
(10, 1, 'Giày thể thao nam', 1, '009 \'Nimbus Cloud Yellow\'', 5, 'NB009NC', 1099000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/040/580/832/original/MS009MC1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,099,000 VNĐ', 35, '2025-11-12 16:03:04'),
(11, 1, 'Giày thể thao nam', 1, 'New Balance Lifestyle 009 White', 5, 'NB009LW', 1500999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/015/239/500/original/ML009DMB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,500,999 VNĐ', 35, '2025-11-12 16:03:04'),
(12, 1, 'Giày thể thao nam', 1, '009v1 \'White Navy\'', 5, 'NB009WN', 1100999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/034/339/070/original/MS009WB1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,100,999 VNĐ', 30, '2025-11-12 16:03:04'),
(13, 1, 'Giày thể thao nam', 1, '068 Extra Wide \'Navy Eclipse\'', 5, 'NB068NE', 1299000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/046/038/987/original/M068CN_4E.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 1,299,000 VNĐ', 25, '2025-11-12 16:03:04'),
(14, 1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Black', 5, 'NK12CB', 2799000.00, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/032/117/original/344646_001.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,799,000 VNĐ', 30, '2025-11-12 16:03:04'),
(15, 1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Black Green Spark', 5, 'NK12BG', 2800999.00, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/032/118/original/344646_002.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,800,999 VNĐ', 30, '2025-11-12 16:03:04'),
(16, 1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Silver', 5, 'NK12CS', 2798999.00, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/100/579/287/original/21956_00.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,798,999 VNĐ', 30, '2025-11-12 16:03:04'),
(17, 1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Royal', 5, 'NK12CR', 2797999.00, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/098/477/735/original/21878_00.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,797,999 VNĐ', 30, '2025-11-12 16:03:04'),
(18, 1, 'Giày thể thao nam', 1, 'Nike Air 1/2 Cent Cranberry', 5, 'NK12CC', 2796999.00, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/032/122/original/344646_600.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 2,796,999 VNĐ', 30, '2025-11-12 16:03:04'),
(19, 1, 'Giày thể thao nam', 1, '1 Rosherun Nm', 5, 'NK1RNM', 1399000.00, 'Sản phẩm chất lượng cao từ thương hiệu Nike', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/000/015/996/original/631749_003.png.png', 'Thương hiệu: Nike<br>Danh mục: Nam<br>Giá: 1,399,000 VNĐ', 40, '2025-11-12 16:03:04'),
(20, 1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal \'Black White\'', 5, 'NB100BW', 899000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/110/426/original/SUF100A1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,000 VNĐ', 35, '2025-11-12 16:03:04'),
(21, 1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal \'Brown Sea Salt\'', 5, 'NB100BS', 899999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/110/428/original/SUF100M1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,999 VNĐ', 35, '2025-11-12 16:03:04'),
(22, 1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal \'Light Grey\'', 5, 'NB100LG', 898999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/465/078/original/SUF100C1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 898,999 VNĐ', 30, '2025-11-12 16:03:04'),
(23, 1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal \'Team Royal\'', 5, 'NB100TR', 899899.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/106/570/454/original/SUF100TB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,899 VNĐ', 25, '2025-11-12 16:03:04'),
(24, 1, 'Giày thể thao nam', 1, 'New Balance 100 Sandal \'White\'', 5, 'NB100WH', 899799.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/109/110/427/original/SUF100K1.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 899,799 VNĐ', 35, '2025-11-12 16:03:04'),
(25, 1, 'Giày thể thao nam', 1, 'New Balance 1000 Angora Moonrock', 5, 'NB1000AM', 2199000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/101/904/878/original/1338390_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,199,000 VNĐ', 30, '2025-11-12 16:03:04'),
(26, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Arid Stone Light Silver\'', 5, 'NB1000AS', 2198999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/585/854/original/M1000N.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,198,999 VNĐ', 30, '2025-11-12 16:03:04'),
(27, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Arid Stone Slate Grey\'', 5, 'NB1000SG', 2197999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/942/772/original/U1000DH.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,197,999 VNĐ', 30, '2025-11-12 16:03:04'),
(28, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Arid Stone Light Silver\'', 5, 'NB1000BK1', 1499000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/694/191/original/GC1000NK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,499,000 VNĐ', 25, '2025-11-12 16:03:04'),
(29, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Black Royal Blue\' JD Exclusive', 5, 'NB1000BK2', 1498999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/207/825/original/GC1000DC.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,498,999 VNĐ', 25, '2025-11-12 16:03:04'),
(30, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Bright Lavender\'', 5, 'NB1000BK3', 1497999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/942/687/original/GC1000RK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,497,999 VNĐ', 25, '2025-11-12 16:03:04'),
(31, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Dragon Berry\'', 5, 'NB1000BK4', 1496999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/931/499/original/GC1000SG.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,496,999 VNĐ', 25, '2025-11-12 16:03:04'),
(32, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Grey Red\' JD Exclusive', 5, 'NB1000BK5', 1495999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/207/824/original/GC1000DB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,495,999 VNĐ', 25, '2025-11-12 16:03:04'),
(33, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Metallic Gold\' Shoe Palace Exclusive', 5, 'NB1000BK6', 1494999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/299/760/original/GC1000PL.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,494,999 VNĐ', 25, '2025-11-12 16:03:04'),
(34, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Nautical Coral\' Footlocker Exclusive', 5, 'NB1000BK7', 1493999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/882/045/original/1575235_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,493,999 VNĐ', 25, '2025-11-12 16:03:04'),
(35, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Parchment\'', 5, 'NB1000BK8', 1492999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/855/108/original/1569709_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,492,999 VNĐ', 25, '2025-11-12 16:03:04'),
(36, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Pearl Grey Sea Salt\'', 5, 'NB1000BK9', 1491999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/171/548/original/GC1000SB.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,491,999 VNĐ', 25, '2025-11-12 16:03:04'),
(37, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Sea Salt Lilac\' Footlocker Exclusive', 5, 'NB1000BK10', 1490999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/058/597/original/GC1000FM.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,490,999 VNĐ', 25, '2025-11-12 16:03:04'),
(38, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Slate Grey Black\'', 5, 'NB1000BK11', 1489999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/694/189/original/GC1000AK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,489,999 VNĐ', 25, '2025-11-12 16:03:04'),
(39, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Triple Black\'', 5, 'NB1000BK12', 1488999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/694/190/original/GC1000BK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,488,999 VNĐ', 25, '2025-11-12 16:03:04'),
(40, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid \'Vintage Indigo\'', 5, 'NB1000BK13', 1487999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/942/685/original/GC1000PK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,487,999 VNĐ', 25, '2025-11-12 16:03:04'),
(41, 3, 'Giày thể thao trẻ em', 1, 'New Balance 1000 Big Kid Wide \'Parchment\'', 5, 'NB1000BK14', 1486999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/108/931/500/original/GC1000SP_W.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ em<br>Giá: 1,486,999 VNĐ', 25, '2025-11-12 16:03:04'),
(42, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Black Grey Blue\'', 5, 'NB1000BG', 2099000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/921/759/original/M1000ZDO.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,099,000 VNĐ', 30, '2025-11-12 16:03:04'),
(43, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Black Grey\'', 5, 'NB1000BG2', 2199000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/011/445/original/M1000A.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,199,000 VNĐ', 30, '2025-11-12 16:03:04'),
(44, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Black Grey Red\'', 5, 'NB1000BGR', 2098999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/921/758/original/M1000ZAL.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,098,999 VNĐ', 30, '2025-11-12 16:03:04'),
(45, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Black Royal Blue\' JD Exclusive', 5, 'NB1000BRB', 2198999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/111/207/901/original/M1000JDC.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,198,999 VNĐ', 30, '2025-11-12 16:03:04'),
(46, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Black Teal\' JD Exclusive', 5, 'NB1000BT', 2197999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/110/290/855/original/1593177_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,197,999 VNĐ', 30, '2025-11-12 16:03:04'),
(47, 1, 'Giày thể thao nam', 1, 'New Balance 1000 \'Lunar New Year\'', 5, 'NB1000LNY', 2196999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/107/309/319/original/1532357_00.png.png', 'Thương hiệu: New Balance<br>Danh mục: Nam<br>Giá: 2,196,999 VNĐ', 30, '2025-11-12 16:03:04'),
(48, 4, 'Giày trẻ sơ sinh', 1, 'New Balance 1000 Bungee Lace Toddler \'Arid Stone\'', 5, 'NB1000T1', 1199000.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/036/316/original/IV1000NK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ sơ sinh<br>Giá: 1,199,000 VNĐ', 20, '2025-11-12 16:03:04'),
(49, 4, 'Giày trẻ sơ sinh', 1, 'New Balance 1000 Bungee Lace Toddler \'Slate Grey Black\'', 5, 'NB1000T2', 1198999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/036/314/original/IV1000AK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ sơ sinh<br>Giá: 1,198,999 VNĐ', 20, '2025-11-12 16:03:04'),
(50, 4, 'Giày trẻ sơ sinh', 1, 'New Balance 1000 Bungee Lace Toddler \'Triple Black\'', 5, 'NB1000T3', 1197999.00, 'Sản phẩm chất lượng cao từ thương hiệu New Balance', 1, 'https://image.goat.com/750/attachments/product_template_pictures/images/112/036/315/original/IV1000BK.png.png', 'Thương hiệu: New Balance<br>Danh mục: Trẻ sơ sinh<br>Giá: 1,197,999 VNĐ', 20, '2025-11-12 16:03:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_size` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `id_product`, `id_size`, `quantity`) VALUES
(1, 1, 1, 10),
(2, 1, 2, 10),
(3, 1, 3, 10),
(4, 1, 4, 10),
(5, 1, 5, 10),
(6, 1, 6, 10),
(7, 1, 7, 10),
(8, 1, 8, 10),
(17, 3, 1, 10),
(18, 3, 2, 10),
(19, 3, 3, 10),
(20, 3, 4, 10),
(21, 3, 5, 10),
(22, 3, 6, 10),
(23, 3, 7, 10),
(24, 3, 8, 10),
(25, 4, 1, 10),
(26, 4, 2, 10),
(27, 4, 3, 10),
(28, 4, 4, 10),
(29, 4, 5, 10),
(30, 4, 6, 10),
(31, 4, 7, 10),
(32, 4, 8, 10),
(33, 5, 1, 10),
(34, 5, 2, 10),
(35, 5, 3, 10),
(36, 5, 4, 10),
(37, 5, 5, 10),
(38, 5, 6, 10),
(39, 5, 7, 10),
(40, 5, 8, 10),
(41, 6, 1, 10),
(42, 6, 2, 10),
(43, 6, 3, 10),
(44, 6, 4, 10),
(45, 6, 5, 10),
(46, 6, 6, 10),
(47, 6, 7, 10),
(48, 6, 8, 10),
(49, 7, 1, 10),
(50, 7, 2, 10),
(51, 7, 3, 10),
(52, 7, 4, 10),
(53, 7, 5, 10),
(54, 7, 6, 10),
(55, 7, 7, 10),
(56, 7, 8, 10),
(57, 8, 1, 10),
(58, 8, 2, 10),
(59, 8, 3, 10),
(60, 8, 4, 10),
(61, 8, 5, 10),
(62, 8, 6, 10),
(63, 8, 7, 10),
(64, 8, 8, 10),
(65, 9, 1, 10),
(66, 9, 2, 10),
(67, 9, 3, 10),
(68, 9, 4, 10),
(69, 9, 5, 10),
(70, 9, 6, 10),
(71, 9, 7, 10),
(72, 9, 8, 10),
(73, 10, 1, 10),
(74, 10, 2, 10),
(75, 10, 3, 10),
(76, 10, 4, 10),
(77, 10, 5, 10),
(78, 10, 6, 10),
(79, 10, 7, 10),
(80, 10, 8, 10),
(81, 11, 1, 10),
(82, 11, 2, 10),
(83, 11, 3, 10),
(84, 11, 4, 10),
(85, 11, 5, 10),
(86, 11, 6, 10),
(87, 11, 7, 10),
(88, 11, 8, 10),
(89, 12, 1, 10),
(90, 12, 2, 10),
(91, 12, 3, 10),
(92, 12, 4, 10),
(93, 12, 5, 10),
(94, 12, 6, 10),
(95, 12, 7, 10),
(96, 12, 8, 10),
(97, 13, 1, 10),
(98, 13, 2, 10),
(99, 13, 3, 10),
(100, 13, 4, 10),
(101, 13, 5, 10),
(102, 13, 6, 10),
(103, 13, 7, 10),
(104, 13, 8, 10),
(105, 14, 1, 10),
(106, 14, 2, 10),
(107, 14, 3, 10),
(108, 14, 4, 10),
(109, 14, 5, 10),
(110, 14, 6, 10),
(111, 14, 7, 10),
(112, 14, 8, 10),
(113, 15, 1, 10),
(114, 15, 2, 10),
(115, 15, 3, 10),
(116, 15, 4, 10),
(117, 15, 5, 10),
(118, 15, 6, 10),
(119, 15, 7, 10),
(120, 15, 8, 10),
(121, 16, 1, 10),
(122, 16, 2, 10),
(123, 16, 3, 10),
(124, 16, 4, 10),
(125, 16, 5, 10),
(126, 16, 6, 10),
(127, 16, 7, 10),
(128, 16, 8, 10),
(129, 17, 1, 10),
(130, 17, 2, 10),
(131, 17, 3, 10),
(132, 17, 4, 10),
(133, 17, 5, 10),
(134, 17, 6, 10),
(135, 17, 7, 10),
(136, 17, 8, 10),
(137, 18, 1, 10),
(138, 18, 2, 10),
(139, 18, 3, 10),
(140, 18, 4, 10),
(141, 18, 5, 10),
(142, 18, 6, 10),
(143, 18, 7, 10),
(144, 18, 8, 10),
(145, 19, 1, 10),
(146, 19, 2, 10),
(147, 19, 3, 10),
(148, 19, 4, 10),
(149, 19, 5, 10),
(150, 19, 6, 10),
(151, 19, 7, 10),
(152, 19, 8, 10),
(153, 20, 1, 10),
(154, 20, 2, 10),
(155, 20, 3, 10),
(156, 20, 4, 10),
(157, 20, 5, 10),
(158, 20, 6, 10),
(159, 20, 7, 10),
(160, 20, 8, 10),
(161, 21, 1, 10),
(162, 21, 2, 10),
(163, 21, 3, 10),
(164, 21, 4, 10),
(165, 21, 5, 10),
(166, 21, 6, 10),
(167, 21, 7, 10),
(168, 21, 8, 10),
(169, 22, 1, 10),
(170, 22, 2, 10),
(171, 22, 3, 10),
(172, 22, 4, 10),
(173, 22, 5, 10),
(174, 22, 6, 10),
(175, 22, 7, 10),
(176, 22, 8, 10),
(177, 23, 1, 10),
(178, 23, 2, 10),
(179, 23, 3, 10),
(180, 23, 4, 10),
(181, 23, 5, 10),
(182, 23, 6, 10),
(183, 23, 7, 10),
(184, 23, 8, 10),
(185, 24, 1, 10),
(186, 24, 2, 10),
(187, 24, 3, 10),
(188, 24, 4, 10),
(189, 24, 5, 10),
(190, 24, 6, 10),
(191, 24, 7, 10),
(192, 24, 8, 10),
(193, 25, 1, 10),
(194, 25, 2, 10),
(195, 25, 3, 10),
(196, 25, 4, 10),
(197, 25, 5, 10),
(198, 25, 6, 10),
(199, 25, 7, 10),
(200, 25, 8, 10),
(201, 26, 1, 10),
(202, 26, 2, 10),
(203, 26, 3, 10),
(204, 26, 4, 10),
(205, 26, 5, 10),
(206, 26, 6, 10),
(207, 26, 7, 10),
(208, 26, 8, 10),
(209, 27, 1, 10),
(210, 27, 2, 10),
(211, 27, 3, 10),
(212, 27, 4, 10),
(213, 27, 5, 10),
(214, 27, 6, 10),
(215, 27, 7, 10),
(216, 27, 8, 10),
(217, 42, 1, 10),
(218, 42, 2, 10),
(219, 42, 3, 10),
(220, 42, 4, 10),
(221, 42, 5, 10),
(222, 42, 6, 10),
(223, 42, 7, 10),
(224, 42, 8, 10),
(225, 43, 1, 10),
(226, 43, 2, 10),
(227, 43, 3, 10),
(228, 43, 4, 10),
(229, 43, 5, 10),
(230, 43, 6, 10),
(231, 43, 7, 10),
(232, 43, 8, 10),
(233, 44, 1, 10),
(234, 44, 2, 10),
(235, 44, 3, 10),
(236, 44, 4, 10),
(237, 44, 5, 10),
(238, 44, 6, 10),
(239, 44, 7, 10),
(240, 44, 8, 10),
(241, 45, 1, 10),
(242, 45, 2, 10),
(243, 45, 3, 10),
(244, 45, 4, 10),
(245, 45, 5, 10),
(246, 45, 6, 10),
(247, 45, 7, 10),
(248, 45, 8, 10),
(249, 46, 1, 10),
(250, 46, 2, 10),
(251, 46, 3, 10),
(252, 46, 4, 10),
(253, 46, 5, 10),
(254, 46, 6, 10),
(255, 46, 7, 10),
(256, 46, 8, 10),
(257, 47, 1, 10),
(258, 47, 2, 10),
(259, 47, 3, 10),
(260, 47, 4, 10),
(261, 47, 5, 10),
(262, 47, 6, 10),
(263, 47, 7, 10),
(264, 47, 8, 10),
(265, 28, 1, 10),
(266, 28, 2, 10),
(267, 28, 3, 10),
(268, 28, 4, 10),
(269, 28, 5, 10),
(270, 28, 6, 10),
(271, 28, 7, 10),
(272, 28, 8, 10),
(273, 29, 1, 10),
(274, 29, 2, 10),
(275, 29, 3, 10),
(276, 29, 4, 10),
(277, 29, 5, 10),
(278, 29, 6, 10),
(279, 29, 7, 10),
(280, 29, 8, 10),
(281, 30, 1, 10),
(282, 30, 2, 10),
(283, 30, 3, 10),
(284, 30, 4, 10),
(285, 30, 5, 10),
(286, 30, 6, 10),
(287, 30, 7, 10),
(288, 30, 8, 10),
(289, 31, 1, 10),
(290, 31, 2, 10),
(291, 31, 3, 10),
(292, 31, 4, 10),
(293, 31, 5, 10),
(294, 31, 6, 10),
(295, 31, 7, 10),
(296, 31, 8, 10),
(297, 32, 1, 10),
(298, 32, 2, 10),
(299, 32, 3, 10),
(300, 32, 4, 10),
(301, 32, 5, 10),
(302, 32, 6, 10),
(303, 32, 7, 10),
(304, 32, 8, 10),
(305, 33, 1, 10),
(306, 33, 2, 10),
(307, 33, 3, 10),
(308, 33, 4, 10),
(309, 33, 5, 10),
(310, 33, 6, 10),
(311, 33, 7, 10),
(312, 33, 8, 10),
(313, 34, 1, 10),
(314, 34, 2, 10),
(315, 34, 3, 10),
(316, 34, 4, 10),
(317, 34, 5, 10),
(318, 34, 6, 10),
(319, 34, 7, 10),
(320, 34, 8, 10),
(321, 35, 1, 10),
(322, 35, 2, 10),
(323, 35, 3, 10),
(324, 35, 4, 10),
(325, 35, 5, 10),
(326, 35, 6, 10),
(327, 35, 7, 10),
(328, 35, 8, 10),
(329, 36, 1, 10),
(330, 36, 2, 10),
(331, 36, 3, 10),
(332, 36, 4, 10),
(333, 36, 5, 10),
(334, 36, 6, 10),
(335, 36, 7, 10),
(336, 36, 8, 10),
(337, 37, 1, 10),
(338, 37, 2, 10),
(339, 37, 3, 10),
(340, 37, 4, 10),
(341, 37, 5, 10),
(342, 37, 6, 10),
(343, 37, 7, 10),
(344, 37, 8, 10),
(345, 38, 1, 10),
(346, 38, 2, 10),
(347, 38, 3, 10),
(348, 38, 4, 10),
(349, 38, 5, 10),
(350, 38, 6, 10),
(351, 38, 7, 10),
(352, 38, 8, 10),
(353, 39, 1, 10),
(354, 39, 2, 10),
(355, 39, 3, 10),
(356, 39, 4, 10),
(357, 39, 5, 10),
(358, 39, 6, 10),
(359, 39, 7, 10),
(360, 39, 8, 10),
(361, 40, 1, 10),
(362, 40, 2, 10),
(363, 40, 3, 10),
(364, 40, 4, 10),
(365, 40, 5, 10),
(366, 40, 6, 10),
(367, 40, 7, 10),
(368, 40, 8, 10),
(369, 41, 1, 10),
(370, 41, 2, 10),
(371, 41, 3, 10),
(372, 41, 4, 10),
(373, 41, 5, 10),
(374, 41, 6, 10),
(375, 41, 7, 10),
(376, 41, 8, 10),
(377, 48, 1, 10),
(378, 48, 2, 10),
(379, 48, 3, 10),
(380, 48, 4, 10),
(381, 48, 5, 10),
(382, 48, 6, 10),
(383, 48, 7, 10),
(384, 48, 8, 10),
(385, 49, 1, 10),
(386, 49, 2, 10),
(387, 49, 3, 10),
(388, 49, 4, 10),
(389, 49, 5, 10),
(390, 49, 6, 10),
(391, 49, 7, 10),
(392, 49, 8, 10),
(393, 50, 1, 10),
(394, 50, 2, 10),
(395, 50, 3, 10),
(396, 50, 4, 10),
(397, 50, 5, 10),
(398, 50, 6, 10),
(399, 50, 7, 10),
(400, 50, 8, 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Cấu trúc bảng cho bảng `wishlists`
--
CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Cấu trúc bảng cho bảng `coupons`
--
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `scope_type` enum('product','category','order') NOT NULL DEFAULT 'order',
  `scope_id` int(11) DEFAULT NULL,
  `discount_type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sizes`
--

INSERT INTO `sizes` (`id`, `size`, `created_at`) VALUES
(1, '38', '2025-11-12 16:03:04'),
(2, '39', '2025-11-12 16:03:04'),
(3, '40', '2025-11-12 16:03:04'),
(4, '41', '2025-11-12 16:03:04'),
(5, '42', '2025-11-12 16:03:04'),
(6, '43', '2025-11-12 16:03:04'),
(7, '44', '2025-11-12 16:03:04'),
(8, '45', '2025-11-12 16:03:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `subcategories`
--

INSERT INTO `subcategories` (`id`, `id_category`, `name`, `created_at`) VALUES
(1, 1, 'Chạy bộ', '2025-11-12 16:03:04'),
(2, 1, 'Bóng đá', '2025-11-12 16:03:04'),
(3, 1, 'Bóng rổ', '2025-11-12 16:03:04'),
(4, 2, 'Chạy bộ', '2025-11-12 16:03:04'),
(5, 2, 'Lifestyle', '2025-11-12 16:03:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` enum('active','locked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `status`, `created_at`) VALUES
-- pass: admin123
(1, 'admin@gmail.com', '$2y$10$Nb3hMe7TxXWRZxU4lvJPQ.X924FotkQl5ZmTVGWRWlQ45kbx51yci', 'Quản trị viên', '0123456789', 'Hà Nội', 'admin', 'active', '2025-11-12 16:03:04'),
-- pass: customer123
(2, 'minh.khoi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Minh Khôi', '0987123456', '12 Nguyễn Trãi, Hà Nội', 'customer', 'active', '2025-11-12 16:03:04'),
(3, 'thu.ha@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thu Hà', '0905123123', '89 Lý Tự Trọng, TP.HCM', 'customer', 'active', '2025-11-12 16:03:04'),
(4, 'anh.tuan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ngô Anh Tuấn', '0912345678', '45 Pasteur, Đà Nẵng', 'customer', 'active', '2025-11-12 16:03:04'),
(5, 'bao.an@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vũ Bảo An', '0978123987', '27 Lê Lợi, Huế', 'customer', 'active', '2025-11-12 16:03:04'),
(6, 'thanh.huyen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thanh Huyền', '0933778899', '102 Điện Biên Phủ, Cần Thơ', 'customer', 'active', '2025-11-12 16:03:04'),
(7, 'buitranthienan1111@gmail.com', '$2y$10$iURTRPifEDIye7vHQdr3seINdspKal4K8nMnig3y1QBL8Znt0Lkze', 'An Bui', '0446735384', 'dsdsdfd', 'customer', 'active', '2025-11-12 16:08:36');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_size` (`id_size`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `id_user` (`id_user`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_size` (`id_size`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_subcategory` (`id_subcategory`),
  ADD KEY `id_brand` (`id_brand`);

--
-- Chỉ mục cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_size` (`id_product`,`id_size`),
  ADD KEY `id_size` (`id_size`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_user` (`id_user`);

--
-- Chỉ mục cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_product` (`id_user`,`id_product`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_product` (`id_product`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_category` (`id_category`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`id_size`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_3` FOREIGN KEY (`id_size`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_subcategory`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`id_brand`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`id_size`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

