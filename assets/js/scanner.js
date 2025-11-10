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

function startScanner() {
  const qrReader = new Html5Qrcode("qr-reader");
  qrReader.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 200 },
    (decodedText) => {
      bubbleChat([
        `✅ Kode terdeteksi: <b>${decodedText}</b>`,
        "Mengalihkan ke login Mikrotik..."
      ], "welcomeMessage", 700);

      setTimeout(() => {
        let mikrotikHost = null;

        if (document.referrer) {
          try {
            mikrotikHost = new URL(document.referrer).hostname;
          } catch(e){}
        }

        if (!mikrotikHost || mikrotikHost === "ilham271103.github.io") {
          mikrotikHost = location.hostname || "192.168.88.1";
        }

        window.location.href = `http://${mikrotikHost}/login?username=${encodeURIComponent(decodedText)}&password=qr`;
      }, 2000);

      qrReader.stop();
    },
    () => {}
  );
}
