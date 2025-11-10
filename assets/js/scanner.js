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
  const mikrotikHost = window.location.hostname; // AUTO DETECT
  window.location.href = `http://${mikrotikHost}/login?username=${decodedText}&password=qr`;
}, 2000);

      qrReader.stop();
    },
    (errorMessage) => {}
  );
}

