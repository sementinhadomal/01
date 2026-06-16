<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Payload inválido.']);
    exit;
}

$cpf   = preg_replace('/\D/', '', $data['cpf'] ?? '');
$nome  = $data['name'] ?? 'Contribuinte';
$email = !empty($data['email']) ? $data['email'] : 'contribuinte@gov.br';
$phone = preg_replace('/\D/', '', $data['phone'] ?? '');
$valorTotalStr = $data['valorTotal'] ?? 'R$ 94,01';

// Converte valor para float e depois para centavos (int)
$valorFloat    = (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $valorTotalStr));
$amountCentavos = (int) round($valorFloat * 100);

if (!defined('FYNTRA_API_KEY') || empty(FYNTRA_API_KEY)) {
    http_response_code(500);
    echo json_encode(['error' => 'Gateway de pagamento não configurado.']);
    exit;
}

$payload = [
    'amount'        => $amountCentavos,
    'paymentMethod' => 'PIX',
    'customer'      => [
        'name'     => $nome,
        'email'    => $email,
        'document' => [
            'number' => $cpf,
            'type'   => 'CPF'
        ],
        'phone' => $phone ?: '5511999999999'
    ],
    'items' => [
        [
            'title'     => defined('PRODUTO_NOME') ? PRODUTO_NOME : 'Regularização CPF Gov BR',
            'unitPrice' => $amountCentavos,
            'quantity'  => 1,
            'tangible'  => false
        ]
    ],
    'pix' => [
        'expiresInDays' => 1
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, FYNTRA_API_URL . '/transactions');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 12);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . FYNTRA_API_KEY,
    'User-Agent: AtivoB2B/1.0'
]);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code !== 200 || empty($response)) {
    http_response_code(502);
    echo json_encode(['error' => 'Erro ao se comunicar com o gateway de pagamento. Tente novamente.']);
    exit;
}

$res_data = json_decode($response, true);
$tx_data  = $res_data['data'] ?? $res_data;

$transaction_id = $tx_data['id'] ?? $tx_data['transaction_id'] ?? '';
$pix_code       = $tx_data['qrCode'] ?? $tx_data['pix_code'] ?? $tx_data['pixCode'] ?? '';

if (empty($pix_code)) {
    http_response_code(502);
    echo json_encode(['error' => 'Gateway não retornou o código PIX. Tente novamente.']);
    exit;
}

// Salva na sessão (sem simulação)
$_SESSION['transacoes'][$transaction_id] = [
    'cpf'        => $cpf,
    'nome'       => $nome,
    'valor'      => $valorFloat,
    'status'     => 'pending',
    'simulado'   => false,
    'created_at' => time()
];

echo json_encode([
    'pixCode'        => $pix_code,
    'pix_code'       => $pix_code,
    'transaction_id' => $transaction_id,
    'id'             => $transaction_id,
    'status'         => 'pending'
]);
