<?php
require_once 'includes/init.php';

require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Thanh toán';
$checkoutController = new CheckoutController($db_conn);

$error = '';
$coupon_message = '';
$applied_coupon = null;
$coupon_code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nếu có full_name thì là đặt hàng, không thì chỉ apply coupon
    if (!empty($_POST['full_name'])) {
        $result = $checkoutController->process((int) $_SESSION['user_id'], $_POST);
        
        if ($result['success']) {
            redirect('/order-success.php?order_code=' . $result['order_code']);
        } else {
            $error = $result['error'];
            $coupon_code = sanitize($_POST['coupon_code'] ?? '');
            if ($coupon_code && strpos($error, 'mã') !== false) {
                $coupon_message = $error;
            }
        }
    } else {
        // Chỉ apply coupon
        $coupon_code = sanitize($_POST['coupon_code'] ?? '');
        if ($coupon_code) {
            $data = $checkoutController->index((int) $_SESSION['user_id']);
            $promotionModel = new PromotionModel($db_conn);
            $calc = $promotionModel->calculateDiscount($data['cart_items'], $coupon_code);
            if ($calc['success']) {
                $applied_coupon = $calc['coupon'];
                $coupon_message = 'Áp dụng mã thành công';
            } else {
                $coupon_message = $calc['message'];
                $error = $calc['message'];
            }
        }
    }
}

$data = $checkoutController->index((int) $_SESSION['user_id']);

// Nếu giỏ trống, giữ lại trên trang và hiển thị thông báo thay vì redirect
if (isset($data['error'])) {
    $error = $error ?: $data['error'];
}

// Gán dữ liệu với giá trị mặc định để tránh lỗi khi giỏ trống
$cart_items = $data['cart_items'] ?? [];
$total = $data['total'] ?? 0;
$user = $data['user'] ?? [];
$discount = $discount ?? ($data['discount'] ?? 0);
$coupon_code = $coupon_code ?? ($data['coupon_code'] ?? '');
$coupon_message = $coupon_message ?? ($data['coupon_message'] ?? '');
$applied_coupon = $applied_coupon ?? ($data['applied_coupon'] ?? null);

// Nếu đã apply coupon thì cập nhật discount
if ($applied_coupon) {
    $promotionModel = new PromotionModel($db_conn);
    $calc = $promotionModel->calculateDiscount($cart_items, $applied_coupon['code']);
    if ($calc['success']) {
        $discount = $calc['discount'];
    }
}

include 'includes/header.php';
include 'includes/views/checkout.view.php';
include 'includes/footer.php';
