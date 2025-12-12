<?php

class HomeController extends Controller
{
    public function index(?int $userId = null): array
    {
        $productModel = new ProductModel($this->db);
        $wishlistModel = new WishlistModel($this->db);
        $reviewModel = new ReviewModel($this->db);

        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $revenueIntervalExpression = $driver === 'pgsql'
            ? "NOW() - INTERVAL '30 days'"
            : "DATE_SUB(NOW(), INTERVAL 30 DAY)";

        $metricsStmt = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM products WHERE visibility = 1) AS total_products,
                (SELECT COUNT(*) FROM orders) AS total_orders,
                (SELECT COUNT(*) FROM users WHERE role = 'customer' AND status = 'active') AS active_customers,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE created_at >= $revenueIntervalExpression) AS revenue_30_days
        ");
        $metrics = $metricsStmt->fetch() ?: [
            'total_products' => 0,
            'total_orders' => 0,
            'active_customers' => 0,
            'revenue_30_days' => 0
        ];

        $categoryIcons = [
            'Nam' => 'fa-person-running',
            'Nữ' => 'fa-person-dress',
            'Trẻ em' => 'fa-children',
            'Giày trẻ sơ sinh' => 'fa-baby',
            'Phụ kiện' => 'fa-socks'
        ];

        $testimonials = [
            [
                'name' => 'Nguyễn Minh Anh',
                'role' => 'Marathon Runner',
                'avatar' => 'https://images.pexels.com/photos/3760259/pexels-photo-3760259.jpeg?auto=compress&cs=tinysrgb&w=80',
                'quote' => '“SneakerShop luôn cập nhật những đôi giày chạy bộ mới nhất, dịch vụ tư vấn chuyên nghiệp giúp tôi chọn được đôi phù hợp nhất cho mỗi giải đấu.”'
            ],
            [
                'name' => 'Trần Thu Hà',
                'role' => 'Fashion Blogger',
                'avatar' => 'https://images.pexels.com/photos/733872/pexels-photo-733872.jpeg?auto=compress&cs=tinysrgb&w=80',
                'quote' => '“Tôi yêu những bộ sưu tập hạn chế tại SneakerShop. Từng chi tiết đóng gói đến bảo hành đều khiến tôi cảm thấy mình là khách hàng VIP.”'
            ],
            [
                'name' => 'Lê Hoàng Long',
                'role' => 'Basketball Coach',
                'avatar' => 'https://images.pexels.com/photos/2379005/pexels-photo-2379005.jpeg?auto=compress&cs=tinysrgb&w=80',
                'quote' => '“Thiết bị tập luyện cần sự tin cậy. SneakerShop cung cấp những dòng giày bóng rổ chất lượng với giá hợp lý và giao hàng cực nhanh.”'
            ]
        ];

        $blogPosts = [
            [
                'title' => '5 Bí quyết chăm sóc sneaker luôn trắng mới',
                'category' => 'Chăm sóc giày',
                'image' => 'https://images.pexels.com/photos/19090/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=600',
                'excerpt' => 'Từ việc vệ sinh sau mỗi lần sử dụng đến bảo quản với silica gel, đây là những mẹo bạn nên áp dụng ngay.',
                'url' => '#'
            ],
            [
                'title' => 'Xu hướng sneaker lifestyle nổi bật 2025',
                'category' => 'Phong cách',
                'image' => 'https://images.pexels.com/photos/292999/pexels-photo-292999.jpeg?auto=compress&cs=tinysrgb&w=600',
                'excerpt' => 'Cùng khám phá những thiết kế đang làm mưa làm gió với chất liệu sustainable và phối màu retro.',
                'url' => '#'
            ],
            [
                'title' => 'Chọn giày chạy bộ theo bàn chân và cự ly',
                'category' => 'Huấn luyện',
                'image' => 'https://images.pexels.com/photos/936094/pexels-photo-936094.jpeg?auto=compress&cs=tinysrgb&w=600',
                'excerpt' => 'Hiểu về cấu trúc bàn chân sẽ giúp bạn tối ưu hiệu suất và tránh chấn thương trong quá trình luyện tập.',
                'url' => '#'
            ]
        ];

        $featured = $productModel->getFeatured();
        $bestSellers = $productModel->getBestSellers();
        $ratingIds = array_unique(array_merge(
            array_column($featured, 'id'),
            array_column($bestSellers, 'id')
        ));
        $productRatings = $reviewModel->getSummariesByProductIds($ratingIds);

        return [
            'page_title' => 'Trang chủ - Cửa hàng giày thể thao',
            'metrics' => $metrics,
            'featured_products' => $featured,
            'best_sellers' => $bestSellers,
            'top_categories' => $productModel->getTopCategories(),
            'top_brands' => $productModel->getTopBrands(),
            'categoryIcons' => $categoryIcons,
            'testimonials' => $testimonials,
            'blog_posts' => $blogPosts,
            'wishlist_product_ids' => $userId ? $wishlistModel->getProductIdsByUser($userId) : [],
            'product_ratings' => $productRatings,
        ];
    }
}

