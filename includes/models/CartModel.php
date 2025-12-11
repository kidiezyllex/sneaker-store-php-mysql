<?php

class CartModel extends Model
{
    public function getItemsByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, p.name, p.price, p.img, p.id_category, s.size, b.name as brand_name
            FROM cart c
            JOIN products p ON c.id_product = p.id
            JOIN sizes s ON c.id_size = s.id
            LEFT JOIN brands b ON p.id_brand = b.id
            WHERE c.id_user = :user
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':user' => $userId]);
        return $stmt->fetchAll();
    }

    public function updateQuantity(int $cartId, int $userId, int $quantity): bool
    {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = :qty WHERE id = :id AND id_user = :user");
        return $stmt->execute([':qty' => $quantity, ':id' => $cartId, ':user' => $userId]);
    }

    public function removeItem(int $cartId, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE id = :id AND id_user = :user");
        return $stmt->execute([':id' => $cartId, ':user' => $userId]);
    }
}

