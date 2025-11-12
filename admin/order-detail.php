<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Chi tiết đơn hàng';

$order_id = $_GET['id'] ?? 0;

$stmt = $db_conn->prepare("
    SELECT o.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
    FROM orders o
    JOIN users u ON o.id_user = u.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('/admin/orders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $update_stmt = $db_conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->execute([$new_status, $order_id]);
    redirect('/admin/order-detail.php?id=' . $order_id . '&success=1');
}

$details_stmt = $db_conn->prepare("
    SELECT od.*, p.img
    FROM order_details od
    LEFT JOIN products p ON od.id_product = p.id
    WHERE od.id_order = ?
");
$details_stmt->execute([$order_id]);
$details = $details_stmt->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Chi tiết đơn hàng #<?php echo htmlspecialchars($order['order_code']); ?></h2>
    <a href="/admin/orders.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Cập nhật thành công!</div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Sản phẩm trong đơn hàng</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $detail): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($detail['img']); ?>" class="cart-item-image me-3" alt="">
                                        <div><?php echo htmlspecialchars($detail['product_name']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo format_price($detail['product_price']); ?></td>
                                <td><?php echo $detail['quantity']; ?></td>
                                <td><strong><?php echo format_price($detail['subtotal']); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td><strong class="text-danger"><?php echo format_price($order['total_amount']); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p class="mb-1"><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Trạng thái đơn hàng</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Trạng thái hiện tại:</strong> <?php echo get_status_badge($order['status']); ?></p>
                <p class="mb-2"><strong>Thanh toán:</strong> <?php echo $order['payment_method'] === 'cod' ? 'COD' : 'Chuyển khoản'; ?></p>
                <p class="mb-3"><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><strong>Cập nhật trạng thái:</strong></label>
                        <select name="status" class="form-select">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="shipping" <?php echo $order['status'] === 'shipping' ? 'selected' : ''; ?>>Đang giao hàng</option>
                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
