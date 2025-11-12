<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Quản lý khách hàng';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status_id'])) {
    $user_id = $_POST['toggle_status_id'];
    $stmt = $db_conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    $new_status = $user['status'] === 'active' ? 'locked' : 'active';
    $db_conn->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$new_status, $user_id]);
    redirect('/admin/customers.php?success=1');
}

$customers = $db_conn->query("
    SELECT u.*, 
           COUNT(DISTINCT o.id) as total_orders,
           COALESCE(SUM(o.total_amount), 0) as total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.id_user
    WHERE u.role = 'customer'
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();

include 'header.php';
?>

<h2 class="mb-4">Quản lý khách hàng</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Tổng đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                            <td><?php echo $customer['total_orders']; ?></td>
                            <td><?php echo format_price($customer['total_spent']); ?></td>
                            <td><?php echo get_status_badge($customer['status']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></td>
                            <td class="table-actions">
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn <?php echo $customer['status'] === 'active' ? 'khóa' : 'mở khóa'; ?> tài khoản này?')">
                                    <input type="hidden" name="toggle_status_id" value="<?php echo $customer['id']; ?>">
                                    <button type="submit" class="btn btn-sm <?php echo $customer['status'] === 'active' ? 'btn-danger' : 'btn-success'; ?>">
                                        <i class="fas fa-<?php echo $customer['status'] === 'active' ? 'lock' : 'unlock'; ?>"></i>
                                        <?php echo $customer['status'] === 'active' ? 'Khóa' : 'Mở'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
