<div class="container">
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Bộ lọc</h5>
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Thương hiệu</label>
                        <select name="brand" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?php echo $b['id']; ?>" <?php echo $brand == $b['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Giá từ</label>
                        <input type="number" name="min_price" class="form-control" placeholder="0" value="<?php echo htmlspecialchars($min_price); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Giá đến</label>
                        <input type="number" name="max_price" class="form-control" placeholder="10000000" value="<?php echo htmlspecialchars($max_price); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                    <a href="/products.php" class="btn btn-outline-secondary w-100 mt-2"><i class="fas fa-redo"></i> Đặt lại</a>
                </form>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="mb-3">
                <p class="text-muted">Tìm thấy <?php echo $total_products; ?> sản phẩm</p>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i> Không tìm thấy sản phẩm nào
                </div>
            <?php else: ?>
                <div class="row mt-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card product-card">
                                <img src="<?php echo htmlspecialchars($product['img']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($product['model']); ?></h6>
                                    <p class="text-muted small mb-1"><?php echo htmlspecialchars($product['name']); ?></p>
                                    <?php 
                                    $ratingData = $product_ratings[$product['id']] ?? ['avg_rating' => 0, 'total_reviews' => 0];
                                    $rating = (float) $ratingData['avg_rating'];
                                    $fullStars = (int) floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                    ?>
                                    <div class="product-card__rating mb-2">
                                        <div class="product-card__stars">
                                            <?php for ($i = 0; $i < $fullStars; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                            <?php if ($hasHalfStar): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php endif; ?>
                                            <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                                                <i class="far fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="product-card__rating-text">
                                            (<?php echo number_format($rating, 1); ?><?php echo $ratingData['total_reviews'] ? ' / ' . (int) $ratingData['total_reviews'] . ' đánh giá' : ''; ?>)
                                        </span>
                                    </div>
                                    <p class="price mb-2"><?php echo format_price($product['price']); ?></p>
                                    <div class="product-card__actions">
                                        <a href="/product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-lg" style="font-size: 16px;">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                        <?php $isFavorite = in_array($product['id'], $wishlist_product_ids ?? []); ?>
                                        <a href="#" class="product-card__favorite <?php echo $isFavorite ? 'active' : ''; ?>" data-product-id="<?php echo $product['id']; ?>" onclick="toggleFavorite(this, event); return false;" title="Thêm vào yêu thích">
                                            <i class="<?php echo $isFavorite ? 'fas fa-heart' : 'far fa-heart'; ?>"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&brand=<?php echo urlencode($brand); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>