<?php
// api/index.php
// Esta página devuelve HTML (front-end) con un formulario que llama a /api/sms.php
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Enviar SMS - prueba 360NRS</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; padding: 20px; background:#f6f7fb; color:#222; }
    .card{ max-width:600px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
    label{ display:block; margin-top:10px; font-weight:600; }
    input, textarea { width:100%; padding:10px; margin-top:6px; border:1px solid #ddd; border-radius:6px; }
    button{ margin-top:12px; padding:10px 16px; border:none; border-radius:6px; cursor:pointer; background:#1a73e8; color:white; }
    pre{ background:#f3f4f6; padding:10px; border-radius:6px; overflow:auto; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Enviar SMS (Prueba 360NRS)</h2>
    <form id="smsForm">
      <label>To (teléfono con prefijo, ej. 34666555444)</label>
      <input id="to" name="to" placeholder="34666555444" required />

      <label>From (remitente, hasta 11 alfanum o 15 números)</label>
      <input id="from" name="from" placeholder="TEST" required />

      <label>Mensaje (texto)</label>
      <textarea id="message" name="message" rows="4" maxlength="900" placeholder="Tu mensaje..." required></textarea>

      <button type="submit">Enviar SMS</button>
    </form>

    <h3>Respuesta</h3>
    <pre id="output">—</pre>
  </div>

<script>
document.getElementById('smsForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const to = document.getElementById('to').value.trim();
  const from = document.getElementById('from').value.trim();
  const message = document.getElementById('message').value.trim();

  const payload = { to: [ to ], from: from, message: message };

  document.getElementById('output').textContent = 'Enviando...';

  try {
    const res = await fetch('/api/sms.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    document.getElementById('output').textContent = JSON.stringify(data, null, 2);
  } catch (err) {
    document.getElementById('output').textContent = 'Error: ' + err.toString();
  }
});
</script>
</body>
</html>
