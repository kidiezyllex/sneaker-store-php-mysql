<?php
require_once 'includes/init.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu yêu thích']);
    exit;
}

$productId = (int) ($_POST['product_id'] ?? 0);
if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu mã sản phẩm']);
    exit;
}

try {
    $controller = new WishlistController($db_conn);
    $result = $controller->toggle((int) $_SESSION['user_id'], $productId);
    echo json_encode(['success' => true, 'favorited' => $result['favorited']]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật yêu thích']);
}

