<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Tổng quan - Quản trị';

$total_orders = $db_conn->query("SELECT COUNT(*) as total FROM orders")->fetch()['total'];
$total_customers = $db_conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch()['total'];
$total_products = $db_conn->query("SELECT COUNT(*) as total FROM products")->fetch()['total'];
$total_revenue = $db_conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'")->fetch()['total'] ?? 0;

$recent_orders = $db_conn->query("
    SELECT o.*, u.full_name as customer_name
    FROM orders o
    JOIN users u ON o.id_user = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
")->fetchAll();

$order_stats = $db_conn->query("
    SELECT status, COUNT(*) as count
    FROM orders
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// PostgreSQL uses TO_CHAR, MySQL uses DATE_FORMAT
$driver = $db_conn->getAttribute(PDO::ATTR_DRIVER_NAME);

if ($driver === 'pgsql') {
    $monthly_revenue = $db_conn->query("
        SELECT TO_CHAR(created_at, 'YYYY-MM') as month, SUM(total_amount) as revenue
        FROM orders
        WHERE status = 'completed'
        GROUP BY TO_CHAR(created_at, 'YYYY-MM')
        ORDER BY TO_CHAR(created_at, 'YYYY-MM') DESC
        LIMIT 6
    ")->fetchAll();
} else {
    $monthly_revenue = $db_conn->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue
        FROM orders
        WHERE status = 'completed'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY DATE_FORMAT(created_at, '%Y-%m') DESC
        LIMIT 6
    ")->fetchAll();
}

include 'header.php';
?>

<h2 class="mb-4">Tổng quan hệ thống</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Tổng đơn hàng</h6>
                        <h2 class="mb-0"><?php echo number_format($total_orders); ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Doanh thu</h6>
                        <h2 class="mb-0"><?php echo format_price($total_revenue); ?></h2>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Khách hàng</h6>
                        <h2 class="mb-0"><?php echo number_format($total_customers); ?></h2>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Sản phẩm</h6>
                        <h2 class="mb-0"><?php echo number_format($total_products); ?></h2>
                    </div>
                    <i class="fas fa-box fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thống kê đơn hàng</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Chờ xử lý:</td>
                        <td class="text-end"><strong><?php echo $order_stats['pending'] ?? 0; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Đã xác nhận:</td>
                        <td class="text-end"><strong><?php echo $order_stats['confirmed'] ?? 0; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Đang giao:</td>
                        <td class="text-end"><strong><?php echo $order_stats['shipping'] ?? 0; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Hoàn thành:</td>
                        <td class="text-end"><strong><?php echo $order_stats['completed'] ?? 0; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Đã hủy:</td>
                        <td class="text-end"><strong><?php echo $order_stats['cancelled'] ?? 0; ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Doanh thu theo tháng</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <?php foreach ($monthly_revenue as $mr): ?>
                        <tr>
                            <td>Tháng <?php echo date('m/Y', strtotime($mr['month'] . '-01')); ?>:</td>
                            <td class="text-end"><strong><?php echo format_price($mr['revenue']); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Đơn hàng gần đây</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_code']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo format_price($order['total_amount']); ?></td>
                            <td><?php echo get_status_badge($order['status']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="/admin/order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
