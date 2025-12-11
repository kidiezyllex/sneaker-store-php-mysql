<?php

class WishlistModel extends Model
{
    public function toggle(int $userId, int $productId): array
    {
        $check = $this->db->prepare("SELECT id FROM wishlists WHERE id_user = :user AND id_product = :product");
        $check->execute([':user' => $userId, ':product' => $productId]);
        $existing = $check->fetchColumn();

        if ($existing) {
            $delete = $this->db->prepare("DELETE FROM wishlists WHERE id = :id");
            $delete->execute([':id' => $existing]);
            return ['favorited' => false];
        }

        $insert = $this->db->prepare("
            INSERT INTO wishlists (id_user, id_product) VALUES (:user, :product)
        ");
        $insert->execute([':user' => $userId, ':product' => $productId]);
        return ['favorited' => true];
    }

    public function getProductIdsByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT id_product FROM wishlists WHERE id_user = :user");
        $stmt->execute([':user' => $userId]);
        return array_column($stmt->fetchAll(), 'id_product');
    }

    public function listByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT w.*, p.name, p.model, p.price, p.img, b.name AS brand_name
            FROM wishlists w
            JOIN products p ON w.id_product = p.id
            LEFT JOIN brands b ON p.id_brand = b.id
            WHERE w.id_user = :user
            ORDER BY w.created_at DESC
        ");
        $stmt->execute([':user' => $userId]);
        return $stmt->fetchAll();
    }
}

