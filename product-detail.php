<?php
require_once 'includes/init.php';

$product_id = (int) ($_GET['id'] ?? 0);

$productController = new ProductController($db_conn);
$data = $productController->detail($product_id, is_logged_in() ? (int) $_SESSION['user_id'] : null);

if (empty($data)) {
    redirect('/products.php');
}

extract($data);

$page_title = $product['name'];

$message = '';
$error = '';
$reviewMessage = '';
$reviewError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        if (!is_logged_in()) {
            redirect('/login.php');
        }
        
        if (is_admin()) {
            $error = 'Tài khoản admin không thể mua hàng';
        } else {
            $size_id = $_POST['size'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if (!$size_id) {
                $error = 'Vui lòng chọn size';
            } else {
                $check_stmt = $db_conn->prepare("SELECT * FROM cart WHERE id_user = ? AND id_product = ? AND id_size = ?");
                $check_stmt->execute([$_SESSION['user_id'], $product_id, $size_id]);
                $existing = $check_stmt->fetch();
                
                if ($existing) {
                    $update_stmt = $db_conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
                    $update_stmt->execute([$quantity, $existing['id']]);
                } else {
                    $insert_stmt = $db_conn->prepare("INSERT INTO cart (id_user, id_product, id_size, quantity) VALUES (?, ?, ?, ?)");
                    $insert_stmt->execute([$_SESSION['user_id'], $product_id, $size_id, $quantity]);
                }
                
                $message = 'Đã thêm vào giỏ hàng thành công!';
            }
        }
    }

    if (isset($_POST['add_review'])) {
        if (!is_logged_in()) {
            redirect('/login.php');
        }

        if (is_admin()) {
            $reviewError = 'Tài khoản admin không thể đánh giá sản phẩm';
        } else {
            $rating = (int) ($_POST['rating'] ?? 0);
            $comment = sanitize($_POST['comment'] ?? '');
            $reviewController = new ReviewController($db_conn);
            $result = $reviewController->store($product_id, (int) $_SESSION['user_id'], $rating, $comment);

            if ($result['success']) {
                $reviewMessage = $result['message'];
                // refresh review data
                $refresh = $productController->detail($product_id, (int) $_SESSION['user_id']);
                $reviews = $refresh['reviews'];
                $avg_rating = $refresh['avg_rating'];
                $total_reviews = $refresh['total_reviews'];
                $wishlist_product_ids = $refresh['wishlist_product_ids'];
            } else {
                $reviewError = $result['message'];
            }
        }
    }
}

$page_title = $product['name'];
include 'includes/header.php';
include __DIR__ . '/includes/views/product-detail.view.php';
include 'includes/footer.php';
