<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Ayarları - Fırın Pastane Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
                    <a href="settings.php" class="nav-link active">
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

            <!-- Settings Content -->
            <div class="container-fluid p-4">
                <div class="row mb-4">
                    <div class="col">
                        <h1 class="h3 mb-0">Sistem Ayarları</h1>
                        <p class="text-muted">Sistem ayarlarınızı yapılandırın.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- General Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Genel Ayarlar</h5>
                            </div>
                            <div class="card-body">
                                <form id="generalSettingsForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="siteName" class="form-label">Site Adı</label>
                                                <input type="text" class="form-control" id="siteName" value="Fırın Pastane">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="siteEmail" class="form-label">Site E-posta</label>
                                                <input type="email" class="form-control" id="siteEmail" value="info@firin.com">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sitePhone" class="form-label">Telefon</label>
                                                <input type="text" class="form-control" id="sitePhone" value="+90 212 123 45 67">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="currency" class="form-label">Para Birimi</label>
                                                <select class="form-select" id="currency">
                                                    <option value="TRY">Türk Lirası (₺)</option>
                                                    <option value="USD">Amerikan Doları ($)</option>
                                                    <option value="EUR">Euro (€)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="siteAddress" class="form-label">Adres</label>
                                        <textarea class="form-control" id="siteAddress" rows="2">İstanbul, Türkiye</textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="taxRate" class="form-label">KDV Oranı (%)</label>
                                                <input type="number" class="form-control" id="taxRate" value="18" min="0" max="100">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="minOrderAmount" class="form-label">Minimum Sipariş Tutarı</label>
                                                <input type="number" class="form-control" id="minOrderAmount" value="25" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Kaydet
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-envelope me-2"></i>E-posta Ayarları</h5>
                            </div>
                            <div class="card-body">
                                <form id="emailSettingsForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtpHost" class="form-label">SMTP Host</label>
                                                <input type="text" class="form-control" id="smtpHost" placeholder="smtp.gmail.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtpPort" class="form-label">SMTP Port</label>
                                                <input type="number" class="form-control" id="smtpPort" value="587">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtpUsername" class="form-label">SMTP Kullanıcı Adı</label>
                                                <input type="text" class="form-control" id="smtpUsername">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtpPassword" class="form-label">SMTP Şifre</label>
                                                <input type="password" class="form-control" id="smtpPassword">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fromEmail" class="form-label">Gönderen E-posta</label>
                                                <input type="email" class="form-control" id="fromEmail" value="noreply@firin.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fromName" class="form-label">Gönderen Adı</label>
                                                <input type="text" class="form-control" id="fromName" value="Fırın Pastane">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg me-2"></i>Kaydet
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="testEmail()">
                                            <i class="bi bi-send me-2"></i>Test E-posta Gönder
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- System Info -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Sistem Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Sistem Versiyonu:</strong></td>
                                        <td>1.0.0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PHP Versiyonu:</strong></td>
                                        <td><?php echo PHP_VERSION; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Veritabanı:</strong></td>
                                        <td>MySQL/MariaDB</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Son Güncelleme:</strong></td>
                                        <td><?php echo date('d.m.Y H:i'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Backup -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-cloud-download me-2"></i>Yedekleme</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Veritabanınızın yedeğini alın.</p>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" onclick="createBackup()">
                                        <i class="bi bi-download me-2"></i>Yedek Oluştur
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="viewBackups()">
                                        <i class="bi bi-folder me-2"></i>Yedekleri Görüntüle
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Güvenlik</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">JWT Token Süresi (saniye)</label>
                                    <input type="number" class="form-control" id="jwtExpiry" value="3600">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Maksimum Dosya Boyutu (MB)</label>
                                    <input type="number" class="form-control" id="maxFileSize" value="5">
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="enableTwoFA">
                                    <label class="form-check-label" for="enableTwoFA">
                                        İki Faktörlü Kimlik Doğrulama
                                    </label>
                                </div>
                                
                                <button class="btn btn-outline-primary w-100" onclick="saveSecuritySettings()">
                                    <i class="bi bi-shield-check me-2"></i>Güvenlik Ayarlarını Kaydet
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadSettings();
            setupEventListeners();
        });
        
        function setupEventListeners() {
            document.getElementById('generalSettingsForm').addEventListener('submit', saveGeneralSettings);
            document.getElementById('emailSettingsForm').addEventListener('submit', saveEmailSettings);
        }
        
        function loadSettings() {
            // In a real implementation, this would load settings from the API
            // For now, we'll use the default values already in the form
        }
        
        async function saveGeneralSettings(e) {
            e.preventDefault();
            
            const settings = {
                site_name: document.getElementById('siteName').value,
                site_email: document.getElementById('siteEmail').value,
                site_phone: document.getElementById('sitePhone').value,
                site_address: document.getElementById('siteAddress').value,
                currency: document.getElementById('currency').value,
                tax_rate: document.getElementById('taxRate').value,
                min_order_amount: document.getElementById('minOrderAmount').value
            };
            
            try {
                // This would typically save to the API
                // await apiRequest('../api/controllers/SettingsController.php', {
                //     method: 'POST',
                //     body: JSON.stringify(settings)
                // });
                
                Swal.fire('Başarılı', 'Genel ayarlar kaydedildi', 'success');
                
            } catch (error) {
                Swal.fire('Hata', 'Ayarlar kaydedilemedi', 'error');
            }
        }
        
        async function saveEmailSettings(e) {
            e.preventDefault();
            
            const settings = {
                smtp_host: document.getElementById('smtpHost').value,
                smtp_port: document.getElementById('smtpPort').value,
                smtp_username: document.getElementById('smtpUsername').value,
                smtp_password: document.getElementById('smtpPassword').value,
                from_email: document.getElementById('fromEmail').value,
                from_name: document.getElementById('fromName').value
            };
            
            try {
                // This would typically save to the API
                Swal.fire('Başarılı', 'E-posta ayarları kaydedildi', 'success');
                
            } catch (error) {
                Swal.fire('Hata', 'E-posta ayarları kaydedilemedi', 'error');
            }
        }
        
        function saveSecuritySettings() {
            const settings = {
                jwt_expiry: document.getElementById('jwtExpiry').value,
                max_file_size: document.getElementById('maxFileSize').value,
                enable_two_fa: document.getElementById('enableTwoFA').checked
            };
            
            Swal.fire('Başarılı', 'Güvenlik ayarları kaydedildi', 'success');
        }
        
        function testEmail() {
            Swal.fire({
                title: 'Test E-posta',
                input: 'email',
                inputLabel: 'Test e-postasının gönderileceği adres',
                inputPlaceholder: 'ornek@email.com',
                showCancelButton: true,
                confirmButtonText: 'Gönder',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // This would send a test email
                    Swal.fire('Başarılı', `Test e-postası ${result.value} adresine gönderildi`, 'success');
                }
            });
        }
        
        function createBackup() {
            Swal.fire({
                title: 'Yedek Oluşturuluyor...',
                text: 'Lütfen bekleyin',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Simulate backup creation
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Yedek Oluşturuldu',
                            text: `Veritabanı yedeği backup_${new Date().toISOString().slice(0, 10)}.sql olarak kaydedildi`,
                            showCancelButton: true,
                            confirmButtonText: 'İndir',
                            cancelButtonText: 'Tamam'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // This would download the backup file
                                console.log('Downloading backup...');
                            }
                        });
                    }, 2000);
                }
            });
        }
        
        function viewBackups() {
            // This would show a list of available backups
            Swal.fire({
                title: 'Mevcut Yedekler',
                html: `
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            backup_2024-01-15.sql
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1">İndir</button>
                                <button class="btn btn-sm btn-outline-danger">Sil</button>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            backup_2024-01-10.sql
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1">İndir</button>
                                <button class="btn btn-sm btn-outline-danger">Sil</button>
                            </div>
                        </div>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false
            });
        }
    </script>
</body>
</html>