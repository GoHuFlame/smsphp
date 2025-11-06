<?php
// api/index.php
// Página principal HTML + JS para enviar SMS a través de 360NRS

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Enviar SMS - Prueba 360NRS</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #f7f8fa;
      color: #222;
      margin: 0;
      padding: 20px;
    }
    .card {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      padding: 24px;
    }
    h2 { margin-bottom: 10px; }
    label {
      display: block;
      font-weight: 600;
      margin-top: 15px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 15px;
    }
    button {
      margin-top: 18px;
      background: #1a73e8;
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 15px;
    }
    button:hover {
      background: #155ec2;
    }
    pre {
      background: #f3f4f6;
      padding: 12px;
      border-radius: 6px;
      overflow: auto;
      font-size: 13px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Enviar SMS (Prueba 360NRS)</h2>

    <form id="smsForm">
      <label for="to">Teléfono destino (con prefijo internacional)</label>
      <input id="to" name="to" placeholder="+34666555444" required />

      <label for="from">Remitente (de 3 a 11 caracteres)</label>
      <input id="from" name="from" placeholder="MiEmpresa" required />

      <label for="message">Mensaje</label>
      <textarea id="message" name="message" rows="4" maxlength="900" placeholder="Escribe tu mensaje..." required></textarea>

      <button type="submit">Enviar SMS</button>
    </form>

    <h3>Respuesta del servidor:</h3>
    <pre id="output">—</pre>
  </div>

<script>
document.getElementById('smsForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const to = document.getElementById('to').value.trim();
  const from = document.getElementById('from').value.trim();
  const message = document.getElementById('message').value.trim();
  const output = document.getElementById('output');

  if (!to || !from || !message) {
    output.textContent = "⚠️ Todos los campos son obligatorios.";
    return;
  }

  output.textContent = "⏳ Enviando mensaje...";

  try {
    const res = await fetch('/api/sms.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        to: [to],
        from,
        message
      })
    });

    // Capturamos texto antes de intentar convertirlo
    const text = await res.text();
    let data;

    try {
      data = JSON.parse(text);
    } catch {
      output.textContent = "⚠️ El servidor no devolvió JSON válido:\n\n" + text;
      return;
    }

    output.textContent = JSON.stringify(data, null, 2);

  } catch (err) {
    output.textContent = "❌ Error de conexión: " + err.message;
  }
});
</script>
</body>
</html>
