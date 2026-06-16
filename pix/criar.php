<?php
session_start();
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$cpf = preg_replace('/\D/', '', $data['cpf'] ?? '');
$nome = $data['name'] ?? 'Contribuinte';
$valorTotalStr = $data['valorTotal'] ?? 'R$ 94,01';

// Limpa o valor para float
$valorLimpo = (float)str_replace(',', '.', preg_replace('/[^\d,]/', '', $valorTotalStr));

// Gera um ID de transação simulado
$transaction_id = 'rfb_' . md5(uniqid($cpf, true));

// PIX Copia e Cola estático simulado de arrecadação (padrão BRCode do Banco Central)
$pix_code = "00020101021226840014br.gov.bcb.pix25620014gov.br/receita5204000053039865405" . number_format($valorLimpo, 2, '.', '') . "5802BR5925SECRETARIA RECEITA FEDERAL6008BRASILIA62070503***6304" . strtoupper(substr(md5($transaction_id), 0, 4));

// Salva na sessão
$_SESSION['transacoes'][$transaction_id] = [
    'cpf' => $cpf,
    'nome' => $nome,
    'valor' => $valorLimpo,
    'status' => 'pending',
    'created_at' => time()
];

echo json_encode([
    'pixCode' => $pix_code,
    'pix_code' => $pix_code,
    'transaction_id' => $transaction_id,
    'id' => $transaction_id,
    'status' => 'pending'
]);
