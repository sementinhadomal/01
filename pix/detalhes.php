<?php
session_start();
header('Content-Type: application/json');

$id = $_GET['id'] ?? '';

if (!$id || !isset($_SESSION['transacoes'][$id])) {
    echo json_encode(['status' => 'pending']);
    exit;
}

$transacao = $_SESSION['transacoes'][$id];

// Simulação de aprovação após 15 segundos
if ($transacao['status'] === 'pending' && (time() - $transacao['created_at']) > 15) {
    $_SESSION['transacoes'][$id]['status'] = 'approved';
    $transacao['status'] = 'approved';
}

echo json_encode([
    'status' => $transacao['status'],
    'transaction_id' => $id,
    'id' => $id
]);
