<?php
require_once 'includes/init.php';

require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Đơn hàng của tôi';
$orderController = new OrderController($db_conn);
$reviewController = new ReviewController($db_conn);

$success = '';
if (isset($_POST['add_review'])) {
    $order_id = (int) $_POST['order_id'];
    $product_id = (int) $_POST['product_id'];
    $rating = (int) $_POST['rating'];
    $comment = sanitize($_POST['comment']);
    
    $result = $reviewController->store($product_id, (int) $_SESSION['user_id'], $rating, $comment);
    if ($result['success']) {
        $success = 'Đã thêm đánh giá thành công!';
    }
}

$orders = $orderController->listForUser((int) $_SESSION['user_id']);

include 'includes/header.php';
include 'includes/views/orders.view.php';
include 'includes/footer.php';
