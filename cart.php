<?php
require_once 'includes/init.php';

require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Giỏ hàng';
$cartController = new CartController($db_conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update') {
        $cart_id = (int) $_POST['cart_id'];
        $quantity = max(1, (int) $_POST['quantity']);
        
        $cartController->updateQuantity((int) $_SESSION['user_id'], $cart_id, $quantity);
        echo json_encode(['success' => true]);
        exit();
    }
    
    if ($_POST['action'] === 'remove') {
        $cart_id = (int) $_POST['cart_id'];
        $cartController->removeItem((int) $_SESSION['user_id'], $cart_id);
        
        echo json_encode(['success' => true]);
        exit();
    }
}

$cartData = $cartController->list((int) $_SESSION['user_id']);
$cart_items = $cartData['items'];
$total = $cartData['total'];

include 'includes/header.php';
include 'includes/views/cart.view.php';
include 'includes/footer.php';
