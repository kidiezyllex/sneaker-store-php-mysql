<?php
require_once 'includes/init.php';
require_login();

if (is_admin()) {
    redirect('/admin/index.php');
}

$page_title = 'Sản phẩm yêu thích';

$controller = new WishlistController($db_conn);
$wishlistItems = $controller->index((int) $_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h3 class="mb-0">Sản phẩm yêu thích</h3>
            <p class="text-muted mb-0">Lưu trữ nhanh các sản phẩm bạn quan tâm.</p>
        </div>
        <a class="btn btn-outline-primary" href="/products.php"><i class="fas fa-th"></i> Tiếp tục mua sắm</a>
    </div>

    <?php if (empty($wishlistItems)): ?>
        <div class="alert alert-info">
            <i class="fas fa-heart"></i> Bạn chưa lưu sản phẩm nào. Hãy nhấn biểu tượng trái tim để thêm vào yêu thích.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="col-md-4">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($item['img']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($item['model']); ?></h6>
                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="mb-2"><?php echo htmlspecialchars($item['brand_name']); ?></p>
                            <p class="price mb-3"><?php echo format_price($item['price']); ?></p>
                            <div class="d-flex justify-content-between">
                                <a href="/product-detail.php?id=<?php echo $item['id_product']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                                <a href="#" class="btn btn-outline-danger product-card__favorite active" data-product-id="<?php echo $item['id_product']; ?>" onclick="toggleFavorite(this, event); return false;">
                                    <i class="fas fa-heart"></i> Bỏ thích
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

