<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

require_login();

$order_code = $_GET['order_code'] ?? '';
$page_title = 'Đặt hàng thành công';

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    <h2 class="mt-4">Đặt hàng thành công!</h2>
                    <p class="lead">Cảm ơn bạn đã đặt hàng tại LnAnhStore</p>
                    <p>Mã đơn hàng của bạn: <strong class="text-primary"><?php echo htmlspecialchars($order_code); ?></strong></p>
                    <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
                    <hr>
                    <a href="/orders.php" class="btn btn-primary me-2"><i class="fas fa-box"></i> Xem đơn hàng</a>
                    <a href="/products.php" class="btn btn-outline-secondary"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
