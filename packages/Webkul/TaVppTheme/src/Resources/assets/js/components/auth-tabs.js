/**
 * Auth Tabs - Tab switching functionality for login/register forms
 */

document.addEventListener('DOMContentLoaded', function() {
    const authTabs = document.querySelectorAll('.auth-tab');
    const authPanels = document.querySelectorAll('.auth-panel');

    if (!authTabs.length || !authPanels.length) {
        return;
    }

    // Handle tab clicks
    authTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetPanel = this.getAttribute('data-tab');

            // Remove active class from all tabs and panels
            authTabs.forEach(t => t.classList.remove('active'));
            authPanels.forEach(p => p.classList.remove('active'));

            // Add active class to clicked tab and corresponding panel
            this.classList.add('active');
            document.getElementById(targetPanel).classList.add('active');

            // Update URL hash without scrolling
            history.replaceState(null, null, '#' + targetPanel);
        });
    });

    // Check URL hash on page load to show correct tab
    const hash = window.location.hash.substring(1);
    if (hash === 'register') {
        const registerTab = document.querySelector('[data-tab="register"]');
        if (registerTab) {
            registerTab.click();
        }
    }

    // Handle AJAX Registration
    const registerForm = document.querySelector('#register form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Clear previous errors
            form.querySelectorAll('.text-red-500').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            
            // Loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang xử lý...';
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(async response => {
                const data = await response.json();
                
                if (response.ok) {
                    // Success
                    if (window.showFlashMessage) {
                        window.showFlashMessage('success', data.message || 'Đăng ký tài khoản thành công!');
                    }
                    
                    // Reset form
                    form.reset();
                    
                    // Wait 2s then switch to login tab
                    setTimeout(() => {
                        const loginTab = document.querySelector('[data-tab="login"]');
                        if (loginTab) loginTab.click();
                    }, 2000);
                } else if (response.status === 422) {
                    // Validation Errors
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('border-red-500');
                            const errorMsg = document.createElement('p');
                            errorMsg.className = 'text-red-500';
                            errorMsg.style.fontSize = '12px';
                            errorMsg.style.marginTop = '4px';
                            errorMsg.style.gridColumn = '2';
                            errorMsg.textContent = data.errors[field][0];
                            input.closest('.control-group').appendChild(errorMsg);
                        }
                    });
                } else {
                    // Other Errors
                    if (window.showFlashMessage) {
                        window.showFlashMessage('error', data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showFlashMessage) {
                    window.showFlashMessage('error', 'Không thể kết nối tới máy chủ.');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }

    // Handle "show password" toggle with eye icon
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            if (passwordInput) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        });
    });
});
