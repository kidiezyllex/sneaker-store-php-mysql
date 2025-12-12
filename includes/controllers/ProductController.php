<?php

class ProductController extends Controller
{
    public function list(array $filters, ?int $userId = null): array
    {
        $productModel = new ProductModel($this->db);
        $wishlistModel = new WishlistModel($this->db);
        $reviewModel = new ReviewModel($this->db);

        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = 9;

        $result = $productModel->search($filters, $perPage, $page);
        $total_pages = (int) ceil($result['total'] / $perPage);

        $categories = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
        $brands = $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll();
        $productIds = array_values(array_column($result['items'], 'id'));
        $productRatings = $reviewModel->getSummariesByProductIds($productIds);

        return [
            'products' => $result['items'],
            'total_products' => $result['total'],
            'total_pages' => $total_pages,
            'page' => $page,
            'per_page' => $perPage,
            'categories' => $categories,
            'brands' => $brands,
            'wishlist_product_ids' => $userId ? $wishlistModel->getProductIdsByUser($userId) : [],
            'product_ratings' => $productRatings,
        ];
    }

    public function detail(int $productId, ?int $userId = null): array
    {
        $productModel = new ProductModel($this->db);
        $reviewModel = new ReviewModel($this->db);
        $wishlistModel = new WishlistModel($this->db);

        $product = $productModel->find($productId);
        if (!$product) {
            return [];
        }

        $sizes = $productModel->getSizes($productId);
        $reviews = $reviewModel->getByProduct($productId);
        $ratingData = $reviewModel->getSummary($productId);

        return [
            'product' => $product,
            'sizes' => $sizes,
            'reviews' => $reviews,
            'avg_rating' => $ratingData['avg_rating'],
            'total_reviews' => $ratingData['total_reviews'],
            'wishlist_product_ids' => $userId ? $wishlistModel->getProductIdsByUser($userId) : [],
        ];
    }
}

