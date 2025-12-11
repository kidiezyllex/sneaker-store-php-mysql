function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) {
        if (!confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            return;
        }
    }
    
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    formData.append('action', 'update');
    
    fetch('/cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}

function removeFromCart(cartId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('action', 'remove');
    
    fetch('/cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}

function confirmDelete(message) {
    return confirm(message || 'Bạn có chắc muốn xóa?');
}

function toggleFavorite(element, event) {
    event.preventDefault();
    const icon = element.querySelector('i');
    const productId = element.getAttribute('data-product-id');

    if (!productId) {
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);

    fetch('/wishlist-toggle.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async (response) => {
            if (response.status === 401) {
                window.location.href = '/login.php';
                return null;
            }
            return response.json();
        })
        .then((data) => {
            if (!data) return;
            if (!data.success) {
                alert(data.message || 'Không thể cập nhật yêu thích');
                return;
            }

            if (data.favorited) {
                element.classList.add('active');
                icon.classList.remove('far', 'fa-heart');
                icon.classList.add('fas', 'fa-heart');
            } else {
                element.classList.remove('active');
                icon.classList.remove('fas', 'fa-heart');
                icon.classList.add('far', 'fa-heart');
            }
        })
        .catch(() => {
            alert('Không thể cập nhật yêu thích');
        });
}
