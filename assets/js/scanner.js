// scanner.js
function bubbleChat(lines, containerId, delay = 600) {
  const container = document.getElementById(containerId);
  if (!container) return;
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

/**
 * Extract voucher from decoded QR text.
 * Strategy:
 *  1) check query params: ?voucher=... or ?username=...
 *  2) check repeated token pattern: token...token (eg thrdp4passwordthrdp4 -> thrdp4)
 *  3) take longest alphanumeric token >=4
 *  4) fallback: trim and remove whitespace
 */
function extractVoucher(decodedText) {
  if (!decodedText) return "";

  const s = decodedText.trim();

  // 1) query param voucher or username
  try {
    const urlLike = new URL(s);
    const params = urlLike.searchParams;
    if (params.has('voucher')) return params.get('voucher');
    if (params.has('username')) return params.get('username');
  } catch (e) {
    // not a full URL, try to find query-like fragments inside string
    const qpMatch = s.match(/[?&](?:voucher|username)=([^&\s]+)/i);
    if (qpMatch && qpMatch[1]) return decodeURIComponent(qpMatch[1]);
  }

  // 2) repeated token pattern (token ... same token)
  const repeatedMatch = s.match(/([A-Za-z0-9]{3,})[\s\S]{1,30}\1/);
  if (repeatedMatch && repeatedMatch[1]) return repeatedMatch[1];

  // 3) longest alphanumeric token >=4
  const tokens = s.match(/[A-Za-z0-9]{4,}/g);
  if (tokens && tokens.length) {
    tokens.sort((a, b) => b.length - a.length);
    return tokens[0];
  }

  // 4) fallback: return trimmed non-space sequence
  return s.replace(/\s+/g, "");
}

/**
 * startScanner options:
 *   { redirect: false }  -> default: do NOT redirect, only show & copy voucher
 *   { redirect: true }   -> will redirect to Mikrotik using detected host/referrer
 */
function startScanner(opts = {}) {
  const { redirect = false } = opts;

  // prefer Html5Qrcode already loaded on page
  const qrReader = new Html5Qrcode("qr-reader");
  qrReader.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 200 },
    (decodedText) => {
      const voucher = extractVoucher(decodedText);

      if (!voucher) {
        bubbleChat(["⚠️ Gagal ekstrak kode voucher. Coba lagi."], "welcomeMessage", 600);
        return;
      }

      bubbleChat([`✅ Kode voucher: <b>${voucher}</b>`], "welcomeMessage", 600);

      // dispatch custom event so login page can catch it if needed
      try {
        const ev = new CustomEvent('voucherDetected', { detail: { voucher } });
        window.dispatchEvent(ev);
      } catch (e) { /* ignore */ }

      if (!redirect) {
        // copy to clipboard and show
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(voucher).then(() => {
            bubbleChat(["✔️ Kode disalin ke clipboard. Tempel di halaman login jika perlu."], "welcomeMessage", 700);
          }).catch(() => {
            bubbleChat([`⚠️ Tidak bisa auto-copy — salin manual: <code>${voucher}</code>`], "welcomeMessage", 700);
          });
        } else {
          bubbleChat([`📋 Silakan salin manual: <code>${voucher}</code>`], "welcomeMessage", 700);
        }
        qrReader.stop().catch(()=>{});
        return;
      }

      // redirect flow: build mikrotik host (from referrer -> location -> fallback)
      setTimeout(() => {
        let mikrotikHost = null;
        try {
          if (document.referrer) {
            const refHost = new URL(document.referrer).hostname;
            if (refHost && refHost !== location.hostname) mikrotikHost = refHost;
          }
        } catch (e) {}
        if (!mikrotikHost) mikrotikHost = location.hostname || "192.168.88.1";

        // Redirect ONLY with voucher as username (and static password 'qr' if needed)
        const url = `http://${mikrotikHost}/login?username=${encodeURIComponent(voucher)}&password=qr`;

        // final message then redirect
        bubbleChat([`🔁 Mengalihkan ke: <code>${mikrotikHost}</code>`], "welcomeMessage", 700);
        qrReader.stop().then(() => {
          window.location.href = url;
        }).catch(() => {
          // if stop fails, still try redirect
          window.location.href = url;
        });
      }, 900);
    },
    (errorMessage) => {
      // silent fail; optionally show last error for debugging
      // console.debug("QR error:", errorMessage);
    }
  ).catch(err => {
    // failed to start camera/qr reader
    bubbleChat([`⚠️ Gagal akses kamera / QR reader: ${err}`], "welcomeMessage", 600);
  });
}
