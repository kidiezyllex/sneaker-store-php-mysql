<?php

class CheckoutController extends Controller
{
    public function index(int $userId): array
    {
        $cartModel = new CartModel($this->db);
        $cartItems = $cartModel->getItemsByUser($userId);
        
        if (empty($cartItems)) {
            return ['error' => 'Giỏ hàng trống'];
        }
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        $userModel = new UserModel($this->db);
        $user = $userModel->findById($userId);
        
        return [
            'cart_items' => $cartItems,
            'total' => $total,
            'user' => $user,
            'discount' => 0,
            'coupon_code' => '',
            'coupon_message' => '',
            'applied_coupon' => null,
            'error' => ''
        ];
    }

    public function process(int $userId, array $data): array
    {
        $cartModel = new CartModel($this->db);
        $cartItems = $cartModel->getItemsByUser($userId);
        
        if (empty($cartItems)) {
            return ['success' => false, 'error' => 'Giỏ hàng trống'];
        }
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        $full_name = sanitize($data['full_name']);
        $phone = sanitize($data['phone']);
        $address = sanitize($data['address']);
        $payment_method = sanitize($data['payment_method']);
        $coupon_code = sanitize($data['coupon_code'] ?? '');
        
        if (empty($full_name) || empty($phone) || empty($address) || empty($payment_method)) {
            return ['success' => false, 'error' => 'Vui lòng điền đầy đủ thông tin'];
        }
        
        $discount = 0;
        $applied_coupon = null;
        $payable = $total;
        
        if ($coupon_code) {
            $promotionModel = new PromotionModel($this->db);
            $calc = $promotionModel->calculateDiscount($cartItems, $coupon_code);
            if ($calc['success']) {
                $discount = $calc['discount'];
                $applied_coupon = $calc['coupon'];
                $payable = max(0, $total - $discount);
            } else {
                return ['success' => false, 'error' => $calc['message']];
            }
        }
        
        $this->db->beginTransaction();
        
        try {
            $order_code = generate_order_code();
            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if ($driver === 'pgsql') {
                $order_stmt = $this->db->prepare("
                    INSERT INTO orders (id_user, order_code, full_name, phone, address, payment_method, coupon_code, discount_amount, total_amount, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                    RETURNING id
                ");
                $order_stmt->execute([
                    $userId,
                    $order_code,
                    $full_name,
                    $phone,
                    $address,
                    $payment_method,
                    $applied_coupon['code'] ?? null,
                    $discount,
                    $payable
                ]);
                $order_id = $order_stmt->fetchColumn();
            } else {
                $order_stmt = $this->db->prepare("
                    INSERT INTO orders (id_user, order_code, full_name, phone, address, payment_method, coupon_code, discount_amount, total_amount, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                $order_stmt->execute([
                    $userId,
                    $order_code,
                    $full_name,
                    $phone,
                    $address,
                    $payment_method,
                    $applied_coupon['code'] ?? null,
                    $discount,
                    $payable
                ]);
                $order_id = $this->db->lastInsertId();
            }
            
            foreach ($cartItems as $item) {
                $detail_stmt = $this->db->prepare("
                    INSERT INTO order_details (id_order, id_product, id_size, product_name, product_price, quantity, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $subtotal = $item['price'] * $item['quantity'];
                $detail_stmt->execute([
                    $order_id,
                    $item['id_product'],
                    $item['id_size'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $subtotal
                ]);
            }
            
            $clear_cart = $this->db->prepare("DELETE FROM cart WHERE id_user = ?");
            $clear_cart->execute([$userId]);
            
            if ($applied_coupon) {
                $promotionModel = new PromotionModel($this->db);
                $promotionModel->markUsed((int) $applied_coupon['id']);
            }
            
            $this->db->commit();
            
            return ['success' => true, 'order_code' => $order_code];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
}

