<?php

class PromotionModel extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM coupons ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO coupons (code, description, scope_type, scope_id, discount_type, discount_value, usage_limit, start_at, end_at, min_order_amount, status)
            VALUES (:code, :description, :scope_type, :scope_id, :discount_type, :discount_value, :usage_limit, :start_at, :end_at, :min_order_amount, :status)
        ");

        return $stmt->execute([
            ':code' => strtoupper(trim($data['code'])),
            ':description' => $data['description'] ?? null,
            ':scope_type' => $data['scope_type'],
            ':scope_id' => $data['scope_id'] ?: null,
            ':discount_type' => $data['discount_type'],
            ':discount_value' => $data['discount_value'],
            ':usage_limit' => $data['usage_limit'] ?: null,
            ':start_at' => $data['start_at'] ?: null,
            ':end_at' => $data['end_at'] ?: null,
            ':min_order_amount' => $data['min_order_amount'] ?? 0,
            ':status' => $data['status'] ?? 'active',
        ]);
    }

    public function findActiveByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM coupons
            WHERE code = :code AND status = 'active'
        ");
        $stmt->execute([':code' => strtoupper($code)]);
        $coupon = $stmt->fetch();
        if (!$coupon) {
            return null;
        }

        $now = new DateTimeImmutable();
        if ($coupon['start_at'] && $now < new DateTimeImmutable($coupon['start_at'])) {
            return null;
        }
        if ($coupon['end_at'] && $now > new DateTimeImmutable($coupon['end_at'])) {
            return null;
        }
        if (!empty($coupon['usage_limit']) && $coupon['used_count'] >= $coupon['usage_limit']) {
            return null;
        }

        return $coupon;
    }

    public function calculateDiscount(array $cartItems, string $code): array
    {
        $coupon = $this->findActiveByCode($code);
        if (!$coupon) {
            return ['success' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn'];
        }

        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }

        $applicableTotal = 0;
        if ($coupon['scope_type'] === 'order') {
            $applicableTotal = $cartTotal;
        } elseif ($coupon['scope_type'] === 'product') {
            foreach ($cartItems as $item) {
                if ((int) $item['id_product'] === (int) $coupon['scope_id']) {
                    $applicableTotal += $item['price'] * $item['quantity'];
                }
            }
        } elseif ($coupon['scope_type'] === 'category') {
            foreach ($cartItems as $item) {
                if ((int) $item['id_category'] === (int) $coupon['scope_id']) {
                    $applicableTotal += $item['price'] * $item['quantity'];
                }
            }
        }

        if ($applicableTotal <= 0) {
            return ['success' => false, 'message' => 'Mã giảm không áp dụng cho giỏ hàng hiện tại'];
        }

        if (!empty($coupon['min_order_amount']) && $cartTotal < $coupon['min_order_amount']) {
            return ['success' => false, 'message' => 'Chưa đạt giá trị tối thiểu để dùng mã'];
        }

        $discount = 0;
        if ($coupon['discount_type'] === 'percent') {
            $discount = $applicableTotal * ($coupon['discount_value'] / 100);
        } else {
            $discount = $coupon['discount_value'];
        }

        $discount = min($discount, $applicableTotal);

        return [
            'success' => true,
            'discount' => round($discount, 2),
            'coupon' => $coupon,
        ];
    }

    public function markUsed(int $couponId): void
    {
        $stmt = $this->db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = :id");
        $stmt->execute([':id' => $couponId]);
    }
}

