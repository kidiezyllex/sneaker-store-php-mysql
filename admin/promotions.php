<?php
require_once '../includes/init.php';
require_login();
require_admin();

$page_title = 'Quản lý khuyến mãi';
$controller = new PromotionController($db_conn);
$promotionModel = new PromotionModel($db_conn);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->create($_POST);
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$promotions = $controller->list();
$categories = $db_conn->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$products = $db_conn->query("SELECT id, model FROM products ORDER BY date_add DESC LIMIT 200")->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h3">Mã giảm giá</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header">Tạo mã mới</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mã giảm giá</label>
                        <input type="text" name="code" class="form-control" required placeholder="SALE10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phạm vi áp dụng</label>
                        <select name="scope_type" class="form-select" required>
                            <option value="order">Toàn bộ đơn hàng</option>
                            <option value="category">Theo danh mục</option>
                            <option value="product">Theo sản phẩm</option>
                        </select>
                        <small class="text-muted">Chọn loại rồi nhập ID danh mục/sản phẩm bên dưới (nếu cần).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục/Sản phẩm mục tiêu (ID)</label>
                        <input type="number" name="scope_id" class="form-control" placeholder="ID danh mục hoặc sản phẩm">
                        <small class="text-muted">Danh mục khả dụng: 
                            <?php foreach ($categories as $c): ?>
                                <span class="badge bg-light text-dark">#<?php echo $c['id']; ?> <?php echo htmlspecialchars($c['name']); ?></span>
                            <?php endforeach; ?>
                        </small>
                        <br>
                        <small class="text-muted">Sản phẩm gần đây:
                            <?php foreach ($products as $p): ?>
                                <span class="badge bg-secondary">#<?php echo $p['id']; ?> <?php echo htmlspecialchars($p['model']); ?></span>
                            <?php endforeach; ?>
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loại giảm</label>
                            <select name="discount_type" class="form-select">
                                <option value="percent">Phần trăm (%)</option>
                                <option value="fixed">Số tiền (đ)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá trị</label>
                            <input type="number" name="discount_value" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số lần sử dụng tối đa</label>
                            <input type="number" name="usage_limit" class="form-control" min="0" placeholder="Để trống nếu không giới hạn">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn tối thiểu</label>
                            <input type="number" name="min_order_amount" class="form-control" min="0" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bắt đầu</label>
                            <input type="datetime-local" name="start_at" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kết thúc</label>
                            <input type="datetime-local" name="end_at" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="active">Kích hoạt</option>
                            <option value="inactive">Tạm tắt</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus"></i> Tạo mã</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Danh sách mã</div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Phạm vi</th>
                            <th>Thời gian</th>
                            <th>Đã dùng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promotions as $promo): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($promo['code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($promo['discount_type']); ?></td>
                                <td>
                                    <?php echo $promo['discount_type'] === 'percent'
                                        ? $promo['discount_value'] . '%'
                                        : format_price($promo['discount_value']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($promo['scope_type'] . ($promo['scope_id'] ? ' #' . $promo['scope_id'] : '')); ?></td>
                                <td>
                                    <div><small>From: <?php echo $promo['start_at'] ?: '-'; ?></small></div>
                                    <div><small>To: <?php echo $promo['end_at'] ?: '-'; ?></small></div>
                                </td>
                                <td><?php echo $promo['used_count']; ?><?php echo $promo['usage_limit'] ? ' / ' . $promo['usage_limit'] : ''; ?></td>
                                <td><?php echo $promo['status'] === 'active' ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-secondary">Tắt</span>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

