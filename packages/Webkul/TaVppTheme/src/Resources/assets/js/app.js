/**
 * This will track all the images and fonts for publishing.
 */
import.meta.glob(["../images/**", "../fonts/**"]);

/**
 * Import component scripts
 */
import './components/auth-tabs.js';
import './components/cart.js';
import './components/checkout.js';

/**
 * Carousel scroll function
 */
window.scrollCarousel = function(btn, direction) {
    const carousel = btn.parentElement.querySelector('.carousel');
    const scrollAmount = carousel.offsetWidth * 0.8;
    carousel.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
};

/**
 * Add to Cart functionality using fetch API
 */
document.addEventListener('DOMContentLoaded', function() {
    // Handle all add-to-cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = this.querySelector('.btn-add-cart');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang thêm...';
            
            // Get form data
            const formData = new FormData(this);
            
            // Send request
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Show success message
                    showFlashMessage('success', data.message);
                    
                    // Update cart count if element exists
                    const cartBadge = document.querySelector('.badge-count');
                    if (cartBadge && data.data && data.data.items_qty) {
                        cartBadge.textContent = data.data.items_qty;
                    }
                } else if (data.data && data.data.message) {
                    showFlashMessage('warning', data.data.message);
                }
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('error', 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    });
});

/**
 * Show flash message
 */
window.showFlashMessage = function(type, message) {
    const colors = {
        success: { bg: '#d4edda', text: '#155724', border: '#c3e6cb' },
        error: { bg: '#f8d7da', text: '#721c24', border: '#f5c6cb' },
        warning: { bg: '#fff3cd', text: '#856404', border: '#ffeeba' },
        info: { bg: '#d1ecf1', text: '#0c5460', border: '#bee5eb' }
    };
    
    const color = colors[type] || colors.success;
    
    const flash = document.createElement('div');
    flash.className = 'vpp-toast';
    flash.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${color.bg};
        color: ${color.text};
        padding: 15px 25px;
        border-radius: 8px;
        border: 1px solid ${color.border};
        z-index: 10000;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease-out;
        font-weight: 500;
    `;
    flash.textContent = message;
    
    document.body.appendChild(flash);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        flash.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => flash.remove(), 300);
    }, 3000);
};

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
