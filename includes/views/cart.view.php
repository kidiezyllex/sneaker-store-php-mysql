<div class="container">
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> Giỏ hàng của bạn đang trống
        </div>
        <a href="/products.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
    <?php else: ?>
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Size</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['img']); ?>" class="cart-item-image me-3" alt="">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($item['brand_name']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo format_price($item['price']); ?></td>
                                        <td><?php echo htmlspecialchars($item['size']); ?></td>
                                        <td>
                                            <input type="number" class="form-control quantity-input" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="10"
                                                   onchange="updateCartQuantity(<?php echo $item['id']; ?>, this.value)">
                                        </td>
                                        <td><strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Tổng đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <strong><?php echo format_price($total); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <strong>Miễn phí</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Tổng cộng:</h5>
                            <h5 class="text-danger"><?php echo format_price($total); ?></h5>
                        </div>
                        <a href="checkout.php" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-check"></i> Thanh toán
                        </a>
                        <a href="/products.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

