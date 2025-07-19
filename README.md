# Fırın Pastane Web Sitesi

Modern ve kullanıcı dostu bir fırın pastane web sitesi. HTML, CSS ve JavaScript teknolojileri kullanılarak geliştirilmiştir.

## 🚀 Özellikler

### Ana Site
- **Modern ve Responsive Tasarım**: Mobil uyumlu, Bootstrap tarzı grid sistem
- **Ürün Kategorileri**: Ekmekler, Simitler, Pastalar
- **Pasta Sipariş Sistemi**: Özel pasta siparişi formu
- **Arama ve Filtreleme**: Ürün arama ve kategori filtreleme
- **İletişim Formu**: Form validasyonu ile iletişim sistemi
- **Sepet Sistemi**: Local storage ile sepet yönetimi

### Admin Paneli
- **Güvenli Giriş Sistemi**: Session yönetimi ile admin girişi
- **Dashboard**: İstatistikler ve genel bakış
- **Ürün Yönetimi**: CRUD operasyonları ile ürün yönetimi
- **Sipariş Yönetimi**: Sipariş takibi ve durum güncelleme
- **Stok Takibi**: Düşük stok uyarıları
- **Raporlama**: Satış ve sipariş raporları

## 📁 Dosya Yapısı

```
firin-scripti/
├── index.html              # Ana sayfa
├── ekmekler.html           # Ekmek kategorisi
├── simitler.html           # Simit kategorisi
├── pastalar.html           # Pasta siparişi
├── iletisim.html           # İletişim sayfası
├── admin/                  # Admin paneli
│   ├── login.html          # Admin girişi
│   ├── dashboard.html      # Ana dashboard
│   ├── urunler.html        # Ürün yönetimi
│   └── siparisler.html     # Sipariş yönetimi
├── css/                    # Stil dosyaları
│   ├── style.css           # Ana stil
│   ├── responsive.css      # Responsive tasarım
│   └── admin.css           # Admin panel stilleri
├── js/                     # JavaScript dosyaları
│   ├── main.js             # Ana JavaScript
│   ├── siparis.js          # Sipariş sistemi
│   └── admin.js            # Admin panel işlevleri
└── images/                 # Görsel dosyaları
    ├── ekmekler/
    ├── simitler/
    └── pastalar/
```

## 🎨 Tasarım Özellikleri

- **Renk Paleti**: Sıcak tonlar (kahverengi, altın, krem)
- **Typography**: Modern ve okunabilir fontlar
- **Responsive**: Mobile-first yaklaşım
- **Accessibility**: Erişilebilirlik standartları
- **Cross-browser**: Tarayıcı uyumluluğu

## 🛠️ Teknolojiler

- **HTML5**: Semantic markup
- **CSS3**: Flexbox, Grid, Animations
- **JavaScript (ES6+)**: Modern JavaScript özellikleri
- **Local Storage**: Veri saklama
- **Font Awesome**: İkonlar
- **Responsive Design**: Mobil uyumluluk

## 🚀 Kurulum ve Çalıştırma

1. **Repository'yi klonlayın:**
   ```bash
   git clone https://github.com/yakup7575/firin-scripti.git
   cd firin-scripti
   ```

2. **Web sunucusu başlatın:**
   ```bash
   # Python ile
   python3 -m http.server 8000
   
   # Node.js ile (http-server kurulu ise)
   http-server
   
   # Ya da herhangi bir web sunucusu ile
   ```

3. **Tarayıcıda açın:**
   ```
   http://localhost:8000
   ```

## 👨‍💼 Admin Paneli

Admin paneline erişim için:

**URL**: `/admin/login.html`

**Demo Giriş Bilgileri**:
- Kullanıcı Adı: `admin`
- Şifre: `123456`

### Admin Panel Özellikleri:
- ✅ Güvenli oturum yönetimi
- ✅ Dashboard ile genel bakış
- ✅ Ürün ekleme/düzenleme/silme
- ✅ Sipariş yönetimi ve takibi
- ✅ Stok kontrolü
- ✅ İstatistikler ve raporlar

## 📱 Responsive Tasarım

- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: 320px - 767px

## 🔧 Özelleştirme

### Renkleri Değiştirme
`css/style.css` dosyasındaki CSS değişkenlerini düzenleyin:

```css
:root {
    --primary-color: #8B4513;
    --secondary-color: #D2691E;
    --accent-color: #DAA520;
    /* ... */
}
```

### Ürün Ekleme
Admin paneli üzerinden veya `js/main.js` dosyasındaki varsayılan ürünleri düzenleyin.

## 📞 İletişim ve Destek

- **E-posta**: info@firinpastane.com
- **Telefon**: +90 212 555 0123
- **Adres**: Fırın Sokak No:123, Pastane Mahallesi, İstanbul

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📝 Değişiklik Geçmişi

- **v1.0.0** - İlk sürüm
  - Ana site sayfaları
  - Admin paneli
  - Responsive tasarım
  - Sipariş sistemi

---

**Geliştirici**: Fırın Pastane Ekibi  
**Versiyon**: 1.0.0  
**Tarih**: 2024