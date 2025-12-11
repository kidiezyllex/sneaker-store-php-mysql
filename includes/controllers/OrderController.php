<?php

class OrderController extends Controller
{
    public function listForUser(int $userId): array
    {
        $model = new OrderModel($this->db);
        return $model->getByUser($userId);
    }

    public function details(int $orderId): array
    {
        $model = new OrderModel($this->db);
        return $model->getDetails($orderId);
    }
}

