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
    <?php if ($reviewMessage): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $reviewMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($reviewError): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <?php echo $reviewError; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-5">
            <img src="<?php echo htmlspecialchars($product['img']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($product['brand_name']); ?> - <?php echo htmlspecialchars($product['model']); ?></p>
            <?php $isFavorite = in_array($product_id, $wishlist_product_ids ?? []); ?>
            
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
                    
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg" style="font-size: 16px;">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                        <a href="#" class="btn btn-outline-danger <?php echo $isFavorite ? 'active' : ''; ?>" data-product-id="<?php echo $product_id; ?>" onclick="toggleFavorite(this, event); return false;">
                            <i class="<?php echo $isFavorite ? 'fas fa-heart' : 'far fa-heart'; ?>"></i> Yêu thích
                        </a>
                    </div>
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

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="mb-3">Viết đánh giá của bạn</h5>
                    <?php if (is_logged_in() && !is_admin()): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Chọn số sao</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">-- Chọn --</option>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> sao</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bình luận</label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                            </div>
                            <button type="submit" name="add_review" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Gửi đánh giá
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted">Vui lòng <a href="/login.php">đăng nhập</a> bằng tài khoản khách hàng để đánh giá sản phẩm.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>