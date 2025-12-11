<?php

class WishlistController extends Controller
{
    public function index(int $userId): array
    {
        $model = new WishlistModel($this->db);
        return $model->listByUser($userId);
    }

    public function toggle(int $userId, int $productId): array
    {
        $model = new WishlistModel($this->db);
        return $model->toggle($userId, $productId);
    }
}

