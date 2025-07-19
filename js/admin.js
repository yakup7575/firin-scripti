// Admin Panel JavaScript - admin.js

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminPanel();
});

function initializeAdminPanel() {
    // Check authentication
    if (!isAuthenticated()) {
        window.location.href = 'login.html';
        return;
    }

    // Load user info
    loadUserInfo();
    
    // Initialize dashboard data
    loadDashboardData();
    
    // Setup navigation
    setupNavigation();
}

function isAuthenticated() {
    const session = localStorage.getItem('adminSession') || sessionStorage.getItem('adminSession');
    
    if (!session) return false;

    try {
        const sessionData = JSON.parse(session);
        const now = new Date();
        const expiry = new Date(sessionData.expiryTime);
        
        return now < expiry;
    } catch {
        return false;
    }
}

function loadUserInfo() {
    const session = localStorage.getItem('adminSession') || sessionStorage.getItem('adminSession');
    
    if (session) {
        try {
            const sessionData = JSON.parse(session);
            const usernameElement = document.getElementById('admin-username');
            if (usernameElement) {
                usernameElement.textContent = sessionData.username;
            }
        } catch (error) {
            console.error('Error loading user info:', error);
        }
    }
}

function loadDashboardData() {
    // Load recent orders
    loadRecentOrders();
    
    // Load stock alerts
    loadStockAlerts();
    
    // Update statistics
    updateStatistics();
}

function loadRecentOrders() {
    const ordersTable = document.getElementById('recent-orders');
    if (!ordersTable) return;

    // Get orders from localStorage
    const allOrders = JSON.parse(localStorage.getItem('orders')) || [];
    const recentOrders = allOrders.slice(0, 10); // Get last 10 orders

    if (recentOrders.length === 0) {
        ordersTable.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-light);">
                    Henüz sipariş bulunmuyor
                </td>
            </tr>
        `;
        return;
    }

    ordersTable.innerHTML = recentOrders.map(order => {
        const orderDate = new Date(order.date).toLocaleDateString('tr-TR');
        const statusBadge = getStatusBadge(order.status || 'pending');
        const orderType = order.type || 'unknown';
        const customerName = order.customerInfo?.name || 'Bilinmeyen';
        const total = order.total || order.totalPrice || 0;

        return `
            <tr>
                <td>#${order.id}</td>
                <td>${customerName}</td>
                <td>${getOrderTypeDisplay(orderType)}</td>
                <td>₺${total.toFixed(2)}</td>
                <td>${orderDate}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-outline btn-small" onclick="viewOrder(${order.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-primary btn-small" onclick="updateOrderStatus(${order.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusBadge(status) {
    const statusMap = {
        'pending': { class: 'pending', text: 'Bekliyor' },
        'processing': { class: 'pending', text: 'İşleniyor' },
        'completed': { class: 'completed', text: 'Tamamlandı' },
        'cancelled': { class: 'cancelled', text: 'İptal' }
    };

    const statusInfo = statusMap[status] || { class: 'pending', text: 'Bekliyor' };
    return `<span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>`;
}

function getOrderTypeDisplay(type) {
    const typeMap = {
        'cake': 'Pasta Siparişi',
        'bread': 'Ekmek',
        'simit': 'Simit',
        'unknown': 'Diğer'
    };

    return typeMap[type] || 'Diğer';
}

function loadStockAlerts() {
    const stockAlertsContainer = document.getElementById('stock-alerts');
    if (!stockAlertsContainer) return;

    // Simulate low stock items
    const lowStockItems = [
        { name: 'Tam Buğday Ekmeği', current: 5, minimum: 10 },
        { name: 'Çörek Otu Simidi', current: 3, minimum: 8 },
        { name: 'Pasta Malzemeleri', current: 2, minimum: 5 }
    ];

    if (lowStockItems.length === 0) {
        stockAlertsContainer.innerHTML = `
            <div class="stock-alert" style="background: #d4edda; border-color: #c3e6cb;">
                <div class="alert-info">
                    <div class="alert-icon" style="color: #155724;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-text">
                        <h4 style="color: #155724;">Tüm Ürünler Yeterli Stokta</h4>
                        <p style="color: #0f5132;">Şu anda düşük stoklu ürün bulunmuyor.</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    stockAlertsContainer.innerHTML = lowStockItems.map(item => `
        <div class="stock-alert">
            <div class="alert-info">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-text">
                    <h4>${item.name}</h4>
                    <p>Stok: ${item.current} / Minimum: ${item.minimum}</p>
                </div>
            </div>
            <button class="btn btn-outline btn-small" onclick="restockItem('${item.name}')">
                <i class="fas fa-plus"></i> Stok Ekle
            </button>
        </div>
    `).join('');
}

function updateStatistics() {
    // Get orders for statistics
    const allOrders = JSON.parse(localStorage.getItem('orders')) || [];
    const today = new Date().toDateString();
    const todayOrders = allOrders.filter(order => 
        new Date(order.date).toDateString() === today
    );

    // Update daily sales
    const dailySales = todayOrders.reduce((total, order) => 
        total + (order.total || order.totalPrice || 0), 0
    );
    updateElement('daily-sales', `₺${dailySales.toFixed(0)}`);

    // Update daily orders
    updateElement('daily-orders', todayOrders.length.toString());

    // Update pending orders
    const pendingOrders = allOrders.filter(order => 
        (order.status || 'pending') === 'pending'
    ).length;
    updateElement('pending-orders', pendingOrders.toString());

    // Update total products (simulated)
    const products = JSON.parse(localStorage.getItem('products')) || {};
    const totalProducts = Object.values(products).reduce((total, category) => 
        total + (Array.isArray(category) ? category.length : 0), 0
    );
    updateElement('total-products', totalProducts.toString());
}

function updateElement(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function setupNavigation() {
    // Setup navigation active states
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link-admin');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    });
}

// Dashboard Actions
function refreshData() {
    showLoading();
    
    setTimeout(() => {
        loadDashboardData();
        hideLoading();
        showSuccessMessage('Veriler yenilendi!');
    }, 1000);
}

function showQuickActions() {
    const modal = document.getElementById('quick-actions-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeQuickActions() {
    const modal = document.getElementById('quick-actions-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

// Quick Actions
function addProduct() {
    closeQuickActions();
    window.location.href = 'urunler.html#add';
}

function viewOrders() {
    closeQuickActions();
    window.location.href = 'siparisler.html';
}

function updatePrices() {
    closeQuickActions();
    showNotification('Fiyat güncelleme özelliği geliştiriliyor...', 'info');
}

function generateReport() {
    closeQuickActions();
    showNotification('Rapor oluşturma özelliği geliştiriliyor...', 'info');
}

// Order Management
function viewOrder(orderId) {
    const orders = JSON.parse(localStorage.getItem('orders')) || [];
    const order = orders.find(o => o.id == orderId);
    
    if (order) {
        showOrderDetails(order);
    } else {
        showErrorMessage('Sipariş bulunamadı!');
    }
}

function showOrderDetails(order) {
    const modal = createOrderModal(order);
    document.body.appendChild(modal);
}

function createOrderModal(order) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    
    const orderDate = new Date(order.date).toLocaleString('tr-TR');
    const customerInfo = order.customerInfo || {};
    const cakeDetails = order.cakeDetails || {};
    const items = order.items || [];

    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Sipariş Detayları - #${order.id}</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="order-info">
                    <h4>Müşteri Bilgileri</h4>
                    <p><strong>Ad Soyad:</strong> ${customerInfo.name || 'Belirtilmemiş'}</p>
                    <p><strong>Telefon:</strong> ${customerInfo.phone || 'Belirtilmemiş'}</p>
                    <p><strong>E-posta:</strong> ${customerInfo.email || 'Belirtilmemiş'}</p>
                    
                    <h4>Sipariş Bilgileri</h4>
                    <p><strong>Tarih:</strong> ${orderDate}</p>
                    <p><strong>Durum:</strong> ${getStatusBadge(order.status || 'pending')}</p>
                    <p><strong>Toplam:</strong> ₺${(order.total || order.totalPrice || 0).toFixed(2)}</p>
                    
                    ${order.type === 'cake' ? `
                        <h4>Pasta Detayları</h4>
                        <p><strong>Kategori:</strong> ${cakeDetails.category || 'Belirtilmemiş'}</p>
                        <p><strong>Boyut:</strong> ${cakeDetails.size || 'Belirtilmemiş'}</p>
                        <p><strong>Tat:</strong> ${cakeDetails.flavor || 'Belirtilmemiş'}</p>
                        <p><strong>Mesaj:</strong> ${cakeDetails.message || 'Yok'}</p>
                        <p><strong>Teslim Tarihi:</strong> ${order.deliveryDate || 'Belirtilmemiş'}</p>
                    ` : items.length > 0 ? `
                        <h4>Ürünler</h4>
                        <ul>
                            ${items.map(item => `
                                <li>${item.name} - ${item.quantity} adet - ₺${(item.price * item.quantity).toFixed(2)}</li>
                            `).join('')}
                        </ul>
                    ` : ''}
                </div>
                <div class="order-actions">
                    <button class="btn btn-primary" onclick="updateOrderStatus(${order.id})">
                        Durumu Güncelle
                    </button>
                    <button class="btn btn-outline" onclick="printOrder(${order.id})">
                        <i class="fas fa-print"></i> Yazdır
                    </button>
                </div>
            </div>
        </div>
    `;

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });

    return modal;
}

function updateOrderStatus(orderId) {
    const newStatus = prompt('Yeni durum seçin:\n1 - Bekliyor\n2 - İşleniyor\n3 - Tamamlandı\n4 - İptal');
    
    if (!newStatus) return;
    
    const statusMap = {
        '1': 'pending',
        '2': 'processing',
        '3': 'completed',
        '4': 'cancelled'
    };
    
    const status = statusMap[newStatus];
    if (!status) {
        showErrorMessage('Geçersiz durum seçimi!');
        return;
    }
    
    // Update order status
    const orders = JSON.parse(localStorage.getItem('orders')) || [];
    const orderIndex = orders.findIndex(o => o.id == orderId);
    
    if (orderIndex !== -1) {
        orders[orderIndex].status = status;
        localStorage.setItem('orders', JSON.stringify(orders));
        
        loadRecentOrders();
        updateStatistics();
        showSuccessMessage('Sipariş durumu güncellendi!');
    } else {
        showErrorMessage('Sipariş bulunamadı!');
    }
}

function printOrder(orderId) {
    showNotification('Yazdırma özelliği geliştiriliyor...', 'info');
}

// Stock Management
function checkAllStock() {
    showLoading();
    
    setTimeout(() => {
        loadStockAlerts();
        hideLoading();
        showSuccessMessage('Stok durumu kontrol edildi!');
    }, 1000);
}

function restockItem(itemName) {
    const quantity = prompt(`${itemName} için eklenecek miktar:`);
    
    if (quantity && !isNaN(quantity) && parseInt(quantity) > 0) {
        showSuccessMessage(`${itemName} için ${quantity} adet stok eklendi!`);
        loadStockAlerts();
    } else if (quantity !== null) {
        showErrorMessage('Geçerli bir miktar girin!');
    }
}

// Authentication
function logout() {
    if (confirm('Çıkış yapmak istediğinizden emin misiniz?')) {
        // Clear session data
        localStorage.removeItem('adminSession');
        sessionStorage.removeItem('adminSession');
        
        // Redirect to login
        window.location.href = 'login.html';
    }
}

// Utility Functions
function showLoading() {
    let loadingOverlay = document.querySelector('.loading-overlay');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="spinner"></div>
            <p>İşlem yapılıyor...</p>
        `;
        document.body.appendChild(loadingOverlay);
    }
    loadingOverlay.style.display = 'flex';
}

function hideLoading() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

function showSuccessMessage(message) {
    showNotification(message, 'success');
}

function showErrorMessage(message) {
    showNotification(message, 'error');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const colors = {
        success: { bg: '#28a745', icon: 'check-circle' },
        error: { bg: '#dc3545', icon: 'exclamation-circle' },
        info: { bg: '#17a2b8', icon: 'info-circle' },
        warning: { bg: '#ffc107', icon: 'exclamation-triangle' }
    };
    
    const color = colors[type] || colors.info;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${color.bg};
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 350px;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    `;
    
    notification.innerHTML = `
        <i class="fas fa-${color.icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto refresh data every 5 minutes
setInterval(() => {
    if (isAuthenticated()) {
        loadDashboardData();
    }
}, 5 * 60 * 1000);

// CSS Animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .order-info h4 {
        color: var(--primary-color);
        margin: 1.5rem 0 1rem 0;
        border-bottom: 1px solid var(--cream-color);
        padding-bottom: 0.5rem;
    }
    
    .order-info h4:first-child {
        margin-top: 0;
    }
    
    .order-info p {
        margin: 0.5rem 0;
        color: var(--text-dark);
    }
    
    .order-info ul {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }
    
    .order-info li {
        margin: 0.5rem 0;
        color: var(--text-dark);
    }
    
    .order-actions {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--cream-color);
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
`;
document.head.appendChild(style);