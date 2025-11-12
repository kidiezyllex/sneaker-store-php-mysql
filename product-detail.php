<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$product_id = $_GET['id'] ?? 0;

$stmt = $db_conn->prepare("
    SELECT p.*, b.name as brand_name, c.name as category_name, sc.name as subcategory_name
    FROM products p
    LEFT JOIN brands b ON p.id_brand = b.id
    LEFT JOIN categories c ON p.id_category = c.id
    LEFT JOIN subcategories sc ON p.id_subcategory = sc.id
    WHERE p.id = ? AND p.visibility = 1
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: /products.php');
    exit();
}

$sizes_stmt = $db_conn->prepare("
    SELECT s.*, ps.quantity
    FROM product_sizes ps
    JOIN sizes s ON ps.id_size = s.id
    WHERE ps.id_product = ? AND ps.quantity > 0
    ORDER BY s.size
");
$sizes_stmt->execute([$product_id]);
$sizes = $sizes_stmt->fetchAll();

$reviews_stmt = $db_conn->prepare("
    SELECT r.*, u.full_name
    FROM reviews r
    JOIN users u ON r.id_user = u.id
    WHERE r.id_product = ?
    ORDER BY r.created_at DESC
");
$reviews_stmt->execute([$product_id]);
$reviews = $reviews_stmt->fetchAll();

$avg_rating_stmt = $db_conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE id_product = ?");
$avg_rating_stmt->execute([$product_id]);
$rating_data = $avg_rating_stmt->fetch();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'];

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
    
    if (is_admin()) {
        $error = 'Tài khoản admin không thể mua hàng';
    } else {
        $size_id = $_POST['size'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$size_id) {
            $error = 'Vui lòng chọn size';
        } else {
            $check_stmt = $db_conn->prepare("SELECT * FROM cart WHERE id_user = ? AND id_product = ? AND id_size = ?");
            $check_stmt->execute([$_SESSION['user_id'], $product_id, $size_id]);
            $existing = $check_stmt->fetch();
            
            if ($existing) {
                $update_stmt = $db_conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
                $update_stmt->execute([$quantity, $existing['id']]);
            } else {
                $insert_stmt = $db_conn->prepare("INSERT INTO cart (id_user, id_product, id_size, quantity) VALUES (?, ?, ?, ?)");
                $insert_stmt->execute([$_SESSION['user_id'], $product_id, $size_id, $quantity]);
            }
            
            $message = 'Đã thêm vào giỏ hàng thành công!';
        }
    }
}

$page_title = $product['name'];
include 'includes/header.php';
?>

<div class="container">
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $message; ?>
            <a href="/cart.php" class="btn btn-sm btn-success ms-2">Xem giỏ hàng</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-5">
            <img src="<?php echo htmlspecialchars($product['img']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($product['brand_name']); ?> - <?php echo htmlspecialchars($product['model']); ?></p>
            
            <div class="mb-3">
                <span class="star-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?php echo $i <= $avg_rating ? '' : '-o'; ?>"></i>
                    <?php endfor; ?>
                </span>
                <span class="text-muted ms-2">(<?php echo $total_reviews; ?> đánh giá)</span>
            </div>
            
            <h3 class="price mb-3"><?php echo format_price($product['price']); ?></h3>
            
            <div class="mb-3">
                <h5>Mô tả:</h5>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <?php if ($product['details']): ?>
                <div class="mb-3">
                    <h5>Chi tiết:</h5>
                    <div><?php echo $product['details']; ?></div>
                </div>
            <?php endif; ?>
            
            <?php if (!is_admin()): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><strong>Chọn size:</strong></label>
                        <div class="btn-group d-flex flex-wrap" role="group">
                            <?php foreach ($sizes as $size): ?>
                                <input type="radio" class="btn-check" name="size" id="size<?php echo $size['id']; ?>" value="<?php echo $size['id']; ?>" required>
                                <label class="btn btn-outline-primary m-1" for="size<?php echo $size['id']; ?>">
                                    <?php echo htmlspecialchars($size['size']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Số lượng:</strong></label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="10" style="width: 100px;">
                    </div>
                    
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg" style="font-size: 16px;">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <hr class="my-5">
    
    <div class="row mt-4">
        <div class="col-md-12">
            <h4 class="mb-4">Đánh giá sản phẩm (<?php echo $total_reviews; ?>)</h4>
            
            <?php if (empty($reviews)): ?>
                <p class="text-muted">Chưa có đánh giá nào</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6><?php echo htmlspecialchars($review['full_name']); ?></h6>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                            </div>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-o'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
