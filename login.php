<?php
require_once 'includes/init.php';

$page_title = 'Đăng nhập';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        $auth = new AuthController($db_conn);
        $result = $auth->login($email, $password);
        if ($result['success']) {
            if (($result['role'] ?? '') === 'admin') {
                redirect('/admin/index.php');
            }
            redirect('/index.php');
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
                    <h5 class="mb-0"><i style="margin-right: 4px;" class="fas fa-sign-in-alt"></i> Đăng nhập</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required value="<?php echo $_POST['email'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt"></i> Đăng nhập</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Chưa có tài khoản? <a href="/register.php">Đăng ký ngay</a></p>
                        <div class="alert alert-info mt-4">
                            <strong>Tài khoản demo Admin:</strong><br>
                            Email: admin@gmail.com<br>
                            Password: admin123
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
