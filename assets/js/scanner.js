let qrReader;
let loginAttempted = false;

// Get URL parameters
function getUrlParams() {
  const params = new URLSearchParams(window.location.search);
  return {
    dst: params.get("dst"),           // Return URL ke login page
    mode: params.get("mode") || "qr", // Login mode (qr, voucher, member)
    username: params.get("username") || "",
    token: params.get("token") || ""
  };
}

function bubbleChat(lines, containerId, delay = 600) {
  const container = document.getElementById(containerId);
  lines.forEach((line, i) => {
    setTimeout(() => {
      const bubble = document.createElement("div");
      bubble.className = "chat-bubble";
      bubble.innerHTML = line;
      container.appendChild(bubble);
      container.scrollTop = container.scrollHeight;
    }, i * delay);
  });
}

function parseQRCredentials(qrText) {
  // Format QR bisa:
  // 1. "username:password"
  // 2. "username" (password kosong)
  // 3. JSON format untuk advanced: {"username":"user","password":"pass","type":"qr"}
  
  if (qrText.startsWith("{")) {
    try {
      return JSON.parse(qrText);
    } catch (e) {
      console.error("Invalid JSON in QR:", e);
    }
  }

  const parts = qrText.split(":");
  return {
    username: parts[0] || "",
    password: parts[1] || "",
    type: "qr"
  };
}

function sendCredentialToLoginPage(username, password) {
  const urlParams = getUrlParams();
  const returnUrl = urlParams.dst;

  if (!returnUrl) {
    bubbleChat([
      "❌ Error: Return URL tidak ditemukan!",
      "Parameter 'dst' diperlukan.",
      "Hubungi admin untuk info lebih lanjut"
    ], "welcomeMessage", 700);
    
    setTimeout(() => {
      retryScanner();
    }, 3000);
    return;
  }

  bubbleChat([
    "✅ Credential diterima",
    "⏳ Redirect ke login page"
  ], "welcomeMessage", 700);

  // Buat URL dengan query parameter
  const redirectUrl = new URL(returnUrl);
  redirectUrl.searchParams.append("qr_username", username);
  redirectUrl.searchParams.append("qr_password", password);
  redirectUrl.searchParams.append("mode", "qr");
  redirectUrl.searchParams.append("qr_timestamp", new Date().toISOString());
  redirectUrl.searchParams.append("qr_source", "scanner");

  console.log("Redirect ke:", redirectUrl.toString());

  // Redirect ke login page
  setTimeout(() => {
    window.location.href = redirectUrl.toString();
  }, 1000);
}

function startScanner() {
  if (loginAttempted) return;
  
  qrReader = new Html5Qrcode("qr-reader");
  qrReader.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 200 },
    (decodedText) => {
      loginAttempted = true;
      qrReader.stop();
      
      // Parse credentials dari QR
      const { username, password } = parseQRCredentials(decodedText);
      
      if (!username) {
        bubbleChat([
          "❌ QR tidak valid!",
          "Format: username:password"
        ], "welcomeMessage", 700);
        
        loginAttempted = false;
        setTimeout(() => {
          startScanner();
        }, 2000);
        return;
      }

      bubbleChat([
        `✅ Kode terdeteksi`,
        `👤 User: <b>${username}</b>`,
        "🔐 Processing..."
      ], "welcomeMessage", 700);

      // Send credential ke login page
      setTimeout(() => {
        sendCredentialToLoginPage(username, password);
      }, 1000);
    },
    (errorMessage) => {
      // Ignore error messages
    }
  );
}

function retryScanner() {
  loginAttempted = false;
  document.getElementById("welcomeMessage").innerHTML = "";
  startScanner();
}

// Show URL info
function showUrlInfo() {
  const urlParams = getUrlParams();
  console.log("Redirect URL (dst):", urlParams.dst);
  console.log("Mode:", urlParams.mode);
  
  if (!urlParams.dst) {
    console.warn("⚠️ Warning: Parameter 'dst' tidak ditemukan. Scanner akan tidak bisa redirect.");
  }
}
