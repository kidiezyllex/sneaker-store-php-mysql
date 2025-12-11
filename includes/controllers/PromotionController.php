<?php

class PromotionController extends Controller
{
    public function list(): array
    {
        $model = new PromotionModel($this->db);
        return $model->all();
    }

    public function create(array $payload): array
    {
        $model = new PromotionModel($this->db);

        if (empty($payload['code'])) {
            return ['success' => false, 'message' => 'Mã giảm giá không được để trống'];
        }

        $payload['discount_value'] = (float) $payload['discount_value'];
        $payload['min_order_amount'] = (float) ($payload['min_order_amount'] ?? 0);
        $payload['usage_limit'] = $payload['usage_limit'] !== '' ? (int) $payload['usage_limit'] : null;
        $payload['scope_id'] = $payload['scope_id'] !== '' ? (int) $payload['scope_id'] : null;

        $success = $model->create($payload);

        return [
            'success' => $success,
            'message' => $success ? 'Đã tạo mã giảm giá' : 'Không thể lưu mã giảm giá',
        ];
    }
}

