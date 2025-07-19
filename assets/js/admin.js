// Admin Panel JavaScript Functions

// Global variables
let currentUser = null;
let adminToken = null;

// Initialize admin panel
document.addEventListener('DOMContentLoaded', function() {
    initializeAdmin();
    setupEventListeners();
});

function initializeAdmin() {
    // Check authentication
    checkAuth();
    
    // Setup sidebar toggle
    setupSidebarToggle();
    
    // Setup user info
    setupUserInfo();
}

function setupEventListeners() {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Auto-hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-success')) {
                alert.style.display = 'none';
            }
        });
    }, 5000);
}

function checkAuth() {
    adminToken = localStorage.getItem('admin_token');
    currentUser = JSON.parse(localStorage.getItem('admin_user') || 'null');
    
    if (!adminToken || !currentUser) {
        if (window.location.pathname !== '/admin/login.php' && !window.location.pathname.includes('login.php')) {
            window.location.href = 'login.php';
            return;
        }
    }
    
    // Verify token with server
    if (adminToken) {
        verifyToken();
    }
}

async function verifyToken() {
    try {
        const response = await fetch('../api/controllers/AuthController.php?action=me', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Token verification failed');
        }
        
        const data = await response.json();
        if (!data.success) {
            throw new Error('Invalid token');
        }
        
        currentUser = data.user;
        localStorage.setItem('admin_user', JSON.stringify(currentUser));
        
    } catch (error) {
        console.error('Token verification failed:', error);
        logout();
    }
}

function setupSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    // Check if elements exist
    if (!sidebar || !mainContent) return;
    
    // Handle responsive behavior
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebar && mainContent) {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
}

function setupUserInfo() {
    if (currentUser) {
        const userFullNameElement = document.getElementById('userFullName');
        if (userFullNameElement) {
            userFullNameElement.textContent = currentUser.full_name || currentUser.username;
        }
    }
}

// API Helper Functions
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${adminToken}`
        }
    };
    
    const finalOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...(options.headers || {})
        }
    };
    
    try {
        const response = await fetch(url, finalOptions);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || `HTTP error! status: ${response.status}`);
        }
        
        return data;
    } catch (error) {
        console.error('API Request failed:', error);
        
        if (error.message.includes('401') || error.message.includes('Unauthorized')) {
            logout();
        }
        
        throw error;
    }
}

// Authentication Functions
async function logout() {
    try {
        await apiRequest('../api/controllers/AuthController.php?action=logout', {
            method: 'POST'
        });
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_user');
        window.location.href = 'login.php';
    }
}

async function changePassword() {
    const { value: formValues } = await Swal.fire({
        title: 'Şifre Değiştir',
        html: `
            <div class="mb-3">
                <label class="form-label">Mevcut Şifre</label>
                <input type="password" id="oldPassword" class="form-control" placeholder="Mevcut şifrenizi girin">
            </div>
            <div class="mb-3">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" id="newPassword" class="form-control" placeholder="Yeni şifrenizi girin">
            </div>
            <div class="mb-3">
                <label class="form-label">Yeni Şifre Tekrar</label>
                <input type="password" id="confirmPassword" class="form-control" placeholder="Yeni şifrenizi tekrar girin">
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Değiştir',
        cancelButtonText: 'İptal',
        preConfirm: () => {
            const oldPassword = document.getElementById('oldPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!oldPassword || !newPassword || !confirmPassword) {
                Swal.showValidationMessage('Tüm alanları doldurun');
                return false;
            }
            
            if (newPassword.length < 6) {
                Swal.showValidationMessage('Yeni şifre en az 6 karakter olmalıdır');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                Swal.showValidationMessage('Yeni şifreler eşleşmiyor');
                return false;
            }
            
            return { oldPassword, newPassword };
        }
    });
    
    if (formValues) {
        try {
            await apiRequest('../api/controllers/AuthController.php?action=change-password', {
                method: 'POST',
                body: JSON.stringify({
                    old_password: formValues.oldPassword,
                    new_password: formValues.newPassword
                })
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: 'Şifreniz başarıyla değiştirildi'
            });
            
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: error.message
            });
        }
    }
}

// Utility Functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(amount);
}

function formatDate(dateString) {
    return new Intl.DateTimeFormat('tr-TR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(dateString));
}

function getStatusBadge(status, type = 'order') {
    const statusConfig = {
        order: {
            pending: { class: 'status-pending', text: 'Beklemede' },
            preparing: { class: 'status-preparing', text: 'Hazırlanıyor' },
            ready: { class: 'status-ready', text: 'Hazır' },
            delivering: { class: 'status-preparing', text: 'Teslimatta' },
            completed: { class: 'status-completed', text: 'Tamamlandı' },
            cancelled: { class: 'status-cancelled', text: 'İptal' }
        },
        general: {
            active: { class: 'status-active', text: 'Aktif' },
            inactive: { class: 'status-inactive', text: 'Pasif' }
        }
    };
    
    const config = statusConfig[type][status];
    if (!config) return `<span class="badge bg-secondary">${status}</span>`;
    
    return `<span class="badge ${config.class}">${config.text}</span>`;
}

function showLoading(element) {
    if (element) {
        element.classList.add('loading');
        const spinner = document.createElement('div');
        spinner.className = 'spinner-overlay';
        spinner.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
        element.style.position = 'relative';
        element.appendChild(spinner);
    }
}

function hideLoading(element) {
    if (element) {
        element.classList.remove('loading');
        const spinner = element.querySelector('.spinner-overlay');
        if (spinner) {
            spinner.remove();
        }
    }
}

function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// File Upload Functions
function setupFileUpload(inputId, previewId, allowMultiple = false) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    if (!input || !preview) return;
    
    input.addEventListener('change', function(e) {
        const files = e.target.files;
        preview.innerHTML = '';
        
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail me-2 mb-2';
                    img.style.maxWidth = '100px';
                    img.style.maxHeight = '100px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

// Data Table Functions
function initializeDataTable(tableId, ajaxUrl, columns) {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        return $(`#${tableId}`).DataTable({
            ajax: {
                url: ajaxUrl,
                headers: {
                    'Authorization': `Bearer ${adminToken}`
                }
            },
            columns: columns,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
            },
            order: [[0, 'desc']]
        });
    }
}

// Export functions to global scope
window.logout = logout;
window.changePassword = changePassword;
window.apiRequest = apiRequest;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.getStatusBadge = getStatusBadge;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showAlert = showAlert;
window.setupFileUpload = setupFileUpload;
window.initializeDataTable = initializeDataTable;
window.checkAuth = checkAuth;