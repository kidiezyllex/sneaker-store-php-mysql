<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Thanh toán';

$stmt = $db_conn->prepare("
    SELECT c.*, p.name, p.price, p.img, s.size, b.name as brand_name
    FROM cart c
    JOIN products p ON c.id_product = p.id
    JOIN sizes s ON c.id_size = s.id
    LEFT JOIN brands b ON p.id_brand = b.id
    WHERE c.id_user = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    redirect('/cart.php');
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$user_stmt = $db_conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $payment_method = sanitize($_POST['payment_method']);
    
    if (empty($full_name) || empty($phone) || empty($address) || empty($payment_method)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        $db_conn->beginTransaction();
        
        try {
            $order_code = generate_order_code();
            
            // PostgreSQL uses RETURNING, MySQL uses lastInsertId()
            $driver = $db_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if ($driver === 'pgsql') {
                $order_stmt = $db_conn->prepare("
                    INSERT INTO orders (id_user, order_code, full_name, phone, address, payment_method, total_amount, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
                    RETURNING id
                ");
                $order_stmt->execute([$_SESSION['user_id'], $order_code, $full_name, $phone, $address, $payment_method, $total]);
                $order_id = $order_stmt->fetchColumn();
            } else {
                $order_stmt = $db_conn->prepare("
                    INSERT INTO orders (id_user, order_code, full_name, phone, address, payment_method, total_amount, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                $order_stmt->execute([$_SESSION['user_id'], $order_code, $full_name, $phone, $address, $payment_method, $total]);
                $order_id = $db_conn->lastInsertId();
            }
            
            foreach ($cart_items as $item) {
                $detail_stmt = $db_conn->prepare("
                    INSERT INTO order_details (id_order, id_product, id_size, product_name, product_price, quantity, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $subtotal = $item['price'] * $item['quantity'];
                $detail_stmt->execute([
                    $order_id,
                    $item['id_product'],
                    $item['id_size'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $subtotal
                ]);
            }
            
            $clear_cart = $db_conn->prepare("DELETE FROM cart WHERE id_user = ?");
            $clear_cart->execute([$_SESSION['user_id']]);
            
            $db_conn->commit();
            $success = true;
            
            redirect('/order-success.php?order_code=' . $order_code);
        } catch (Exception $e) {
            $db_conn->rollBack();
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="checkoutForm">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($user['full_name']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" required checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="transfer" id="transfer">
                                <label class="form-check-label" for="transfer">
                                    <i class="fas fa-university"></i> Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Đơn hàng của bạn</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <strong><?php echo format_price($total); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển:</span>
                        <strong>Miễn phí</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Tổng cộng:</h5>
                        <h5 class="text-danger"><?php echo format_price($total); ?></h5>
                    </div>
                    <button type="submit" form="checkoutForm" class="btn btn-success w-100 btn-lg">
                        <i class="fas fa-check-circle"></i> Đặt hàng
                    </button>
                    <a href="/cart.php" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
