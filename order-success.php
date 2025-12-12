<?php
require_once 'includes/init.php';

require_login();

$order_code = $_GET['order_code'] ?? '';
$page_title = 'Đặt hàng thành công';

include 'includes/header.php';
include 'includes/views/order-success.view.php';
include 'includes/footer.php';
