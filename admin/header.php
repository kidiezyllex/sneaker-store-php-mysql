<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Quản trị - LnAnhStore'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/index.php"><i class="fas fa-tachometer-alt"></i> Quản trị LnAnhStore</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Xem website</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo sanitize($_SESSION['full_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/logout.php">Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row mt-4">
            <nav class="col-md-2 d-md-block admin-sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/index.php">
                                <i class="fas fa-chart-line"></i> Tổng quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/products.php">
                                <i class="fas fa-box"></i> Sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/categories.php">
                                <i class="fas fa-list"></i> Danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/brands.php">
                                <i class="fas fa-tag"></i> Thương hiệu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/sizes.php">
                                <i class="fas fa-ruler"></i> Kích cỡ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/promotions.php">
                                <i class="fas fa-ticket-alt"></i> Khuyến mãi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/orders.php">
                                <i class="fas fa-shopping-cart"></i> Đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/customers.php">
                                <i class="fas fa-users"></i> Khách hàng
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
