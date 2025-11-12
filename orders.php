<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Đơn hàng của tôi';

$stmt = $db_conn->prepare("
    SELECT * FROM orders 
    WHERE id_user = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

if (isset($_POST['add_review'])) {
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = sanitize($_POST['comment']);
    
    $check_review = $db_conn->prepare("SELECT * FROM reviews WHERE id_product = ? AND id_user = ?");
    $check_review->execute([$product_id, $_SESSION['user_id']]);
    
    if (!$check_review->fetch()) {
        $insert_review = $db_conn->prepare("INSERT INTO reviews (id_product, id_user, rating, comment) VALUES (?, ?, ?, ?)");
        $insert_review->execute([$product_id, $_SESSION['user_id'], $rating, $comment]);
        $success = 'Đã thêm đánh giá thành công!';
    }
}

include 'includes/header.php';
?>

<div class="container pt-4">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> Bạn chưa có đơn hàng nào
        </div>
        <a href="/products.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Mua sắm ngay</a>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong>Mã đơn hàng:</strong> <?php echo htmlspecialchars($order['order_code']); ?>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <small><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <?php echo get_status_badge($order['status']); ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $details_stmt = $db_conn->prepare("
                        SELECT od.*, p.img
                        FROM order_details od
                        LEFT JOIN products p ON od.id_product = p.id
                        WHERE od.id_order = ?
                    ");
                    $details_stmt->execute([$order['id']]);
                    $details = $details_stmt->fetchAll();
                    ?>
                    
                    <?php foreach ($details as $detail): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="<?php echo htmlspecialchars($detail['img']); ?>" class="cart-item-image me-3" alt="">
                            <div class="flex-grow-1">
                                <h6><?php echo htmlspecialchars($detail['product_name']); ?></h6>
                                <p class="mb-0 text-muted">Số lượng: <?php echo $detail['quantity']; ?> | <?php echo format_price($detail['product_price']); ?></p>
                            </div>
                            <div class="text-end">
                                <strong><?php echo format_price($detail['subtotal']); ?></strong>
                                <?php if ($order['status'] === 'completed'): ?>
                                    <?php
                                    $check_review = $db_conn->prepare("SELECT * FROM reviews WHERE id_product = ? AND id_user = ?");
                                    $check_review->execute([$detail['id_product'], $_SESSION['user_id']]);
                                    if (!$check_review->fetch()):
                                    ?>
                                        <br>
                                        <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $detail['id']; ?>">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                        
                                        <div class="modal fade" id="reviewModal<?php echo $detail['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Đánh giá sản phẩm</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo $detail['id_product']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Đánh giá:</label>
                                                                <div class="btn-group" role="group">
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <input type="radio" class="btn-check" name="rating" id="rating<?php echo $detail['id']; ?>_<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                                                        <label class="btn btn-outline-warning" for="rating<?php echo $detail['id']; ?>_<?php echo $i; ?>">
                                                                            <i class="fas fa-star"></i> <?php echo $i; ?>
                                                                        </label>
                                                                    <?php endfor; ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Nhận xét:</label>
                                                                <textarea name="comment" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" name="add_review" class="btn btn-primary">Gửi đánh giá</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p class="mb-1"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                            <p class="mb-1"><strong>Thanh toán:</strong> 
                                <?php echo $order['payment_method'] === 'cod' ? 'COD' : 'Chuyển khoản'; ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5>Tổng tiền: <span class="text-danger"><?php echo format_price($order['total_amount']); ?></span></h5>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
