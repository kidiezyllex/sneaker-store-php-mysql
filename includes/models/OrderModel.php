<?php

class OrderModel extends Model
{
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM orders
            WHERE id_user = :user
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user' => $userId]);
        return $stmt->fetchAll();
    }

    public function getDetails(int $orderId): array
    {
        $stmt = $this->db->prepare("
            SELECT od.*, p.img
            FROM order_details od
            LEFT JOIN products p ON od.id_product = p.id
            WHERE od.id_order = :order
        ");
        $stmt->execute([':order' => $orderId]);
        return $stmt->fetchAll();
    }
}

