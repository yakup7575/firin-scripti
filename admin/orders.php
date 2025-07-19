<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Yönetimi - Fırın Pastane Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3><i class="bi bi-shop"></i> Fırın Pastane</h3>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products.php" class="nav-link">
                        <i class="bi bi-box"></i> Ürünler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories.php" class="nav-link">
                        <i class="bi bi-tags"></i> Kategoriler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="orders.php" class="nav-link active">
                        <i class="bi bi-cart"></i> Siparişler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customers.php" class="nav-link">
                        <i class="bi bi-people"></i> Müşteriler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i class="bi bi-person-gear"></i> Kullanıcılar
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reports.php" class="nav-link">
                        <i class="bi bi-graph-up"></i> Raporlar
                    </a>
                </li>
                <li class="nav-item">
                    <a href="campaigns.php" class="nav-link">
                        <i class="bi bi-megaphone"></i> Kampanyalar
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <i class="bi bi-gear"></i> Ayarlar
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button type="button" id="sidebarToggle" class="btn btn-outline-primary">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                               data-bs-toggle="dropdown">
                                <img src="https://via.placeholder.com/32x32" class="rounded-circle me-2" alt="User">
                                <span id="userFullName">Admin User</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="changePassword()">
                                    <i class="bi bi-key"></i> Şifre Değiştir
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="logout()">
                                    <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Orders Content -->
            <div class="container-fluid p-4">
                <div class="row mb-4">
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0">Sipariş Yönetimi</h1>
                                <p class="text-muted">Siparişleri takip edin ve durumlarını güncelleyin.</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">
                                <i class="bi bi-plus-lg"></i> Yeni Sipariş
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="text-uppercase text-muted mb-1">Bekleyen</h6>
                                        <h4 class="mb-0" id="pendingOrders">0</h4>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock fs-2 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="text-uppercase text-muted mb-1">Hazırlanıyor</h6>
                                        <h4 class="mb-0" id="preparingOrders">0</h4>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-hourglass fs-2 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="text-uppercase text-muted mb-1">Tamamlanan</h6>
                                        <h4 class="mb-0" id="completedOrders">0</h4>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle fs-2 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="text-uppercase text-muted mb-1">Toplam Ciro</h6>
                                        <h4 class="mb-0" id="totalRevenue">₺0</h4>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-currency-dollar fs-2 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">Tüm Durumlar</option>
                            <option value="pending">Beklemede</option>
                            <option value="preparing">Hazırlanıyor</option>
                            <option value="ready">Hazır</option>
                            <option value="delivering">Teslimatta</option>
                            <option value="completed">Tamamlandı</option>
                            <option value="cancelled">İptal</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateFromFilter">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateToFilter">
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Sipariş veya müşteri ara...">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                            <i class="bi bi-arrow-clockwise"></i> Filtreleri Temizle
                        </button>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="ordersTable" class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Müşteri</th>
                                        <th>Tutar</th>
                                        <th>Durum</th>
                                        <th>Ödeme</th>
                                        <th>Tarih</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sipariş Detayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailContent">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Sipariş Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customerSearch" class="form-label">Müşteri</label>
                                    <input type="text" class="form-control" id="customerSearch" placeholder="Müşteri ara...">
                                    <input type="hidden" id="customerId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="paymentMethod" class="form-label">Ödeme Yöntemi</label>
                                    <select class="form-select" id="paymentMethod">
                                        <option value="cash">Nakit</option>
                                        <option value="card">Kredi Kartı</option>
                                        <option value="online">Online</option>
                                        <option value="bank_transfer">Havale</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ürünler</label>
                            <div class="d-flex mb-2">
                                <input type="text" class="form-control me-2" id="productSearch" placeholder="Ürün ara...">
                                <button type="button" class="btn btn-outline-primary" onclick="addProduct()">
                                    <i class="bi bi-plus"></i> Ekle
                                </button>
                            </div>
                            <div id="orderItems">
                                <!-- Order items will be added here -->
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="deliveryDate" class="form-label">Teslimat Tarihi</label>
                                    <input type="datetime-local" class="form-control" id="deliveryDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discountAmount" class="form-label">İndirim (₺)</label>
                                    <input type="number" class="form-control" id="discountAmount" step="0.01" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deliveryAddress" class="form-label">Teslimat Adresi</label>
                            <textarea class="form-control" id="deliveryAddress" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="orderNotes" class="form-label">Notlar</label>
                            <textarea class="form-control" id="orderNotes" rows="2"></textarea>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="border-top pt-3">
                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Ara Toplam:</td>
                                            <td class="text-end" id="subtotal">₺0.00</td>
                                        </tr>
                                        <tr>
                                            <td>KDV (18%):</td>
                                            <td class="text-end" id="taxAmount">₺0.00</td>
                                        </tr>
                                        <tr>
                                            <td>İndirim:</td>
                                            <td class="text-end" id="discountDisplay">₺0.00</td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Toplam:</td>
                                            <td class="text-end" id="totalAmount">₺0.00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="createOrder()">Sipariş Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        let ordersTable;
        let orderItems = [];
        
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadOrderStats();
            initializeOrdersTable();
            setupEventListeners();
        });
        
        function setupEventListeners() {
            // Filter changes
            document.getElementById('statusFilter').addEventListener('change', filterOrders);
            document.getElementById('dateFromFilter').addEventListener('change', filterOrders);
            document.getElementById('dateToFilter').addEventListener('change', filterOrders);
            
            // Search
            document.getElementById('searchBtn').addEventListener('click', filterOrders);
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterOrders();
                }
            });
            
            // Order form calculations
            document.getElementById('discountAmount').addEventListener('input', calculateOrderTotal);
        }
        
        async function loadOrderStats() {
            try {
                const response = await apiRequest('../api/controllers/OrderController.php?action=stats');
                const stats = response.data;
                
                document.getElementById('pendingOrders').textContent = stats.pending_orders || 0;
                document.getElementById('preparingOrders').textContent = '0'; // This would need to be calculated
                document.getElementById('completedOrders').textContent = stats.completed_orders || 0;
                document.getElementById('totalRevenue').textContent = formatCurrency(stats.total_revenue || 0);
                
            } catch (error) {
                console.error('Error loading order stats:', error);
            }
        }
        
        function initializeOrdersTable() {
            ordersTable = $('#ordersTable').DataTable({
                ajax: {
                    url: '../api/controllers/OrderController.php',
                    headers: {
                        'Authorization': `Bearer ${adminToken}`
                    },
                    data: function(d) {
                        d.status = document.getElementById('statusFilter').value;
                        d.date_from = document.getElementById('dateFromFilter').value;
                        d.date_to = document.getElementById('dateToFilter').value;
                        d.search = document.getElementById('searchInput').value;
                    },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'order_number' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let html = row.customer_name || 'Misafir';
                            if (row.customer_email) {
                                html += `<br><small class="text-muted">${row.customer_email}</small>`;
                            }
                            return html;
                        }
                    },
                    {
                        data: 'total_amount',
                        render: function(data) {
                            return formatCurrency(data);
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            return getStatusBadge(data, 'order');
                        }
                    },
                    {
                        data: 'payment_status',
                        render: function(data) {
                            const statusConfig = {
                                pending: { class: 'bg-warning', text: 'Beklemede' },
                                paid: { class: 'bg-success', text: 'Ödendi' },
                                failed: { class: 'bg-danger', text: 'Başarısız' },
                                refunded: { class: 'bg-info', text: 'İade' }
                            };
                            const config = statusConfig[data] || { class: 'bg-secondary', text: data };
                            return `<span class="badge ${config.class}">${config.text}</span>`;
                        }
                    },
                    {
                        data: 'order_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" onclick="viewOrderDetail(${row.id})" title="Detay">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" title="Durum">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" onclick="updateOrderStatus(${row.id}, 'pending')">Beklemede</a></li>
                                            <li><a class="dropdown-item" onclick="updateOrderStatus(${row.id}, 'preparing')">Hazırlanıyor</a></li>
                                            <li><a class="dropdown-item" onclick="updateOrderStatus(${row.id}, 'ready')">Hazır</a></li>
                                            <li><a class="dropdown-item" onclick="updateOrderStatus(${row.id}, 'delivering')">Teslimatta</a></li>
                                            <li><a class="dropdown-item" onclick="updateOrderStatus(${row.id}, 'completed')">Tamamlandı</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" onclick="updateOrderStatus(${row.id}, 'cancelled')">İptal</a></li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-outline-danger" onclick="deleteOrder(${row.id})" title="Sil">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        },
                        orderable: false
                    }
                ],
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
                },
                order: [[5, 'desc']],
                pageLength: 25
            });
        }
        
        function filterOrders() {
            ordersTable.ajax.reload();
        }
        
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFromFilter').value = '';
            document.getElementById('dateToFilter').value = '';
            document.getElementById('searchInput').value = '';
            filterOrders();
        }
        
        async function viewOrderDetail(id) {
            try {
                const response = await apiRequest(`../api/controllers/OrderController.php?id=${id}`);
                const order = response.data;
                
                const detailContent = document.getElementById('orderDetailContent');
                detailContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Sipariş Bilgileri</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Sipariş No:</strong></td><td>${order.order_number}</td></tr>
                                <tr><td><strong>Durum:</strong></td><td>${getStatusBadge(order.status, 'order')}</td></tr>
                                <tr><td><strong>Ödeme:</strong></td><td>${order.payment_method}</td></tr>
                                <tr><td><strong>Tarih:</strong></td><td>${formatDate(order.order_date)}</td></tr>
                                ${order.delivery_date ? `<tr><td><strong>Teslimat:</strong></td><td>${formatDate(order.delivery_date)}</td></tr>` : ''}
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Müşteri Bilgileri</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Ad:</strong></td><td>${order.customer_name || 'Misafir'}</td></tr>
                                ${order.customer_email ? `<tr><td><strong>Email:</strong></td><td>${order.customer_email}</td></tr>` : ''}
                                ${order.customer_phone ? `<tr><td><strong>Telefon:</strong></td><td>${order.customer_phone}</td></tr>` : ''}
                                ${order.delivery_address ? `<tr><td><strong>Adres:</strong></td><td>${order.delivery_address}</td></tr>` : ''}
                            </table>
                        </div>
                    </div>
                    
                    <h6 class="mt-4">Sipariş İçeriği</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Fiyat</th>
                                    <th>Adet</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${order.items.map(item => `
                                    <tr>
                                        <td>${item.product_name}</td>
                                        <td>${formatCurrency(item.price)}</td>
                                        <td>${item.quantity}</td>
                                        <td>${formatCurrency(item.subtotal)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3"><strong>Ara Toplam</strong></td><td><strong>${formatCurrency(order.total_amount - order.tax_amount + order.discount_amount)}</strong></td></tr>
                                <tr><td colspan="3"><strong>KDV</strong></td><td><strong>${formatCurrency(order.tax_amount)}</strong></td></tr>
                                ${order.discount_amount > 0 ? `<tr><td colspan="3"><strong>İndirim</strong></td><td><strong>-${formatCurrency(order.discount_amount)}</strong></td></tr>` : ''}
                                <tr class="table-primary"><td colspan="3"><strong>Genel Toplam</strong></td><td><strong>${formatCurrency(order.total_amount)}</strong></td></tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    ${order.notes ? `<div class="mt-3"><h6>Notlar</h6><p>${order.notes}</p></div>` : ''}
                `;
                
                new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
                
            } catch (error) {
                Swal.fire('Hata', 'Sipariş detayları yüklenemedi', 'error');
            }
        }
        
        async function updateOrderStatus(id, status) {
            try {
                await apiRequest(`../api/controllers/OrderController.php?id=${id}&action=status`, {
                    method: 'PUT',
                    body: JSON.stringify({ status: status })
                });
                
                Swal.fire('Başarılı', 'Sipariş durumu güncellendi', 'success');
                ordersTable.ajax.reload();
                loadOrderStats();
                
            } catch (error) {
                Swal.fire('Hata', error.message, 'error');
            }
        }
        
        async function deleteOrder(id) {
            const result = await Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu sipariş kalıcı olarak silinecek ve stok geri yüklenecek!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal'
            });
            
            if (result.isConfirmed) {
                try {
                    await apiRequest(`../api/controllers/OrderController.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    Swal.fire('Silindi', 'Sipariş başarıyla silindi', 'success');
                    ordersTable.ajax.reload();
                    loadOrderStats();
                    
                } catch (error) {
                    Swal.fire('Hata', error.message, 'error');
                }
            }
        }
        
        function addProduct() {
            // This would typically search for products and add them to the order
            // For demo purposes, we'll just add a simple item
            const productName = document.getElementById('productSearch').value;
            if (!productName) return;
            
            const item = {
                product_id: null,
                product_name: productName,
                price: 10.00,
                quantity: 1
            };
            
            orderItems.push(item);
            renderOrderItems();
            calculateOrderTotal();
            
            document.getElementById('productSearch').value = '';
        }
        
        function renderOrderItems() {
            const container = document.getElementById('orderItems');
            container.innerHTML = orderItems.map((item, index) => `
                <div class="row mb-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" class="form-control" value="${item.product_name}" readonly>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" value="${item.price}" step="0.01" onchange="updateItemPrice(${index}, this.value)">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" value="${item.quantity}" min="1" onchange="updateItemQuantity(${index}, this.value)">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" value="${formatCurrency(item.price * item.quantity)}" readonly>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        function updateItemPrice(index, price) {
            orderItems[index].price = parseFloat(price) || 0;
            renderOrderItems();
            calculateOrderTotal();
        }
        
        function updateItemQuantity(index, quantity) {
            orderItems[index].quantity = parseInt(quantity) || 1;
            renderOrderItems();
            calculateOrderTotal();
        }
        
        function removeItem(index) {
            orderItems.splice(index, 1);
            renderOrderItems();
            calculateOrderTotal();
        }
        
        function calculateOrderTotal() {
            const subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxAmount = subtotal * 0.18; // 18% KDV
            const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
            const total = subtotal + taxAmount - discountAmount;
            
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('taxAmount').textContent = formatCurrency(taxAmount);
            document.getElementById('discountDisplay').textContent = formatCurrency(discountAmount);
            document.getElementById('totalAmount').textContent = formatCurrency(total);
        }
        
        async function createOrder() {
            if (orderItems.length === 0) {
                Swal.fire('Hata', 'En az bir ürün eklemeniz gerekiyor', 'error');
                return;
            }
            
            const orderData = {
                customer_id: document.getElementById('customerId').value || null,
                payment_method: document.getElementById('paymentMethod').value,
                delivery_date: document.getElementById('deliveryDate').value || null,
                delivery_address: document.getElementById('deliveryAddress').value,
                notes: document.getElementById('orderNotes').value,
                discount_amount: parseFloat(document.getElementById('discountAmount').value) || 0,
                items: orderItems
            };
            
            try {
                const response = await apiRequest('../api/controllers/OrderController.php', {
                    method: 'POST',
                    body: JSON.stringify(orderData)
                });
                
                if (response.success) {
                    Swal.fire('Başarılı', 'Sipariş oluşturuldu', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                    ordersTable.ajax.reload();
                    loadOrderStats();
                    
                    // Reset form
                    document.getElementById('orderForm').reset();
                    orderItems = [];
                    renderOrderItems();
                    calculateOrderTotal();
                }
                
            } catch (error) {
                Swal.fire('Hata', error.message, 'error');
            }
        }
    </script>
</body>
</html>