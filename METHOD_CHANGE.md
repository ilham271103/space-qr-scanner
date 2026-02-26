# ✅ UPDATE: Method GET (Query Parameter)

## What Changed?

### Previous (POST Method)
```
Scanner → Form POST submission (hidden form)
→ Login Page process form data
→ Redirect on success
```

**Pros:** Data tidak terlihat di URL  
**Cons:** Complex form handling, CORS issues

---

### Current (GET Method) ✨ NEW
```
Scanner → URL Redirect dengan Query Parameter
→ Login Page extract parameter dari URL
→ Process & Redirect
```

**Pros:** Simple, no CORS issues, lightweight  
**Cons:** Data visible di URL (tapi temporary)

---

## URL Format

### Before (POST)
```
[Form submission di background]
```

### Now (GET) ✨ NEW
```
http://localhost:8090/login.php?
  qr_username=admin&
  qr_password=password123&
  mode=qr&
  qr_timestamp=2024-02-26T10:30:00Z&
  qr_source=scanner
```

---

## How to Handle in Login Page

### PHP (Recommended)

```php
<?php
// Cek apakah dari QR scanner
if (isset($_GET['mode']) && $_GET['mode'] === 'qr') {
    // Get parameter dari URL
    $username = $_GET['qr_username'] ?? '';
    $password = $_GET['qr_password'] ?? '';
    $timestamp = $_GET['qr_timestamp'] ?? '';
    
    // Validation
    if (empty($username) || empty($password)) {
        die("Username atau password tidak boleh kosong!");
    }
    
    // Process login
    $result = loginToMikrotik($username, $password);
    
    if ($result['success']) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_mode'] = 'qr';
        $_SESSION['login_time'] = $timestamp;
        
        // Redirect ke dashboard
        header("Location: /admin");
        exit;
    } else {
        $_SESSION['error'] = "Login gagal!";
        header("Location: /login.php");
        exit;
    }
}
?>
```

### JavaScript

```javascript
// Baca parameter dari URL
const params = new URLSearchParams(window.location.search);
const username = params.get('qr_username');
const password = params.get('qr_password');
const mode = params.get('mode');

if (mode === 'qr') {
    console.log("Login via QR:", username);
    // Process dengan AJAX atau form submission
}
```

---

## Query Parameter Details

| Parameter | Example | Description |
|-----------|---------|-------------|
| `qr_username` | `admin` | Username dari QR code |
| `qr_password` | `p@ss123` | Password dari QR code |
| `mode` | `qr` | Mode login (always "qr") |
| `qr_timestamp` | `2024-02-26T10:30:00Z` | Kapan QR di-scan |
| `qr_source` | `scanner` | Sumber scan (always "scanner") |

---

## Important Notes

### ⚠️ URL Encoding
Special character akan di-encode automatically oleh browser:
- `:` → `%3A`
- `&` → `%26`
- `=` → `%3D`
- ` ` → `%20`

PHP otomatis decode, jadi tidak perlu worry.

### ⚠️ Security
1. **Use HTTPS** - GitHub Pages automatically HTTPS
2. **Validate Length** - Jangan accept terlalu panjang parameter
3. **Sanitize Input** - Escape/filter input dari user
4. **Rate Limiting** - Limit attempts per IP

### ⚠️ Browser Limits
Some browsers memiliki limit panjang URL (biasanya 2048 characters), tapi credential tidak akan panjang jadi tidak masalah.

---

## Testing

### Local Test
```bash
# Scanner at localhost:8000
http://localhost:8000/?dst=http://localhost:8090/login.php

# After scan, will redirect to:
http://localhost:8090/login.php?qr_username=test&qr_password=123&...
```

### Debug Tips
```javascript
// Di browser console:
console.log(window.location.href);  // Lihat full URL
console.log(window.location.search); // Lihat query string
```

---

## Comparison: POST vs GET

| Aspect | POST | GET |
|--------|------|-----|
| **Data Visibility** | Hidden | Visible in URL |
| **URL Length Limit** | None | ~2048 chars |
| **Secure** | Better | Okay for non-sensitive |
| **Complexity** | Form handling | Simple parameter |
| **Caching** | No | Yes (URL) |
| **Browser History** | No clear | Shows in history |
| **CORS** | Required | Not needed |
| **Speed** | Slower | Faster |

**Kami pilih GET karena SIMPLE & Tidak perlu CORS config!**

---

Version: 1.1 (Updated to GET method)
