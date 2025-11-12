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
    const isActive = element.classList.contains('active');
    
    if (isActive) {
        element.classList.remove('active');
        icon.classList.remove('fas', 'fa-heart');
        icon.classList.add('far', 'fa-heart');
    } else {
        element.classList.add('active');
        icon.classList.remove('far', 'fa-heart');
        icon.classList.add('fas', 'fa-heart');
    }
    
    // Here you can add AJAX call to save favorite to database
    // For now, it's just a visual toggle
}
