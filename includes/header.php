<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Cửa hàng giày thể thao'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav style="background-color: #010101 !important;" class="navbar navbar-expand-lg navbar-dark py-0">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/index.php">
                <img src="/assets/images/logo.png" alt="SneakerShop" height="60">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products.php">Sản phẩm</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <?php if (is_admin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/index.php"><i class="fas fa-tachometer-alt"></i> Quản trị</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/cart.php">
                                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                                    <?php 
                                    $cart_count = get_cart_count($db_conn, $_SESSION['user_id']);
                                    if ($cart_count > 0): 
                                    ?>
                                        <span class="badge bg-danger"><?php echo $cart_count; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/wishlist.php"><i class="fas fa-heart"></i> Yêu thích</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/orders.php"><i class="fas fa-box"></i> Đơn hàng</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo sanitize($_SESSION['full_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile.php">Thông tin cá nhân</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout.php">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main>
