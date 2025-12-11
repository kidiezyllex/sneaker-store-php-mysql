<?php
require_once 'includes/init.php';

$page_title = 'Đăng ký tài khoản';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    if (empty($email) || empty($password) || empty($full_name)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } else {
        $auth = new AuthController($db_conn);
        $result = $auth->register([
            'email' => $email,
            'password' => $password,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address
        ]);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i style="margin-right: 4px;" class="fas fa-user-plus"></i> Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?> <a href="/login.php">Đăng nhập ngay</a></div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required value="<?php echo $_POST['email'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" required value="<?php echo $_POST['full_name'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo $_POST['phone'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo $_POST['address'] ?? ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-user-plus"></i> Đăng ký</button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Đã có tài khoản? <a href="/login.php">Đăng nhập ngay</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
