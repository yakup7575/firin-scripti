<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Yönetimi - Fırın Pastane Admin</title>
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
                    <a href="products.php" class="nav-link active">
                        <i class="bi bi-box"></i> Ürünler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories.php" class="nav-link">
                        <i class="bi bi-tags"></i> Kategoriler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="orders.php" class="nav-link">
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

            <!-- Products Content -->
            <div class="container-fluid p-4">
                <div class="row mb-4">
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0">Ürün Yönetimi</h1>
                                <p class="text-muted">Ürünlerinizi yönetin, stok takibi yapın.</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                                <i class="bi bi-plus-lg"></i> Yeni Ürün
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Tüm Kategoriler</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Tüm Durumlar</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Pasif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="stockFilter">
                            <option value="">Tüm Stoklar</option>
                            <option value="low">Düşük Stok</option>
                            <option value="out">Tükendi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Ürün ara...">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="productsTable" class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Resim</th>
                                        <th>Ürün Adı</th>
                                        <th>Kategori</th>
                                        <th>SKU</th>
                                        <th>Fiyat</th>
                                        <th>Stok</th>
                                        <th>Durum</th>
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

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Yeni Ürün Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="productId">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Ürün Adı *</label>
                                    <input type="text" class="form-control" id="productName" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="productDescription" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="productDescription" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productCategory" class="form-label">Kategori</label>
                                            <select class="form-select" id="productCategory">
                                                <option value="">Kategori seçin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productSKU" class="form-label">SKU</label>
                                            <input type="text" class="form-control" id="productSKU" placeholder="Otomatik oluşturulacak">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productPrice" class="form-label">Fiyat (₺) *</label>
                                            <input type="number" class="form-control" id="productPrice" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productOldPrice" class="form-label">Eski Fiyat (₺)</label>
                                            <input type="number" class="form-control" id="productOldPrice" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productStock" class="form-label">Stok Miktarı *</label>
                                            <input type="number" class="form-control" id="productStock" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productMinStock" class="form-label">Minimum Stok</label>
                                            <input type="number" class="form-control" id="productMinStock" value="5">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="productStatus" class="form-label">Durum</label>
                                            <select class="form-select" id="productStatus">
                                                <option value="active">Aktif</option>
                                                <option value="inactive">Pasif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="productFeatured">
                                                <label class="form-check-label" for="productFeatured">
                                                    Öne Çıkarılan Ürün
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="productImage" class="form-label">Ürün Resmi</label>
                                    <input type="file" class="form-control" id="productImage" accept="image/*">
                                    <div id="imagePreview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div class="modal fade" id="stockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stok Güncelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm">
                        <input type="hidden" id="stockProductId">
                        
                        <div class="mb-3">
                            <label class="form-label">Ürün: <strong id="stockProductName"></strong></label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mevcut Stok: <strong id="currentStock"></strong></label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stockOperation" class="form-label">İşlem</label>
                            <select class="form-select" id="stockOperation">
                                <option value="add">Stok Ekle</option>
                                <option value="subtract">Stok Çıkar</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stockQuantity" class="form-label">Miktar</label>
                            <input type="number" class="form-control" id="stockQuantity" min="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="updateStock()">Güncelle</button>
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
        let productsTable;
        let categories = [];
        
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadCategories();
            initializeProductsTable();
            setupEventListeners();
            setupFileUpload('productImage', 'imagePreview');
        });
        
        function setupEventListeners() {
            // Filter changes
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);
            document.getElementById('statusFilter').addEventListener('change', filterProducts);
            document.getElementById('stockFilter').addEventListener('change', filterProducts);
            
            // Search
            document.getElementById('searchBtn').addEventListener('click', filterProducts);
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterProducts();
                }
            });
        }
        
        async function loadCategories() {
            try {
                const response = await apiRequest('../api/controllers/CategoryController.php?action=active');
                categories = response.data;
                
                // Populate category selectors
                const categorySelects = document.querySelectorAll('#categoryFilter, #productCategory');
                categorySelects.forEach(select => {
                    const isFilter = select.id === 'categoryFilter';
                    select.innerHTML = isFilter ? '<option value="">Tüm Kategoriler</option>' : '<option value="">Kategori seçin</option>';
                    
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        select.appendChild(option);
                    });
                });
                
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }
        
        function initializeProductsTable() {
            productsTable = $('#productsTable').DataTable({
                ajax: {
                    url: '../api/controllers/ProductController.php',
                    headers: {
                        'Authorization': `Bearer ${adminToken}`
                    },
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: 'image',
                        render: function(data) {
                            return data ? 
                                `<img src="../uploads/${data}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">` :
                                '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bi bi-image text-muted"></i></div>';
                        },
                        orderable: false
                    },
                    {
                        data: 'name',
                        render: function(data, type, row) {
                            let html = `<strong>${data}</strong>`;
                            if (row.featured) {
                                html += ' <span class="badge bg-warning ms-1">Öne Çıkan</span>';
                            }
                            if (row.stock <= row.min_stock) {
                                html += ' <span class="badge bg-danger ms-1">Düşük Stok</span>';
                            }
                            return html;
                        }
                    },
                    { data: 'category_name' },
                    { data: 'sku' },
                    {
                        data: 'price',
                        render: function(data, type, row) {
                            let html = `<strong>${formatCurrency(data)}</strong>`;
                            if (row.old_price > 0) {
                                html += `<br><small class="text-muted text-decoration-line-through">${formatCurrency(row.old_price)}</small>`;
                            }
                            return html;
                        }
                    },
                    {
                        data: 'stock',
                        render: function(data, type, row) {
                            let className = 'text-success';
                            if (data <= 0) className = 'text-danger';
                            else if (data <= row.min_stock) className = 'text-warning';
                            
                            return `<span class="${className}"><strong>${data}</strong></span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            return getStatusBadge(data, 'general');
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editProduct(${row.id})" title="Düzenle">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="openStockModal(${row.id}, '${row.name}', ${row.stock})" title="Stok Güncelle">
                                        <i class="bi bi-box"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteProduct(${row.id})" title="Sil">
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
                order: [[1, 'asc']],
                pageLength: 25
            });
        }
        
        function filterProducts() {
            productsTable.ajax.reload();
        }
        
        async function saveProduct() {
            const form = document.getElementById('productForm');
            const formData = new FormData(form);
            
            const productData = {
                name: document.getElementById('productName').value,
                description: document.getElementById('productDescription').value,
                category_id: document.getElementById('productCategory').value || null,
                sku: document.getElementById('productSKU').value,
                price: parseFloat(document.getElementById('productPrice').value),
                old_price: parseFloat(document.getElementById('productOldPrice').value) || 0,
                stock: parseInt(document.getElementById('productStock').value),
                min_stock: parseInt(document.getElementById('productMinStock').value) || 5,
                status: document.getElementById('productStatus').value,
                featured: document.getElementById('productFeatured').checked,
                image: '' // Will be handled separately for file upload
            };
            
            if (!productData.name || !productData.price || productData.stock < 0) {
                Swal.fire('Hata', 'Lütfen gerekli alanları doldurun', 'error');
                return;
            }
            
            try {
                const productId = document.getElementById('productId').value;
                const isEdit = !!productId;
                
                const url = isEdit ? 
                    `../api/controllers/ProductController.php?id=${productId}` : 
                    '../api/controllers/ProductController.php';
                
                const method = isEdit ? 'PUT' : 'POST';
                
                const response = await apiRequest(url, {
                    method: method,
                    body: JSON.stringify(productData)
                });
                
                if (response.success) {
                    Swal.fire('Başarılı', isEdit ? 'Ürün güncellendi' : 'Ürün eklendi', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                    productsTable.ajax.reload();
                    form.reset();
                }
                
            } catch (error) {
                Swal.fire('Hata', error.message, 'error');
            }
        }
        
        async function editProduct(id) {
            try {
                const response = await apiRequest(`../api/controllers/ProductController.php?id=${id}`);
                const product = response.data;
                
                document.getElementById('productId').value = product.id;
                document.getElementById('productName').value = product.name;
                document.getElementById('productDescription').value = product.description || '';
                document.getElementById('productCategory').value = product.category_id || '';
                document.getElementById('productSKU').value = product.sku || '';
                document.getElementById('productPrice').value = product.price;
                document.getElementById('productOldPrice').value = product.old_price || '';
                document.getElementById('productStock').value = product.stock;
                document.getElementById('productMinStock').value = product.min_stock;
                document.getElementById('productStatus').value = product.status;
                document.getElementById('productFeatured').checked = product.featured == 1;
                
                document.getElementById('productModalTitle').textContent = 'Ürün Düzenle';
                new bootstrap.Modal(document.getElementById('productModal')).show();
                
            } catch (error) {
                Swal.fire('Hata', 'Ürün bilgileri yüklenemedi', 'error');
            }
        }
        
        function openStockModal(id, name, currentStock) {
            document.getElementById('stockProductId').value = id;
            document.getElementById('stockProductName').textContent = name;
            document.getElementById('currentStock').textContent = currentStock;
            document.getElementById('stockQuantity').value = '';
            
            new bootstrap.Modal(document.getElementById('stockModal')).show();
        }
        
        async function updateStock() {
            const productId = document.getElementById('stockProductId').value;
            const operation = document.getElementById('stockOperation').value;
            const quantity = parseInt(document.getElementById('stockQuantity').value);
            
            if (!quantity || quantity <= 0) {
                Swal.fire('Hata', 'Geçerli bir miktar girin', 'error');
                return;
            }
            
            try {
                const response = await apiRequest(`../api/controllers/ProductController.php?id=${productId}&action=update-stock`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        quantity: quantity,
                        operation: operation
                    })
                });
                
                if (response.success) {
                    Swal.fire('Başarılı', 'Stok güncellendi', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('stockModal')).hide();
                    productsTable.ajax.reload();
                }
                
            } catch (error) {
                Swal.fire('Hata', error.message, 'error');
            }
        }
        
        async function deleteProduct(id) {
            const result = await Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu ürün kalıcı olarak silinecek!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal'
            });
            
            if (result.isConfirmed) {
                try {
                    await apiRequest(`../api/controllers/ProductController.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    Swal.fire('Silindi', 'Ürün başarıyla silindi', 'success');
                    productsTable.ajax.reload();
                    
                } catch (error) {
                    Swal.fire('Hata', error.message, 'error');
                }
            }
        }
        
        // Reset modal when closed
        document.getElementById('productModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModalTitle').textContent = 'Yeni Ürün Ekle';
            document.getElementById('imagePreview').innerHTML = '';
        });
    </script>
</body>
</html>