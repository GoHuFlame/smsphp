<?php
// api/sms.php
// Recibe JSON { to: ["346..."], from: "SENDER", message: "text" }
// Llama a 360NRS POST {{DASHBOARD_HOST}}/api/rest/sms

header('Content-Type: application/json; charset=utf-8');

// permitir CORS desde mismo origen si hace falta
// header('Access-Control-Allow-Origin: https://tu-dominio.vercel.app'); // opcional
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // preflight
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['to']) || !isset($data['from']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request: faltan campos (to, from, message)']);
    exit;
}

// CONFIG: modifica DASHBOARD_HOST si tu cuenta usa otro host.
// Por defecto usamos dashboard.360nrs.com (si en tu cuenta tu host es distinto, reemplaza).
$dashboardHost = getenv('DASHBOARD_HOST');

// AUTORIZACIÓN: token base64. Recomendado: guardarlo en variable de entorno en Vercel.
// Si no existe, usa el token que me diste (solo para pruebas).
$authTokenBase64 = getenv('AUTH_TOKEN_BASE64');

// Construir payload JSON (la API espera "to" como array)
$payload = [
    'to' => $data['to'],
    'from' => $data['from'],
    'message' => $data['message']
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, rtrim($dashboardHost, '/') . '/api/rest/sms');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . $authTokenBase64
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $err = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . $err]);
    exit;
}
curl_close($ch);

// reenviamos directamente la respuesta de 360NRS al front
http_response_code($httpCode ?: 200);
echo $response;
