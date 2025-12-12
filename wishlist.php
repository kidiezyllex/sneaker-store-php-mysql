<?php
require_once 'includes/init.php';
require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Sản phẩm yêu thích';

$controller = new WishlistController($db_conn);
$wishlistItems = $controller->index((int) $_SESSION['user_id']);

include 'includes/header.php';
include 'includes/views/wishlist.view.php';
include 'includes/footer.php';

