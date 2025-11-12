<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Quản lý kích cỡ';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $size = sanitize($_POST['size']);
        $db_conn->prepare("INSERT INTO sizes (size) VALUES (?)")->execute([$size]);
        redirect('/admin/sizes.php?success=1');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $db_conn->prepare("DELETE FROM sizes WHERE id = ?")->execute([$_POST['delete_id']]);
    redirect('/admin/sizes.php?success=1');
}

$sizes = $db_conn->query("SELECT * FROM sizes ORDER BY CAST(size AS UNSIGNED)")->fetchAll();

include 'header.php';
?>

<h2 class="mb-4">Quản lý kích cỡ</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thêm kích cỡ mới</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kích cỡ (VD: 38, 39, 40...)</label>
                        <input type="text" name="size" class="form-control" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Thêm kích cỡ
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Danh sách kích cỡ</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kích cỡ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sizes as $size): ?>
                            <tr>
                                <td><?php echo $size['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($size['size']); ?></strong></td>
                                <td class="table-actions">
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                        <input type="hidden" name="delete_id" value="<?php echo $size['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
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
</div>

<?php include 'footer.php'; ?>
