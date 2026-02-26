# QR Code Scanner - Mikrotik Login Integration

![QR Scanner](https://img.shields.io/badge/Version-1.0-blue)
![License](https://img.shields.io/badge/License-MIT-green)

QR Code scanner halaman yang di-design khusus untuk integrasi dengan login page Mikrotik. Support multiple login modes: QR Code, Voucher, dan Member.

## 🎯 Flow Aplikasi

```
Mikrotik Login Page 
    ↓ (redirect dengan ?dst=URL)
QR Scanner Page (di GitHub Pages)
    ↓ (scan QR)
Parse Credential
    ↓ (GET redirect dengan query param)
Mikrotik Login Page (receive & process)
    ↓
Mikrotik Dashboard / Redirect URL
```

## 🚀 Fitur Utama

- ✅ **QR Code Scanner** - Real-time scanning menggunakan html5-qrcode.js
- ✅ **Multiple Format Support** - `username:password` atau JSON format
- ✅ **POST Redirect** - Kirim credential kembali ke login page
- ✅ **Settings Panel** - Konfigurasi dinamis tanpa perlu edit kode
- ✅ **Responsive Design** - Mobile & Desktop optimized
- ✅ **GitHub Pages Ready** - Deploy langsung ke GitHub Pages tanpa perlu server

## 📋 Prerequisites

- Modern browser dengan akses camera
- Login page Mikrotik yang sudah jadi (support multiple modes)
- GitHub account untuk hosting (optional)

## 🔧 Setup & Deployment

### 1. Clone Repository

```bash
git clone https://github.com/username/scanner-qr.git
cd scanner-qr
```

### 2. Deploy ke GitHub Pages

Jika ingin deploy ke GitHub Pages:

1. Go to repository settings → Pages
2. Set source: `Branch: main` → `/ (root)`
3. Domain akan jadi: `https://username.github.io/scanner-qr/`

### 3. Update Login Page Mikrotik

Di halaman login Mikrotik Anda, tambahkan button untuk QR mode:

```html
<!-- Tombol QR Code -->
<a href="https://username.github.io/scanner-qr/?dst=http://localhost:8090/login" 
   class="btn btn-qr">
   📱 Login dengan QR Code
</a>
```

Atau jika local testing:

```html
<a href="http://your-local-ip/scanner-qr/?dst=http://localhost:8090/login">
   📱 Login dengan QR Code
</a>
```

## 📦 URL Parameters

| Parameter | Deskripsi | Required | Contoh |
|-----------|-----------|----------|--------|
| `dst` | Return URL ke login page | ✅ Yes | `?dst=http://localhost:8090/login` |
| `mode` | Login mode | ❌ No | `?mode=qr` |
| `username` | Pre-fill username | ❌ No | `?username=admin` |
| `token` | Additional token | ❌ No | `?token=abc123` |

## 🎫 Format QR Code

### Format Sederhana
```
admin:password123
```

Atau hanya username (password kosong):
```
admin
```

### Format JSON (Advanced)
```json
{
  "username": "admin",
  "password": "password123",
  "type": "qr",
  "extra": "data-tambahan"
}
```

## 📤 Data yang Dikirim ke Login Page

Setelah scan, scanner melakukan redirect ke URL dengan query parameter:

```
GET /login.php?qr_username=admin&qr_password=password123&mode=qr&qr_timestamp=...&qr_source=scanner
```

**Parameter yang dikirim:**

| Parameter | Contoh | Keterangan |
|-----------|--------|-----------|
| `qr_username` | `admin` | Username dari QR code |
| `qr_password` | `password123` | Password dari QR code |
| `mode` | `qr` | Mode login (always "qr") |
| `qr_timestamp` | `2024-02-26T...` | Timestamp scan |
| `qr_source` | `scanner` | Sumber scan |

### Contoh Handle di Login Page (PHP):

```php
<?php
if (isset($_GET['mode']) && $_GET['mode'] === 'qr') {
    // QR Mode
    $username = $_GET['qr_username'] ?? '';
    $password = $_GET['qr_password'] ?? '';
    $timestamp = $_GET['qr_timestamp'] ?? '';
    
    // Handle login
    $login_result = loginUser($username, $password, 'qr');
    
    if ($login_result['success']) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_mode'] = 'qr';
        header("Location: /admin");
    } else {
        $error = "Login gagal";
    }
}
?>
```

## 🔐 Security Best Practices

1. **Gunakan HTTPS** di production
2. **QR Code Encryption** - Encode credential dengan encryption jika sensitive
3. **CORS Configuration** - Set proper CORS headers di login page
4. **Rate Limiting** - Limit scan attempts
5. **Session Management** - Implement proper session handling

## 🎨 Customization

### Edit Styling

File: `assets/css/style.css`

```css
/* Change color theme */
:root {
  --primary-color: #00f0ff;
  --secondary-color: #667eea;
}
```

### Modify Messages

File: `assets/js/scanner.js`

```javascript
bubbleChat([
  "Custom message 1",
  "Custom message 2"
], "welcomeMessage", 700);
```

## 📱 Testing Locally

### Setup Local Server

Using Python:
```bash
python -m http.server 8000
```

Using PHP:
```bash
php -S localhost:8000
```

Using Node.js:
```bash
npx http-server
```

Akses: `http://localhost:8000/?dst=http://your-login-page/login`

## 🐛 Troubleshooting

### Camera tidak muncul
- Check browser permissions untuk camera
- Gunakan HTTPS jika di production
- Test di browser yang support WebRTC

### Scan tidak terdeteksi
- Pastikan QR code format sesuai
- Cek console browser untuk error message
- Coba perbaikan lighting/angle

### Redirect tidak bekerja
- Verify parameter `dst` ada dan valid
- Check CORS policy di login page
- Gunakan POST method untuk form submission

## 📄 File Structure

```
scanner-qr/
├── index.html              # Main HTML
├── README.md              # Documentation
├── assets/
│   ├── css/
│   │   ├── style.css      # Main styling
│   │   └── fontawesome/   # Icons
│   ├── js/
│   │   ├── scanner.js     # Main logic
│   │   └── html5-qrcode.min.js
│   └── img/
│       ├── planet.webp
│       └── ...
```

## 🔗 Related Projects

- [html5-qrcode](https://github.com/mebjas/html5-qrcode) - QR Code library
- [Mikrotik RouterOS](https://mikrotik.com/download) - RouterOS

## 📝 License

MIT License - feel free to use and modify

## 💬 Support

For issues and questions:
1. Check GitHub Issues
2. Review documentation
3. Create new issue dengan detail error

## 👨‍💻 Contributing

Contributions welcome! Fork dan submit pull request.

---

**Version:** 1.0.0  
**Last Updated:** February 2026  
**Author:** Your Name
