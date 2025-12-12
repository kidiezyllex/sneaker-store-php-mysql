<?php
require_once 'includes/init.php';

$page_title = 'Danh sách sản phẩm';

$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'brand' => $_GET['brand'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'page' => isset($_GET['page']) ? (int) $_GET['page'] : 1,
];

$controller = new ProductController($db_conn);
$data = $controller->list($filters, is_logged_in() ? (int) $_SESSION['user_id'] : null);
extract($data);

$search = $filters['search'];
$category = $filters['category'];
$brand = $filters['brand'];
$min_price = $filters['min_price'];
$max_price = $filters['max_price'];
$page = $data['page'];

include 'includes/header.php';
include __DIR__ . '/includes/views/products.view.php';
include 'includes/footer.php';
