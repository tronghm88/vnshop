document.addEventListener('DOMContentLoaded', function () {
    /**
     * Update quantity
     */
    const updateQty = (itemId, qty) => {
        const data = {
            qty: {}
        };
        data.qty[itemId] = qty;

        fetch('/api/checkout/cart', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(res => {
                if (res.message) {
                    // Refresh the page to show updated totals and prices
                    // In a more advanced implementation, we would update the DOM manually
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('error', 'Có lỗi xảy ra khi cập nhật số lượng');
            });
    };

    /**
     * Handle quantity buttons
     */
    document.querySelectorAll('.qty-up, .qty-down').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.cart-item-card');
            const itemId = card.dataset.id;
            const input = card.querySelector('input[type="number"]');
            let qty = parseInt(input.value);

            if (this.classList.contains('qty-up')) {
                qty++;
            } else if (qty > 1) {
                qty--;
            }

            input.value = qty;
            updateQty(itemId, qty);
        });
    });

    /**
     * Handle delete buttons
     */
    document.querySelectorAll('.cart-item-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                return;
            }

            const card = this.closest('.cart-item-card');
            const itemId = card.dataset.id;

            fetch('/api/checkout/cart', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ cart_item_id: itemId })
            })
                .then(response => response.json())
                .then(res => {
                    if (res.message) {
                        showFlashMessage('success', res.message);
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFlashMessage('error', 'Có lỗi xảy ra khi xóa sản phẩm');
                });
        });
    });

    /**
     * Handle Coupon application
     */
    const couponForm = document.getElementById('coupon-form');
    if (couponForm) {
        couponForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const code = formData.get('code');

            if (!code) return;

            fetch('/api/checkout/cart/coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code: code })
            })
                .then(response => response.json())
                .then(res => {
                    if (res.message) {
                        showFlashMessage('success', res.message);
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFlashMessage('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn');
                });
        });

        const removeCouponBtn = document.getElementById('remove-coupon');
        if (removeCouponBtn) {
            removeCouponBtn.addEventListener('click', function () {
                fetch('/api/checkout/cart/coupon', {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(res => {
                        if (res.message) {
                            showFlashMessage('success', res.message);
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showFlashMessage('error', 'Có lỗi xảy ra khi xóa mã giảm giá');
                    });
            });
        }
    }
});
