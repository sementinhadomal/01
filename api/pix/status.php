<?php
// Vercel: NÃO usa session_start() — serverless não tem sessões persistentes
require_once '../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$id = $_GET['transactionId'] ?? '';

if (empty($id)) {
    echo json_encode(['status' => 'pending']);
    exit;
}

// Consulta o status direto na Fyntra (sem depender de sessão)
if (!defined('FYNTRA_API_KEY') || empty(FYNTRA_API_KEY)) {
    echo json_encode(['status' => 'pending']);
    exit;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,            FYNTRA_API_URL . '/transactions/' . $id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT,        8);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER,     [
    'Content-Type: application/json',
    'Accept: application/json',
    'x-api-key: ' . FYNTRA_API_KEY,
    'User-Agent: AtivoB2B/1.0'
]);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$status = 'pending';

if ($http_code === 200 && !empty($response)) {
    $res_data = json_decode($response, true);
    $tx_data  = $res_data['data'] ?? $res_data;

    if (!empty($tx_data['status'])) {
        $s = strtolower($tx_data['status']);
        if (in_array($s, ['paid', 'approved', 'success', 'succeeded', 'completed', 'waiting_payment'])) {
            // waiting_payment = gerou mas não pagou ainda = pending
            $status = in_array($s, ['paid', 'approved', 'success', 'succeeded', 'completed'])
                ? 'approved'
                : 'pending';
        } elseif (in_array($s, ['failed', 'canceled', 'cancelled', 'expired', 'refunded', 'refused'])) {
            $status = 'failed';
        }
    }
}

echo json_encode([
    'status'         => $status,
    'transaction_id' => $id
]);
