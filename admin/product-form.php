<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Thêm/Sửa sản phẩm';
$editing = false;
$product = null;

if (isset($_GET['id'])) {
    $editing = true;
    $stmt = $db_conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
    
    if (!$product) {
        redirect('/admin/products.php');
    }
}

$categories = $db_conn->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$subcategories = $db_conn->query("SELECT * FROM subcategories ORDER BY name")->fetchAll();
$brands = $db_conn->query("SELECT * FROM brands ORDER BY name")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $id_category = $_POST['id_category'];
    $id_subcategory = $_POST['id_subcategory'] ?: null;
    $id_brand = $_POST['id_brand'];
    $model = sanitize($_POST['model']);
    $code = sanitize($_POST['code']);
    $price = $_POST['price'];
    $description = sanitize($_POST['description']);
    $details = $_POST['details'];
    $stock = $_POST['stock'];
    $visibility = isset($_POST['visibility']) ? 1 : 0;
    $img = $_POST['img'];
    
    if (empty($name) || empty($price)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } else {
        if ($editing) {
            $stmt = $db_conn->prepare("
                UPDATE products SET
                    name = ?, id_category = ?, id_subcategory = ?, id_brand = ?,
                    model = ?, code = ?, price = ?, description = ?, details = ?,
                    stock = ?, visibility = ?, img = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $id_category, $id_subcategory, $id_brand, $model, $code, $price, $description, $details, $stock, $visibility, $img, $_GET['id']]);
        } else {
            $stmt = $db_conn->prepare("
                INSERT INTO products (name, id_category, id_subcategory, id_brand, model, code, price, description, details, stock, visibility, img)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $id_category, $id_subcategory, $id_brand, $model, $code, $price, $description, $details, $stock, $visibility, $img]);
        }
        
        redirect('/admin/products.php?success=1');
    }
}

include 'header.php';
?>

<h2 class="mb-4"><?php echo $editing ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới'; ?></h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Mã sản phẩm</label>
                        <input type="text" name="code" class="form-control" value="<?php echo htmlspecialchars($product['code'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($product['model'] ?? ''); ?>">
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="id_category" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($product['id_category'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Danh mục con</label>
                        <select name="id_subcategory" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($subcategories as $sub): ?>
                                <option value="<?php echo $sub['id']; ?>" <?php echo ($product['id_subcategory'] ?? '') == $sub['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sub['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Thương hiệu</label>
                        <select name="id_brand" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['id']; ?>" <?php echo ($product['id_brand'] ?? '') == $brand['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Giá <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" required value="<?php echo $product['price'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tồn kho</label>
                        <input type="number" name="stock" class="form-control" value="<?php echo $product['stock'] ?? 0; ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Hiển thị</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="visibility" <?php echo ($product['visibility'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Hiển thị trên website</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">URL hình ảnh</label>
                <input type="text" name="img" class="form-control" value="<?php echo htmlspecialchars($product['img'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mô tả ngắn</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Chi tiết (HTML)</label>
                <textarea name="details" class="form-control" rows="4"><?php echo htmlspecialchars($product['details'] ?? ''); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="/admin/products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
