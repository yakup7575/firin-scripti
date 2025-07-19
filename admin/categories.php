<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Yönetimi - Fırın Pastane Admin</title>
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
                    <a href="categories.php" class="nav-link active">
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

            <!-- Categories Content -->
            <div class="container-fluid p-4">
                <div class="row mb-4">
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0">Kategori Yönetimi</h1>
                                <p class="text-muted">Ürün kategorilerinizi düzenleyin ve yönetin.</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="bi bi-plus-lg"></i> Yeni Kategori
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Categories Grid -->
                <div class="row" id="categoriesGrid">
                    <!-- Categories will be loaded here -->
                </div>

                <!-- Categories Table -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Kategori Listesi</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="categoriesTable" class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Resim</th>
                                        <th>Kategori Adı</th>
                                        <th>Açıklama</th>
                                        <th>Ürün Sayısı</th>
                                        <th>Sıralama</th>
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

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Yeni Kategori Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Kategori Adı *</label>
                                    <input type="text" class="form-control" id="categoryName" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categoryDescription" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="categoryStatus" class="form-label">Durum</label>
                                            <select class="form-select" id="categoryStatus">
                                                <option value="active">Aktif</option>
                                                <option value="inactive">Pasif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="categorySortOrder" class="form-label">Sıralama</label>
                                            <input type="number" class="form-control" id="categorySortOrder" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="categoryImage" class="form-label">Kategori Resmi</label>
                                    <input type="file" class="form-control" id="categoryImage" accept="image/*">
                                    <div id="categoryImagePreview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">Kaydet</button>
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
        let categoriesTable;
        
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadCategoriesGrid();
            initializeCategoriesTable();
            setupFileUpload('categoryImage', 'categoryImagePreview');
        });
        
        async function loadCategoriesGrid() {
            try {
                const response = await apiRequest('../api/controllers/CategoryController.php?action=with-product-count');
                const categories = response.data;
                
                const grid = document.getElementById('categoriesGrid');
                grid.innerHTML = '';
                
                categories.forEach(category => {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
                    
                    col.innerHTML = `
                        <div class="card h-100 border-0 shadow-sm category-card">
                            <div class="card-body text-center">
                                <div class="category-icon mb-3">
                                    ${category.image ? 
                                        `<img src="../uploads/${category.image}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">` :
                                        '<div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;"><i class="bi bi-tag fs-4"></i></div>'
                                    }
                                </div>
                                <h6 class="card-title">${category.name}</h6>
                                <p class="card-text text-muted small">${category.description || 'Açıklama yok'}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge ${category.status === 'active' ? 'bg-success' : 'bg-secondary'}">${category.status === 'active' ? 'Aktif' : 'Pasif'}</span>
                                    <span class="text-muted small">${category.product_count} ürün</span>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editCategory(${category.id})" title="Düzenle">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${category.id})" title="Sil">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    grid.appendChild(col);
                });
                
            } catch (error) {
                console.error('Error loading categories grid:', error);
            }
        }
        
        function initializeCategoriesTable() {
            categoriesTable = $('#categoriesTable').DataTable({
                ajax: {
                    url: '../api/controllers/CategoryController.php?action=with-product-count',
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
                                `<img src="../uploads/${data}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">` :
                                '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-tag text-muted"></i></div>';
                        },
                        orderable: false
                    },
                    { data: 'name' },
                    { 
                        data: 'description',
                        render: function(data) {
                            return data || '<span class="text-muted">Açıklama yok</span>';
                        }
                    },
                    { 
                        data: 'product_count',
                        render: function(data) {
                            return `<span class="badge bg-info">${data}</span>`;
                        }
                    },
                    { data: 'sort_order' },
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
                                    <button class="btn btn-outline-primary" onclick="editCategory(${row.id})" title="Düzenle">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteCategory(${row.id})" title="Sil">
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
                order: [[4, 'asc'], [1, 'asc']],
                pageLength: 25
            });
        }
        
        async function saveCategory() {
            const categoryData = {
                name: document.getElementById('categoryName').value,
                description: document.getElementById('categoryDescription').value,
                status: document.getElementById('categoryStatus').value,
                sort_order: parseInt(document.getElementById('categorySortOrder').value) || 0,
                image: '' // Will be handled separately for file upload
            };
            
            if (!categoryData.name) {
                Swal.fire('Hata', 'Kategori adı gereklidir', 'error');
                return;
            }
            
            try {
                const categoryId = document.getElementById('categoryId').value;
                const isEdit = !!categoryId;
                
                const url = isEdit ? 
                    `../api/controllers/CategoryController.php?id=${categoryId}` : 
                    '../api/controllers/CategoryController.php';
                
                const method = isEdit ? 'PUT' : 'POST';
                
                const response = await apiRequest(url, {
                    method: method,
                    body: JSON.stringify(categoryData)
                });
                
                if (response.success) {
                    Swal.fire('Başarılı', isEdit ? 'Kategori güncellendi' : 'Kategori eklendi', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                    categoriesTable.ajax.reload();
                    loadCategoriesGrid();
                    document.getElementById('categoryForm').reset();
                }
                
            } catch (error) {
                Swal.fire('Hata', error.message, 'error');
            }
        }
        
        async function editCategory(id) {
            try {
                const response = await apiRequest(`../api/controllers/CategoryController.php?id=${id}`);
                const category = response.data;
                
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryName').value = category.name;
                document.getElementById('categoryDescription').value = category.description || '';
                document.getElementById('categoryStatus').value = category.status;
                document.getElementById('categorySortOrder').value = category.sort_order;
                
                document.getElementById('categoryModalTitle').textContent = 'Kategori Düzenle';
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
                
            } catch (error) {
                Swal.fire('Hata', 'Kategori bilgileri yüklenemedi', 'error');
            }
        }
        
        async function deleteCategory(id) {
            const result = await Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu kategori kalıcı olarak silinecek!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal'
            });
            
            if (result.isConfirmed) {
                try {
                    await apiRequest(`../api/controllers/CategoryController.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    Swal.fire('Silindi', 'Kategori başarıyla silindi', 'success');
                    categoriesTable.ajax.reload();
                    loadCategoriesGrid();
                    
                } catch (error) {
                    Swal.fire('Hata', error.message, 'error');
                }
            }
        }
        
        // Reset modal when closed
        document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryModalTitle').textContent = 'Yeni Kategori Ekle';
            document.getElementById('categoryImagePreview').innerHTML = '';
        });
        
        // Add some custom styles for category cards
        const style = document.createElement('style');
        style.textContent = `
            .category-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .category-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>