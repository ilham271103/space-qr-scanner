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

function extractVoucher(s) {
  if (!s) return "";

  const str = s.trim();

  // 1) kalau ada query param ?voucher= atau ?username= -> ambil nilainya
  const qp = str.match(/[?&](?:voucher|username)=([^&\s]+)/i);
  if (qp && qp[1]) return decodeURIComponent(qp[1]);

  // 2) pola token yang diulang (contoh: thrdp4passwordthrdp4)
  const repeated = str.match(/([A-Za-z0-9]{3,})[\s\S]{1,50}\1/);
  if (repeated && repeated[1]) return repeated[1];

  // 3) ambil token alfanumerik terpanjang (panjang minimal 4)
  const tokens = str.match(/[A-Za-z0-9]{4,}/g);
  if (tokens && tokens.length) {
    tokens.sort((a, b) => b.length - a.length);
    return tokens[0];
  }

  // fallback: bersihkan non-alnum
  return str.replace(/[^A-Za-z0-9]/g, "");
}

function startScanner() {
  const qrReader = new Html5Qrcode("qr-reader");
  qrReader.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 200 },
    (decodedText) => {
      const voucher = extractVoucher(decodedText);

      if (!voucher) {
        bubbleChat(["⚠️ Gagal deteksi kode voucher. Coba lagi."], "welcomeMessage", 600);
        return;
      }

      bubbleChat([`✅ Kode voucher: <b>${voucher}</b>`, "Mengalihkan ke login Mikrotik..."], "welcomeMessage", 700);

      // tentukan host Mikrotik: prioritas referrer -> lokasi -> fallback
      setTimeout(() => {
        let mikrotikHost = null;
        try {
          if (document.referrer) {
            const refHost = new URL(document.referrer).hostname;
            if (refHost) mikrotikHost = refHost;
          }
        } catch (e) {}

        if (!mikrotikHost || mikrotikHost === location.hostname) {
          mikrotikHost = location.hostname || "192.168.88.1";
        }

        const safeVoucher = encodeURIComponent(voucher);
        const url = `http://${mikrotikHost}/login?username=${safeVoucher}&password=qr`;

        // redirect
        window.location.href = url;
      }, 1000);

      qrReader.stop().catch(() => {}); // safe stop
    },
    (errorMessage) => {
      // silent fail
    }
  ).catch((e) => {
    console.error("Html5Qrcode start error:", e);
    bubbleChat(["⚠️ Gagal mengaktifkan kamera."], "welcomeMessage", 600);
  });
}
