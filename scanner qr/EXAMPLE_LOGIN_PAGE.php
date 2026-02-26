<?php
/**
 * CONTOH LOGIN PAGE YANG SUPPORT MULTIPLE MODE
 * Mode: Voucher, Member, dan QR Code
 * 
 * File ini menunjukkan cara mengintegrasikan QR Scanner
 * Simpan sebagai: login.php atau sesuaikan dengan login page Anda
 */

session_start();

// Inisialisasi variabel
$error_message = '';
$success_message = '';
$login_mode = 'default'; // default, voucher, member, qr
$username_prefill = '';
$password_prefill = '';

// ============================================
// CEK REQUEST METHOD DAN MODE
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Mode 1: QR CODE LOGIN
    if (isset($_POST['mode']) && $_POST['mode'] === 'qr') {
        $login_mode = 'qr';
        $username = $_POST['qr_username'] ?? '';
        $password = $_POST['qr_password'] ?? '';
        
        // Optional: Parse QR data JSON jika ada
        if (isset($_POST['qr_data'])) {
            $qr_data = json_decode($_POST['qr_data'], true);
            // Bisa tambahan processing dari QR data
            // Contoh: check timestamp, validate signature, etc
        }
        
        // IMPLEMENT: Login ke Mikrotik dengan credential
        if ($username && $password) {
            $login_result = loginToMikrotik($username, $password);
            
            if ($login_result['success']) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['login_mode'] = 'qr';
                
                $success_message = "✅ Login berhasil! Mode: QR Code";
                
                // Redirect ke dashboard atau Mikrotik
                header("Location: /admin");
                exit;
            } else {
                $error_message = "❌ Login gagal: " . $login_result['error'];
            }
        } else {
            $error_message = "❌ Username atau password kosong";
        }
    }
    
    // Mode 2: VOUCHER LOGIN
    else if (isset($_POST['mode']) && $_POST['mode'] === 'voucher') {
        $login_mode = 'voucher';
        $voucher_code = $_POST['voucher_code'] ?? '';
        
        if ($voucher_code) {
            $voucher_result = validateVoucher($voucher_code);
            
            if ($voucher_result['valid']) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'voucher';
                $_SESSION['voucher_code'] = $voucher_code;
                $_SESSION['voucher_data'] = $voucher_result;
                
                $success_message = "✅ Login voucher berhasil!";
                
                header("Location: /admin");
                exit;
            } else {
                $error_message = "❌ Voucher tidak valid atau sudah kadaluarsa";
            }
        } else {
            $error_message = "❌ Masukkan kode voucher";
        }
    }
    
    // Mode 3: MEMBER LOGIN (username/password standard)
    else if (isset($_POST['username']) && isset($_POST['password'])) {
        $login_mode = 'member';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username && $password) {
            $login_result = loginToMikrotik($username, $password);
            
            if ($login_result['success']) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['login_mode'] = 'member';
                
                $success_message = "✅ Login berhasil! Mode: Member";
                
                header("Location: /admin");
                exit;
            } else {
                $error_message = "❌ Username atau password salah";
                $username_prefill = htmlspecialchars($username);
            }
        } else {
            $error_message = "❌ Username dan password harus diisi";
        }
    }
}

// ============================================
// FUNCTION HELPER
// ============================================

/**
 * Login ke Mikrotik menggunakan API
 * Customize sesuai dengan setup Mikrotik Anda
 */
function loginToMikrotik($username, $password) {
    $mikrotik_host = '192.168.88.1';
    $mikrotik_port = 8728;
    
    try {
        // Implementasi sesuai metode Anda:
        // 1. Direct socket connection
        // 2. REST API
        // 3. Hotspot API
        // 4. Database query
        
        // CONTOH: Simple database check
        if ($username === 'admin' && $password === 'admin') {
            return [
                'success' => true,
                'user_id' => 1,
                'role' => 'admin'
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Credential tidak cocok'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Validasi voucher
 */
function validateVoucher($code) {
    // Implement logic untuk validasi voucher
    // Connect ke database atau API voucher Anda
    
    // CONTOH:
    $valid_vouchers = [
        'VOUCHER-001' => ['data' => 'unlimited', 'days' => 30],
        'VOUCHER-002' => ['data' => '5GB', 'days' => 7],
    ];
    
    if (isset($valid_vouchers[$code])) {
        return [
            'valid' => true,
            'voucher_data' => $valid_vouchers[$code]
        ];
    }
    
    return ['valid' => false];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mikrotik Hotspot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .mode-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .mode-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            border: none;
            background: none;
            color: #999;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .mode-tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .mode-tab:hover {
            color: #667eea;
        }
        
        .mode-content {
            display: none;
        }
        
        .mode-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #f0f0f0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        .success-message {
            background: #efe;
            color: #0a0;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        
        .success-message.show {
            display: block;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .qr-button {
            width: 100%;
            padding: 14px;
            background: #00f0ff;
            color: #333;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .qr-button:hover {
            background: #00d4ff;
            transform: translateY(-2px);
        }
        
        .qr-scanner-url {
            display: none;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 12px;
            word-break: break-all;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Hotspot Login</h1>
            <p>Pilih metode login</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message show"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="success-message show"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <!-- MODE TABS -->
        <div class="mode-tabs">
            <button class="mode-tab active" data-mode="member">
                👤 Member
            </button>
            <button class="mode-tab" data-mode="voucher">
                🎫 Voucher
            </button>
            <button class="mode-tab" data-mode="qr">
                📱 QR Code
            </button>
        </div>
        
        <!-- MODE 1: MEMBER LOGIN -->
        <form method="POST" class="mode-content active" id="member-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo $username_prefill; ?>" 
                       placeholder="Masukkan username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="submit-btn">Login Member</button>
        </form>
        
        <!-- MODE 2: VOUCHER LOGIN -->
        <form method="POST" class="mode-content" id="voucher-form">
            <input type="hidden" name="mode" value="voucher">
            
            <div class="form-group">
                <label for="voucher_code">Kode Voucher</label>
                <input type="text" id="voucher_code" name="voucher_code" 
                       placeholder="Cth: VOUCHER-001" 
                       style="text-transform: uppercase;">
            </div>
            
            <button type="submit" class="submit-btn">Aktifkan Voucher</button>
        </form>
        
        <!-- MODE 3: QR CODE LOGIN -->
        <div class="mode-content" id="qr-form">
            <div style="text-align: center; padding: 20px;">
                <p style="color: #666; margin-bottom: 20px;">
                    Klik tombol di bawah untuk membuka QR Scanner
                </p>
                
                <button type="button" class="qr-button" id="open-qr-btn">
                    📱 Buka QR Scanner
                </button>
                
                <div class="qr-scanner-url" id="qr-scanner-url"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Mode switching
        document.querySelectorAll('.mode-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                
                // Update active tab
                document.querySelectorAll('.mode-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show corresponding form
                document.querySelectorAll('.mode-content').forEach(form => form.classList.remove('active'));
                
                if (mode === 'member') {
                    document.getElementById('member-form').classList.add('active');
                } else if (mode === 'voucher') {
                    document.getElementById('voucher-form').classList.add('active');
                } else if (mode === 'qr') {
                    document.getElementById('qr-form').classList.add('active');
                }
            });
        });
        
        // QR Scanner Button
        document.getElementById('open-qr-btn').addEventListener('click', function() {
            // URL ke scanner QR (sesuaikan dengan lokasi Anda)
            const scannerUrl = 'https://username.github.io/scanner-qr/?dst=' + 
                             encodeURIComponent(window.location.href);
            
            // Show URL
            const urlElement = document.getElementById('qr-scanner-url');
            urlElement.textContent = scannerUrl;
            urlElement.style.display = 'block';
            
            // Open in new window/tab
            window.open(scannerUrl, 'QR_Scanner', 'width=500,height=700');
        });
    </script>
</body>
</html>
