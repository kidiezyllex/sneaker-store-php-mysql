<?php

class CartController extends Controller
{
    public function list(int $userId): array
    {
        $model = new CartModel($this->db);
        $items = $model->getItemsByUser($userId);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return ['items' => $items, 'total' => $total];
    }

    public function updateQuantity(int $userId, int $cartId, int $quantity): bool
    {
        $quantity = max(1, $quantity);
        $model = new CartModel($this->db);
        return $model->updateQuantity($cartId, $userId, $quantity);
    }

    public function removeItem(int $userId, int $cartId): bool
    {
        $model = new CartModel($this->db);
        return $model->removeItem($cartId, $userId);
    }
}

