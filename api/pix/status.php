<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

$id = $_GET['transactionId'] ?? '';

if (!$id || !isset($_SESSION['transacoes'][$id])) {
    echo json_encode(['status' => 'pending']);
    exit;
}

$transacao = $_SESSION['transacoes'][$id];
$status = $transacao['status'];

// 1. Se for transação real do gateway Fyntra, consulta a API
if (isset($transacao['simulado']) && !$transacao['simulado']) {
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, FYNTRA_API_URL . '/transactions/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . FYNTRA_API_KEY,
            'User-Agent: AtivoB2B/1.0'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($http_code === 200 && !empty($response)) {
            $res_data = json_decode($response, true);
            $tx_data = $res_data['data'] ?? $res_data;
            
            if (!empty($tx_data['status'])) {
                $status_fyntra = strtolower($tx_data['status']);
                if (in_array($status_fyntra, ['paid', 'approved', 'success', 'succeeded'])) {
                    $status = 'approved';
                } elseif (in_array($status_fyntra, ['failed', 'canceled', 'expired', 'refunded'])) {
                    $status = 'failed';
                }
                $_SESSION['transacoes'][$id]['status'] = $status;
            }
        }
    } catch (Exception $e) {
        // falha silenciosa
    }
} else {
    // 2. Simulação local após 15 segundos
    if ($status === 'pending' && (time() - $transacao['created_at']) > 15) {
        $_SESSION['transacoes'][$id]['status'] = 'approved';
        $status = 'approved';
    }
}

// Retorna objeto e array para cobertura total de formatos da API cliente
echo json_encode([
    'status' => $status,
    'transaction_id' => $id,
    'id' => $id,
    [
        'status' => $status
    ]
]);
