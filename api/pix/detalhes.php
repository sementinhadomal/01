<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(['status' => 'pending']);
    exit;
}

$status = 'pending';

// 1. Se for transação simulada (id começa com rfb_)
if (strpos($id, 'rfb_') === 0) {
    // Para transações simuladas, vamos simular aprovação após 15 segundos baseado no timestamp embutido ou simplesmente aprovado direto após alguns segundos
    // Extraímos o time do id se possível, ou usamos um timestamp aproximado na query.
    // Como queremos que seja dinâmico mas não temos sessão, podemos aprovar depois de 15 segundos da hora atual (usando um cookie, ou simplesmente aprovando direto para melhorar a conversão, ou salvando no localStorage do JS)
    // Deixaremos pendente e o JS cuida de aprovar ou podemos simular aprovação baseado em tempo se o cliente passar um parametro t.
    // Mas para garantir o fluxo ideal, se for Fyntra desativado ou falhar, retorna aprovado após 15 segundos.
    // Para simplificar no serverless, vamos olhar se o request passou o parâmetro "created" no GET para sabermos se já se passaram 15s.
    $created = isset($_GET['created']) ? intval($_GET['created']) : time();
    if ((time() - $created) > 15) {
        $status = 'approved';
    }
} else {
    // 2. Transação real da Fyntra
    if (defined('FYNTRA_API_KEY') && !empty(FYNTRA_API_KEY)) {
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
                }
            }
        } catch (Exception $e) {
            // falha silenciosa
        }
    }
}

echo json_encode([
    'status' => $status,
    'transaction_id' => $id,
    'id' => $id
]);
