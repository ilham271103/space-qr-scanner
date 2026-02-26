# 📚 Setup Guide - GitHub Deployment

Panduan lengkap untuk deploy QR Scanner di GitHub Pages dan integrasi dengan login page Mikrotik.

## 🚀 Quick Start - 5 Menit

### Step 1: Fork/Clone Repository

```bash
# Clone ke komputer lokal
git clone https://github.com/your-username/scanner-qr.git
cd scanner-qr
```

### Step 2: Setup GitHub Pages

1. Go to GitHub repository → **Settings**
2. Muncul di sidebar → **Pages**
3. Di bagian "Source", pilih:
   - **Branch:** `main`
   - **Folder:** `/ (root)`
4. Klik **Save**

Your site akan live di: `https://your-username.github.io/scanner-qr/`

### Step 3: Update Login Page

Di halaman login Mikrotik Anda, tambahkan button untuk redirect ke scanner:

```html
<a href="https://your-username.github.io/scanner-qr/?dst=http://localhost:8090/login" 
   class="btn btn-qr">
   📱 Login dengan QR Code
</a>
```

**⚠️ Penting:** Ganti `your-username` dengan username GitHub Anda!

## 🔌 Integrasi dengan Login Page

### Format URL Parameter

```
https://your-username.github.io/scanner-qr/?dst=YOUR_LOGIN_PAGE_URL
```

Contoh lengkap:

```html
<!-- Jika login page di domain sendiri -->
<a href="https://your-username.github.io/scanner-qr/?dst=https://yourdomain.com/login.php">
   Login QR
</a>

<!-- Jika localhost -->
<a href="https://your-username.github.io/scanner-qr/?dst=http://localhost:8090/login.php">
   Login QR
</a>

<!-- Dengan pre-fill username -->
<a href="https://your-username.github.io/scanner-qr/?dst=http://localhost:8090/login.php&username=admin">
   Login QR
</a>
```

## 🔐 Production Setup

### Checklist Keamanan

- [ ] **Use HTTPS** - GitHub Pages automatically HTTPS
- [ ] **CORS Configuration** - Login page perlu accept POST dari GitHub domain
- [ ] **Rate Limiting** - Implement di login page
- [ ] **Input Validation** - Validate credential format
- [ ] **Session Management** - Proper timeout handling

### CORS Setup (di Login Page)

Jika login page berbeda domain dengan user (yang scan QR), perlu CORS:

```php
<?php
// Di PHP login page Anda
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
```

### Atau lebih ketat (recommended):

```php
<?php
$allowed_origins = [
    'https://your-username.github.io',
    'http://localhost:3000',
    'http://localhost:8000'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}
?>
```

## 📝 Testing Locally

Sebelum push ke GitHub, test dulu di local:

### Setup Local Server

**Using Python 3:**
```bash
python -m http.server 8000
```

**Using PHP:**
```bash
php -S localhost:8000
```

**Using Node.js:**
```bash
npx http-server
```

### Test Scanner

Buka browser, akses:
```
http://localhost:8000/?dst=http://localhost:9000/login.php
```

## 🎯 Flow Testing Checklist

- [ ] Scanner page load dengan baik
- [ ] Camera permission diminta & working
- [ ] QR code terdeteksi dan di-parse
- [ ] Credential dikirim ke login page via POST
- [ ] Login page menerima form data
- [ ] User redirect ke dashboard setelah login
- [ ] Error handling berfungsi (invalid QR, network error, etc)

## 📦 File Structure

```
scanner-qr/
├── index.html                    # Main page
├── README.md                     # Documentation
├── SETUP.md                      # File ini
├── .gitignore                    # Git ignore
├── EXAMPLE_LOGIN_PAGE.php        # Contoh integrasi
├── assets/
│   ├── css/
│   │   ├── style.css            # Main CSS
│   │   └── fontawesome-free-7.1.0-web/
│   │       ├── css/all.min.css
│   │       └── webfonts/
│   ├── js/
│   │   ├── scanner.js           # Main logic
│   │   └── html5-qrcode.min.js  # QR library
│   └── img/
│       ├── planet.webp
│       └── rocket.webp
```

## ⚙️ Configuration

### Environment Variables (untuk future use)

Jika perlu config lebih complex, bisa tambah `.env.example`:

```bash
# .env.example
SCANNER_URL=https://your-username.github.io/scanner-qr/
LOGIN_PAGE_URL=http://localhost:8090/login.php
TIMEOUT_REDIRECT=5000
```

## 🐛 Troubleshooting

### Camera tidak muncul
- ✅ Check browser permissions
- ✅ Use HTTPS (bukan HTTP localhost!)
- ✅ Test di browser yang support WebRTC (Chrome, Firefox, Edge, Safari)
- ✅ Bukan Firefox dalam privacy mode

### QR tidak terdeteksi
- ✅ Cek QR code format: `username:password`
- ✅ Coba perbaikan angle dan lighting
- ✅ Scan dari distance yang sesuai (10-30cm)
- ✅ Check console untuk error message

### Redirect tidak bekerja
- ✅ Verify parameter `dst` ada
- ✅ Check CORS error di console
- ✅ Pastikan login page bisa menerima POST
- ✅ Check form data di Network tab

### Form POST tidak terima di login page
```javascript
// Debug di browser console:
console.log("Form data:", new FormData());
console.log("Return URL:", urlParams.dst);
```

## 📊 Performance Optimization

### Image Optimization

Compresskan gambar sebelum upload:

```bash
# Using ImageOptim (Mac) atau similar tools
# Atau online: https://tinypng.com/
```

### CSS/JS Minification

Jika perlu minify:

```bash
npm install -g terser
terser assets/js/scanner.js -c -m -o assets/js/scanner.min.js
```

## 🔄 Update & Maintenance

### Update Dependencies

```bash
# Jika ada package.json
npm update
```

### Update html5-qrcode Library

Check latest version: https://github.com/mebjas/html5-qrcode

```html
<!-- Update di index.html -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@latest"></script>
```

## 📱 Mobile Testing

### iOS Safari
- Needs HTTPS
- Camera permission via Settings
- Test dengan real device

### Android Chrome
- Support HTTP localhost
- Camera permission di app
- Test di device simulator atau real device

## 🎓 Learning Resources

- [GitHub Pages Docs](https://docs.github.com/en/pages)
- [html5-qrcode Docs](https://github.com/mebjas/html5-qrcode)
- [CORS Explained](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
- [WebRTC API](https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API)

## 💡 Tips & Tricks

### Generate QR Code

Use tools untuk generate QR dengan format:
- [qr-server.com](https://qr-server.com/) - Online QR generator
- [Online QR Code Generator](https://www.qr-code-generator.com/)

Data format:
```
admin:password123
```

### Test Data

Untuk testing, gunakan QR dengan data:
- `testuser:testpass123`
- `admin:admin`

### Debug Mode

Enable debug logging di browser console:

```javascript
// Di scanner.js, uncomment/tambah:
console.log("Decoded QR Text:", decodedText);
console.log("URL Params:", getUrlParams());
console.log("Return URL:", urlParams.dst);
```

## 📞 Support & Help

1. **Check Issues** di GitHub repository
2. **Search Documentation** di README.md
3. **Review Example** EXAMPLE_LOGIN_PAGE.php
4. **Check Browser Console** untuk error message
5. **Contact** via GitHub Issues

---

**Version:** 1.0  
**Last Updated:** February 2026
