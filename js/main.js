// Fırın Pastane Main JavaScript
class FirinPastaneApp {
    constructor() {
        this.currentUser = null;
        this.initializeData();
        this.bindEvents();
        this.loadProducts();
        this.checkAuthState();
    }

    // Initialize default data structure
    initializeData() {
        // Initialize localStorage structure if not exists
        if (!localStorage.getItem('firinData')) {
            const defaultData = {
                products: {
                    ekmek: [
                        { id: 1, name: 'Beyaz Ekmek', price: 3.50, stock: 50, category: 'ekmek' },
                        { id: 2, name: 'Tam Buğday Ekmeği', price: 4.00, stock: 30, category: 'ekmek' },
                        { id: 3, name: 'Çavdar Ekmeği', price: 4.50, stock: 25, category: 'ekmek' },
                        { id: 4, name: 'Köy Ekmeği', price: 5.00, stock: 20, category: 'ekmek' }
                    ],
                    simit: [
                        { id: 5, name: 'İstanbul Simidi', price: 2.50, stock: 100, category: 'simit' },
                        { id: 6, name: 'Susam Simidi', price: 3.00, stock: 80, category: 'simit' },
                        { id: 7, name: 'Çay Simidi', price: 2.00, stock: 120, category: 'simit' },
                        { id: 8, name: 'Peynirli Simit', price: 4.00, stock: 40, category: 'simit' }
                    ],
                    pasta: [
                        { id: 9, name: 'Doğum Günü Pastası', price: 45.00, stock: 10, category: 'pasta' },
                        { id: 10, name: 'Çikolatalı Pasta', price: 35.00, stock: 15, category: 'pasta' },
                        { id: 11, name: 'Muzlu Pasta', price: 30.00, stock: 12, category: 'pasta' },
                        { id: 12, name: 'Cheesecake', price: 40.00, stock: 8, category: 'pasta' }
                    ]
                },
                orders: [],
                customers: [],
                users: [
                    { username: 'admin', password: 'admin123', role: 'admin', name: 'Admin User' }
                ],
                settings: {
                    shopName: 'Fırın Pastane',
                    currency: 'TL',
                    taxRate: 0.18
                }
            };
            localStorage.setItem('firinData', JSON.stringify(defaultData));
        }

        // Initialize admin data structure if not exists
        if (!localStorage.getItem('adminData')) {
            const adminData = {
                users: [
                    { username: 'admin', password: 'admin123', role: 'admin', name: 'Admin User' }
                ],
                products: [],
                orders: [],
                customers: [],
                settings: {}
            };
            localStorage.setItem('adminData', JSON.stringify(adminData));
        }
    }

    // Get data from localStorage
    getData() {
        return JSON.parse(localStorage.getItem('firinData'));
    }

    // Save data to localStorage
    saveData(data) {
        localStorage.setItem('firinData', JSON.stringify(data));
    }

    // Get admin data
    getAdminData() {
        return JSON.parse(localStorage.getItem('adminData'));
    }

    // Save admin data
    saveAdminData(data) {
        localStorage.setItem('adminData', JSON.stringify(data));
    }

    // Bind event listeners
    bindEvents() {
        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        
        if (hamburger) {
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
            });
        }

        // Login modal
        const loginBtn = document.getElementById('loginBtn');
        const loginModal = document.getElementById('loginModal');
        const closeModal = document.querySelector('.close');
        const loginForm = document.getElementById('loginForm');

        if (loginBtn) {
            loginBtn.addEventListener('click', () => {
                if (this.currentUser) {
                    this.logout();
                } else {
                    loginModal.style.display = 'block';
                }
            });
        }

        if (closeModal) {
            closeModal.addEventListener('click', () => {
                loginModal.style.display = 'none';
            });
        }

        if (loginModal) {
            window.addEventListener('click', (e) => {
                if (e.target === loginModal) {
                    loginModal.style.display = 'none';
                }
            });
        }

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleLogin();
            });
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // Handle login
    handleLogin() {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        const data = this.getData();
        const user = data.users.find(u => u.username === username && u.password === password);
        
        if (user) {
            this.currentUser = user;
            localStorage.setItem('currentUser', JSON.stringify(user));
            this.updateAuthUI();
            document.getElementById('loginModal').style.display = 'none';
            document.getElementById('loginForm').reset();
            
            // Show success message
            this.showNotification('Başarıyla giriş yapıldı!', 'success');
        } else {
            this.showNotification('Kullanıcı adı veya şifre hatalı!', 'error');
        }
    }

    // Handle logout
    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        this.updateAuthUI();
        this.showNotification('Başarıyla çıkış yapıldı!', 'success');
    }

    // Check authentication state
    checkAuthState() {
        const savedUser = localStorage.getItem('currentUser');
        if (savedUser) {
            this.currentUser = JSON.parse(savedUser);
            this.updateAuthUI();
        }
    }

    // Update authentication UI
    updateAuthUI() {
        const loginBtn = document.getElementById('loginBtn');
        const adminNav = document.querySelector('.admin-nav');
        
        if (this.currentUser) {
            loginBtn.textContent = 'Çıkış';
            loginBtn.title = `${this.currentUser.name} - Çıkış Yap`;
            
            if (this.currentUser.role === 'admin' && adminNav) {
                adminNav.style.display = 'block';
            }
        } else {
            loginBtn.textContent = 'Giriş';
            loginBtn.title = 'Giriş Yap';
            
            if (adminNav) {
                adminNav.style.display = 'none';
            }
        }
    }

    // Load products to display
    loadProducts() {
        const data = this.getData();
        
        // Load ekmek products
        this.displayProducts('ekmek', data.products.ekmek);
        this.displayProducts('simit', data.products.simit);
        this.displayProducts('pasta', data.products.pasta);
    }

    // Display products in category
    displayProducts(category, products) {
        const container = document.getElementById(`${category}-products`);
        if (!container) return;
        
        container.innerHTML = '';
        
        products.slice(0, 3).forEach(product => {
            const productElement = document.createElement('div');
            productElement.className = 'product-item';
            productElement.innerHTML = `
                <span class="product-name">${product.name}</span>
                <span class="product-price">${product.price.toFixed(2)} TL</span>
            `;
            container.appendChild(productElement);
        });
    }

    // Show notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 2rem;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        
        switch (type) {
            case 'success':
                notification.style.backgroundColor = '#28a745';
                break;
            case 'error':
                notification.style.backgroundColor = '#dc3545';
                break;
            default:
                notification.style.backgroundColor = '#17a2b8';
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Add slide-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => {
                document.body.removeChild(notification);
                document.head.removeChild(style);
            }, 300);
        }, 3000);
        
        // Add slide-out animation
        style.textContent += `
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
    }

    // Generate unique ID
    generateId() {
        return Date.now() + Math.floor(Math.random() * 1000);
    }

    // Format currency
    formatCurrency(amount) {
        return `${amount.toFixed(2)} TL`;
    }

    // Format date
    formatDate(date) {
        return new Date(date).toLocaleDateString('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Admin Panel Manager
class AdminManager {
    constructor() {
        this.app = new FirinPastaneApp();
        this.initAdminPanel();
    }

    initAdminPanel() {
        // Check if user is admin
        if (!this.app.currentUser || this.app.currentUser.role !== 'admin') {
            this.redirectToMain();
            return;
        }

        this.bindAdminEvents();
        this.loadDashboardData();
    }

    redirectToMain() {
        window.location.href = '../index.html';
    }

    bindAdminEvents() {
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.admin-sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // Logout functionality
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                this.app.logout();
                this.redirectToMain();
            });
        }
    }

    loadDashboardData() {
        const data = this.app.getData();
        
        // Load stats
        this.updateStats(data);
        
        // Load recent orders
        this.loadRecentOrders(data.orders);
        
        // Load low stock items
        this.loadLowStockItems(data.products);
    }

    updateStats(data) {
        const totalProducts = Object.values(data.products).flat().length;
        const totalOrders = data.orders.length;
        const totalRevenue = data.orders.reduce((sum, order) => sum + (order.total || 0), 0);
        const lowStockItems = Object.values(data.products).flat().filter(p => p.stock < 10).length;

        // Update stat cards
        const statElements = {
            'total-products': totalProducts,
            'total-orders': totalOrders,
            'total-revenue': this.app.formatCurrency(totalRevenue),
            'low-stock': lowStockItems
        };

        Object.keys(statElements).forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = statElements[id];
            }
        });
    }

    loadRecentOrders(orders) {
        const recentOrdersContainer = document.getElementById('recent-orders');
        if (!recentOrdersContainer) return;

        const recentOrders = orders.slice(-5).reverse();
        
        if (recentOrders.length === 0) {
            recentOrdersContainer.innerHTML = '<p>Henüz sipariş bulunmuyor.</p>';
            return;
        }

        const table = document.createElement('table');
        table.className = 'table';
        table.innerHTML = `
            <thead>
                <tr>
                    <th>Sipariş ID</th>
                    <th>Müşteri</th>
                    <th>Toplam</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                ${recentOrders.map(order => `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.customerName || 'Bilinmiyor'}</td>
                        <td>${this.app.formatCurrency(order.total || 0)}</td>
                        <td><span class="badge badge-${order.status || 'pending'}">${this.getStatusText(order.status)}</span></td>
                        <td>${this.app.formatDate(order.date || Date.now())}</td>
                    </tr>
                `).join('')}
            </tbody>
        `;
        
        recentOrdersContainer.innerHTML = '';
        recentOrdersContainer.appendChild(table);
    }

    loadLowStockItems(products) {
        const lowStockContainer = document.getElementById('low-stock-items');
        if (!lowStockContainer) return;

        const lowStockItems = Object.values(products).flat().filter(p => p.stock < 10);
        
        if (lowStockItems.length === 0) {
            lowStockContainer.innerHTML = '<p>Düşük stoklu ürün bulunmuyor.</p>';
            return;
        }

        const list = document.createElement('div');
        list.innerHTML = lowStockItems.map(item => `
            <div class="low-stock-item" style="padding: 0.5rem; border-left: 3px solid #dc3545; margin: 0.5rem 0; background: #fff;">
                <strong>${item.name}</strong> - Stok: ${item.stock}
            </div>
        `).join('');
        
        lowStockContainer.innerHTML = '';
        lowStockContainer.appendChild(list);
    }

    getStatusText(status) {
        const statusMap = {
            'pending': 'Beklemede',
            'preparing': 'Hazırlanıyor',
            'ready': 'Hazır',
            'delivered': 'Teslim Edildi',
            'cancelled': 'İptal Edildi'
        };
        return statusMap[status] || 'Bilinmiyor';
    }
}

// Initialize app based on page
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('admin/')) {
        new AdminManager();
    } else {
        new FirinPastaneApp();
    }
});

// Export for global access
window.FirinPastaneApp = FirinPastaneApp;
window.AdminManager = AdminManager;