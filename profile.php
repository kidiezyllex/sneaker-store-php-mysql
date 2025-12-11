<?php
require_once 'includes/init.php';

require_login();

$page_title = 'Thông tin cá nhân';
$userController = new UserController($db_conn);
$user = $userController->profile((int) $_SESSION['user_id']);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize($_POST['full_name']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        
        $result = $userController->updateProfile((int) $_SESSION['user_id'], [
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address
        ]);
        if ($result['success']) {
            $success = $result['message'];
            $user = $userController->profile((int) $_SESSION['user_id']);
        } else {
            $error = $result['message'];
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $result = $userController->changePassword($user, $current_password, $new_password, $confirm_password);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Cập nhật thông tin</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small class="text-muted">Email không thể thay đổi</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($user['full_name']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                            <small class="text-muted">Tối thiểu 6 ký tự</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-key"></i> Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
