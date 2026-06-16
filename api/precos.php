<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

echo json_encode([
    'valores_anos' => [
        '2020' => VALOR_2020 / 100,
        '2021' => VALOR_2021 / 100,
        '2022' => VALOR_2022 / 100,
        '2023' => VALOR_2023 / 100,
        '2024' => VALOR_2024 / 100,
        '2025' => VALOR_2025 / 100
    ],
    'produto_nome' => PRODUTO_NOME
]);
