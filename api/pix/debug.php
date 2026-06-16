<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

// Teste com valor fixo de R$ 94,01 = 9401 centavos
$payload = [
    'amount'        => 9401,
    'paymentMethod' => 'PIX',
    'customer'      => [
        'name'     => 'Teste Contribuinte',
        'email'    => 'teste@gov.br',
        'document' => [
            'number' => '00000000000',
            'type'   => 'CPF'
        ],
        'phone' => '5511999999999'
    ],
    'items' => [
        [
            'title'     => 'Regularização CPF Gov BR',
            'unitPrice' => 9401,
            'quantity'  => 1
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
$curl_err  = curl_error($ch);

// Retorna tudo para debug
echo json_encode([
    'debug'         => true,
    'http_code'     => $http_code,
    'curl_error'    => $curl_err,
    'fyntra_url'    => FYNTRA_API_URL . '/transactions',
    'api_key_set'   => !empty(FYNTRA_API_KEY),
    'payload_sent'  => $payload,
    'raw_response'  => $response,
    'parsed'        => json_decode($response, true)
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
