# Fırın Pastane Yönetim Sistemi

Modern ve profesyonel bir fırın/pastane işletmesi için geliştirilmiş kapsamlı yönetim sistemi.

## Özellikler

### 🔐 Güvenlik
- JWT tabanlı kimlik doğrulama
- Rol tabanlı yetki yönetimi (Admin, Moderatör, Görüntüleyici)
- SQL injection koruması
- XSS koruması
- CSRF koruması
- Güvenli dosya yükleme

### 📊 Admin Paneli
- Modern ve responsive tasarım
- Gerçek zamanlı dashboard
- Kullanıcı dostu arayüz
- Bootstrap 5 ve modern JavaScript

### 🛍️ Ürün Yönetimi
- Kategori bazlı ürün organizasyonu
- Stok takibi ve uyarılar
- Toplu ürün işlemleri
- Ürün görselleri ve galeri
- SKU yönetimi
- Öne çıkan ürünler
- Fiyat geçmişi

### 📋 Sipariş Yönetimi
- Kapsamlı sipariş takibi
- Durum güncellemeleri
- Müşteri bilgileri
- Otomatik sipariş numarası
- Stok entegrasyonu

### 👥 Müşteri Yönetimi
- Müşteri profilleri
- Sipariş geçmişi
- İstatistikler ve analizler

### 📈 Raporlama ve Analitik
- Satış raporları
- Ürün performansı
- Müşteri analizleri
- Gelir-gider takibi
- Grafikli görselleştirme

## Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri / MariaDB
- Apache/Nginx web sunucusu
- PHP PDO ve GD uzantıları

### Adım 1: Dosyaları Yükleyin
```bash
git clone https://github.com/yakup7575/firin-scripti.git
cd firin-scripti
```

### Adım 2: Veritabanı Ayarları
`api/config/database.php` dosyasında veritabanı ayarlarınızı yapın:

```php
private $host = 'localhost';
private $db_name = 'firin_db';
private $username = 'root';
private $password = '';
```

### Adım 3: Veritabanını Oluşturun
Tarayıcınızda `install.php` dosyasını çalıştırın:
```
http://yoursite.com/install.php
```

Bu script:
- Gerekli tabloları oluşturur
- Örnek verileri ekler
- Varsayılan admin kullanıcısı oluşturur

### Adım 4: İzinleri Ayarlayın
```bash
chmod 755 uploads/
chmod 755 backup/
```

### Adım 5: Admin Paneline Giriş
```
URL: http://yoursite.com/admin/login.php
Kullanıcı: admin
Şifre: admin123
```

## Dizin Yapısı

```
firin-scripti/
├── admin/                 # Admin panel sayfaları
│   ├── login.php         # Giriş sayfası
│   ├── dashboard.php     # Dashboard
│   ├── products.php      # Ürün yönetimi
│   ├── categories.php    # Kategori yönetimi
│   └── ...
├── api/                  # Backend API
│   ├── config/          # Yapılandırma dosyaları
│   ├── controllers/     # API kontrolcüleri
│   ├── models/          # Veri modelleri
│   ├── middleware/      # Ara yazılımlar
│   └── utils/           # Yardımcı sınıflar
├── assets/              # Statik dosyalar
│   ├── css/            # Stil dosyaları
│   ├── js/             # JavaScript dosyaları
│   └── images/         # Resim dosyaları
├── uploads/             # Yüklenen dosyalar
├── backup/              # Yedek dosyalar
└── install.php          # Kurulum scripti
```

## API Endpoints

### Kimlik Doğrulama
- `POST /api/controllers/AuthController.php` - Giriş yap
- `GET /api/controllers/AuthController.php?action=me` - Kullanıcı bilgileri
- `POST /api/controllers/AuthController.php?action=logout` - Çıkış yap

### Ürünler
- `GET /api/controllers/ProductController.php` - Ürün listesi
- `POST /api/controllers/ProductController.php` - Yeni ürün
- `PUT /api/controllers/ProductController.php?id={id}` - Ürün güncelle
- `DELETE /api/controllers/ProductController.php?id={id}` - Ürün sil

### Kategoriler
- `GET /api/controllers/CategoryController.php` - Kategori listesi
- `POST /api/controllers/CategoryController.php` - Yeni kategori
- `PUT /api/controllers/CategoryController.php?id={id}` - Kategori güncelle
- `DELETE /api/controllers/CategoryController.php?id={id}` - Kategori sil

## Güvenlik

### JWT Token
Tüm API istekleri JWT token ile korunur:
```javascript
headers: {
    'Authorization': 'Bearer ' + token
}
```

### Rol Tabanlı Erişim
- **Admin**: Tüm işlemler
- **Moderatör**: Ürün/kategori yönetimi
- **Görüntüleyici**: Sadece görüntüleme

### Dosya Yükleme
- Dosya türü kontrolü
- Boyut sınırlaması
- Otomatik resim optimizasyonu
- Güvenli dosya isimlendirme

## Özelleştirme

### Tema
`assets/css/admin.css` dosyasında tema renklerini değiştirebilirsiniz:

```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}
```

### Ayarlar
`api/config/config.php` dosyasında sistem ayarlarını yapabilirsiniz.

## Katkıda Bulunma

1. Bu repository'yi fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## Destek

Herhangi bir sorun yaşarsanız veya öneriniz varsa issue açabilirsiniz.

---

**Not**: Bu sistem demo amaçlı olarak geliştirilmiştir. Canlı ortamda kullanmadan önce güvenlik ayarlarını gözden geçirin.