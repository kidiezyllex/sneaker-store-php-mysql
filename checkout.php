<?php
require_once 'includes/init.php';

require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Thanh toán';

$stmt = $db_conn->prepare("
    SELECT c.*, p.name, p.price, p.img, p.id_category, s.size, b.name as brand_name
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

$discount = 0;
$coupon_code = $_POST['coupon_code'] ?? '';
$coupon_message = '';
$applied_coupon = null;
$promotionModel = null;

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
    $coupon_code = sanitize($_POST['coupon_code'] ?? '');
    
    if (empty($full_name) || empty($phone) || empty($address) || empty($payment_method)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        $payable = $total;
        if ($coupon_code) {
            $promotionModel = new PromotionModel($db_conn);
            $calc = $promotionModel->calculateDiscount($cart_items, $coupon_code);
            if ($calc['success']) {
                $discount = $calc['discount'];
                $applied_coupon = $calc['coupon'];
                $payable = max(0, $total - $discount);
                $coupon_message = 'Áp dụng mã thành công';
            } else {
                $coupon_message = $calc['message'];
                $error = $coupon_message;
            }
        }

        if ($error) {
            // Dừng lại nếu có lỗi coupon/validation
        } else {
        $db_conn->beginTransaction();
        
        try {
            $order_code = generate_order_code();
            
            // PostgreSQL uses RETURNING, MySQL uses lastInsertId()
            $driver = $db_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
            
                if ($driver === 'pgsql') {
                    $order_stmt = $db_conn->prepare("
                        INSERT INTO orders (id_user, order_code, full_name, phone, address, payment_method, coupon_code, discount_amount, total_amount, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                        RETURNING id
                    ");
                    $order_stmt->execute([
                        $_SESSION['user_id'],
                        $order_code,
                        $full_name,
                        $phone,
                        $address,
                        $payment_method,
                        $applied_coupon['code'] ?? null,
                        $discount,
                        $payable
                    ]);
                    $order_id = $order_stmt->fetchColumn();
                } else {
                    $order_stmt = $db_conn->prepare("
                        INSERT INTO orders (id_user, order_code, full_name, phone, address, payment_method, coupon_code, discount_amount, total_amount, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                    ");
                    $order_stmt->execute([
                        $_SESSION['user_id'],
                        $order_code,
                        $full_name,
                        $phone,
                        $address,
                        $payment_method,
                        $applied_coupon['code'] ?? null,
                        $discount,
                        $payable
                    ]);
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
                if ($applied_coupon && $promotionModel) {
                    $promotionModel->markUsed((int) $applied_coupon['id']);
                }
            
            $db_conn->commit();
            $success = true;
            
            redirect('/order-success.php?order_code=' . $order_code);
        } catch (Exception $e) {
            $db_conn->rollBack();
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
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
                    <div class="mb-3">
                        <label class="form-label">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="coupon_code" form="checkoutForm" placeholder="Nhập mã" value="<?php echo htmlspecialchars($coupon_code); ?>">
                            <button class="btn btn-outline-secondary" type="submit" form="checkoutForm">
                                <i class="fas fa-check"></i> Áp dụng
                            </button>
                        </div>
                        <?php if ($coupon_message): ?>
                            <small class="<?php echo $applied_coupon ? 'text-success' : 'text-danger'; ?>"><?php echo htmlspecialchars($coupon_message); ?></small>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <strong><?php echo format_price($total); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Giảm giá:</span>
                        <strong class="text-success">- <?php echo format_price($discount); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển:</span>
                        <strong>Miễn phí</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Tổng cộng:</h5>
                        <h5 class="text-danger"><?php echo format_price(max(0, $total - $discount)); ?></h5>
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
