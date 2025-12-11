<?php

class ReviewModel extends Model
{
    public function getByProduct(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name
            FROM reviews r
            JOIN users u ON r.id_user = u.id
            WHERE r.id_product = :product
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':product' => $productId]);
        return $stmt->fetchAll();
    }

    public function getSummary(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
            FROM reviews
            WHERE id_product = :product
        ");
        $stmt->execute([':product' => $productId]);
        $row = $stmt->fetch() ?: [];
        return [
            'avg_rating' => round((float) ($row['avg_rating'] ?? 0), 1),
            'total_reviews' => (int) ($row['total_reviews'] ?? 0),
        ];
    }

    public function create(int $productId, int $userId, int $rating, ?string $comment): bool
    {
        $rating = max(1, min(5, $rating));
        $check = $this->db->prepare("SELECT id FROM reviews WHERE id_product = :product AND id_user = :user");
        $check->execute([':product' => $productId, ':user' => $userId]);
        if ($check->fetchColumn()) {
            return true; // đã tồn tại, xem như thành công để tránh lỗi trùng
        }

        $stmt = $this->db->prepare("
            INSERT INTO reviews (id_product, id_user, rating, comment)
            VALUES (:product, :user, :rating, :comment)
        ");
        return $stmt->execute([
            ':product' => $productId,
            ':user' => $userId,
            ':rating' => $rating,
            ':comment' => $comment,
        ]);
    }
}

