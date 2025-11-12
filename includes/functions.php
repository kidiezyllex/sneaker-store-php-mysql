<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit();
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: /index.php');
        exit();
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function format_price($price) {
    return number_format($price, 0, ',', '.') . ' ₫';
}

function generate_order_code() {
    return 'ORD' . date('Ymd') . rand(1000, 9999);
}

function get_cart_count($db_conn, $user_id) {
    $stmt = $db_conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE id_user = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function get_status_badge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning text-dark">Chờ xử lý</span>',
        'confirmed' => '<span class="badge bg-info">Đã xác nhận</span>',
        'shipping' => '<span class="badge bg-primary">Đang giao</span>',
        'completed' => '<span class="badge bg-success">Hoàn thành</span>',
        'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
        'active' => '<span class="badge bg-success">Hoạt động</span>',
        'locked' => '<span class="badge bg-danger">Đã khóa</span>'
    ];
    return $badges[$status] ?? $status;
}

function upload_image($file, $directory = 'uploads/products/') {
    if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return false;
    }
    
    $new_filename = uniqid() . '.' . $ext;
    $destination = $directory . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return '/' . $destination;
    }
    
    return false;
}
?>
