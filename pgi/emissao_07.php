<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['tempo_login'])) {
    $_SESSION['tempo_login'] = time();
}

$tempo_online = time() - $_SESSION['tempo_login'];
$tempo_online_formatado = ($tempo_online < 3600)
    ? "Tempo Logado: " . floor($tempo_online / 60) . " minutos"
    : "Tempo Logado: " . sprintf("%02d:%02d horas", floor($tempo_online / 3600), floor(($tempo_online % 3600) / 60));



// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Se o botão de sair foi clicado
if (isset($_POST['sairnexus'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Conexão com banco de dados
require 'conexao_admpgi.php';
$tipo_cliente = '';

// Carrega dados do banco de dados usando o código de venda da sessão
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? ''; // Obtém o código de venda da sessão

if ($codigo_venda) {
    $query = "SELECT tipo_cliente FROM vendas WHERE codigo_venda = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $codigo_venda); // Usando o código de venda da sessão
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tipo_cliente = htmlspecialchars($row['tipo_cliente']);
    } else {
        $tipo_cliente = "Não encontrado";
    }

    $stmt->close();
}




// Incluiu a Validação de senha alterada - Primeiro Acesso!
require 'valida_entrada.php';

// Inclui o arquivo que carrega foto de perfil
require 'carregar_foto_perfil.php';
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);

// Recupera o código de venda da sessão
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? '';

// Se o código de venda mudar, limpa os dados da sessão para garantir consistência
if (isset($_POST['codigo_venda']) && $_POST['codigo_venda'] != $codigo_venda) {
    session_unset(); // Limpa a sessão quando mudar a venda
    $_SESSION['dados_emissao']['codigo_venda'] = $_POST['codigo_venda']; // Atualiza com o novo código de venda
}

// Recupera os dados da sessão salvos na página emissao_06
$dados_sessao = $_SESSION['dados_emissao'] ?? [];
$numero_portado_fixa = $dados_sessao['numeroPortadoFixa'] ?? '';
$operadora_doadora_fixa = $dados_sessao['operadoraDoadoraFixa'] ?? '';
$quantidade_chips = $dados_sessao['quantidadeChips'] ?? '';
$chips = $dados_sessao['chips'] ?? '';

// Define valores padrão para os campos baseados nos dados da sessão
$codigo_venda = $dados_sessao['codigo_venda'] ?? '';
$data_vencimento = $dados_sessao['data_vencimento'] ?? '';
$forma_pagamento = $dados_sessao['forma_pagamento'] ?? '';
$email_fatura = $dados_sessao['email_fatura'] ?? '';
$banco = $dados_sessao['banco'] ?? '';
$agencia = $dados_sessao['agencia'] ?? '';
$conta = $dados_sessao['conta'] ?? '';
$instancia = $dados_sessao['instancia'] ?? '';
$ordem_servico = $dados_sessao['ordem_servico'] ?? '';
$cnl = $dados_sessao['cnl'] ?? '';
$at = $dados_sessao['at'] ?? '';
$protocolo_smart_next = $dados_sessao['protocolo_smart_next'] ?? '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualiza a sessão com os dados enviados no formulário
    $_SESSION['dados_emissao'] = array_merge($dados_sessao, [
        'codigo_venda' => $_POST['codigo_venda'] ?? $codigo_venda,
        'data_vencimento' => $_POST['data-vencimento'] ?? $data_vencimento,
        'forma_pagamento' => $_POST['forma-pagamento'] ?? $forma_pagamento,
        'email_fatura' => $_POST['email-fatura'] ?? $email_fatura,
        'banco' => $_POST['banco'] ?? $banco,
        'agencia' => $_POST['agencia'] ?? $agencia,
        'conta' => $_POST['conta'] ?? $conta,
        'instancia' => $_POST['instancia'] ?? $instancia,
        'ordem_servico' => $_POST['ordem-servico'] ?? $ordem_servico,
        'cnl' => $_POST['cnl'] ?? $cnl,
        'at' => $_POST['at'] ?? $at,
        'protocolo_smart_next' => $_POST['protocolo-smart-next'] ?? $protocolo_smart_next,
    ]);

    // Redireciona conforme a ação
    if (isset($_POST['avancar'])) {
        header("Location: emissao_08.php");
        exit();
    }

    if (isset($_POST['voltar'])) {
        header("Location: emissao_06.php");
        exit();
    }
    if (isset($_POST['sair_venda'])) {
        header("Location: fila.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissão Venda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="table-stylesheet" rel="stylesheet" href="../css/tabelas.css">
    <link id="page-stylesheet" rel="stylesheet" href="../css/page-auditoria.css">
</head>
<body>
    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fi fi-br-bars-sort" id="toggle-icon" style="color: #697891; margin-right: 4px;"></i>
            <span class="menu-text" id="nexus_texto" style="display: none;">&nbsp;NEXUS</span>
        </button>


        <i class="fa-solid fa-house page-link" onclick="window.location.href='menu'">
            <span class="menu-text"><a href="menu" class="page-link">Página inicial</a></span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown">
            <i class="fi fi-sr-user-headset active" onclick="toggleDropdown()">
                <span class="menu-text" style="display: none;">Backoffice</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenu">
                <a href="paineltfp" class="page-link" onclick="window.location.href='paineltfp'">Painel TFP</a>
                <a href="#" class="page-link">Pós venda</a>
                <a href="fila" onclick="window.location.href='fila'" class="page-link">Fila de vendas</a>
            </div>
        </div>


        <i class="fa-solid fa-shapes">
            <span class="menu-text">Dashboards</span>
        </i>

        <i class="fa-solid fa-user-tie">
            <span class="menu-text">Comercial</span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown-rh">
            <i class="fa-solid fa-user-group" onclick="toggleDropdownrh()">
                <span class="menu-text" style="display: none;">Recursos Humanos</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenurh">
                <a href="painelrh" class="page-link" onclick="window.location.href='painelrh'">Cadastrar Colaborador</a>
                <a href="colaboradores_rh" class="page-link" onclick="window.location.href='colaboradores_rh'">Ver
                    Colaboradores</a>
                <a href="carga-lotecolaboradores" class="page-link"
                    onclick="window.location.href='carga-lotecolaboradores'">Opções de cadastro</a>
            </div>
        </div>


        <i class="fas fa-cog page-link" onclick="window.location.href='configuracoes.php'">
            <span class="menu-text"><a href="configuracoes.php" class="page-link">Configurações</a></span>
        </i>

        <!-- Texto de rodapé -->
        <div class="footer">
            <p>&copy; 2024 VIGGO - Todos os direitos reservados.</p>
            <p>Versão: 1.0.0</p>
        </div>
    </div>

    <!-- Área principal -->
    <div class="main-content">
        <!-- Barra de navegação superior -->
        <div class="topbar">
            <div class="navbar">
                <img src="../img/nexuspgi_light.png" width="auto" height="25px" alt="">
            </div>
            <div class="user-profile" onclick="toggleLogoutMenu()">
                <span id="tempo-logado"><?php echo $tempo_online_formatado; ?></span>
                <div class="vertical-separator"></div>
                <span id="user-name"><?php echo $_SESSION['nome']; ?></span>
                <img src="<?php echo $foto_perfil; ?>" alt="" style="cursor: pointer; border: 3px solid #53bdc1;">

                <!-- Submenu for Logout -->
                <?php include 'submenu.php'; ?>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div id="container-content">
        <div class="content">
            <div class="panel" style="padding: 0px !important;">
                <div class="header-container">
                    <h2>&nbsp;<i class="fa-solid fa-file-lines fa-xl"></i> Emissão de Venda
                    <div class="right-info">
                        <span id="ip" class="info"></span><span class="info">&nbsp; | &nbsp;</span>
                        <span id="datetime" class="info"></span>
                    </div>
                    </h2>
                </div>
            </div>

            <div class="subpanel">
                <div class="header-container">
                <div class="header-title"><span class="tittle" onclick="window.location.href='emissao_01.php'">Dados Cadastrais</span></div>
                <div class="header-title sub1"><span class="tittle" onclick="window.location.href='emissao_02.php'">&nbsp; Tipo Endereço</span></div>
                <div class="header-title sub2"><span class="tittle" onclick="window.location.href='emissao_03.php'">&nbsp; Tipo Venda</span></div>
                <div class="header-title sub3"><span class="tittle" onclick="window.location.href='emissao_04.php'">&nbsp; Dados upgrade</span></div>
                <div class="header-title sub03"><span class="tittle" onclick="window.location.href='emissao_05.php'">&nbsp; Produtos</span></div>
                <div class="header-title sub04"><span class="tittle" onclick="window.location.href='emissao_06.php'">&nbsp; Portabilidade</span></div>
                <div class="header-title sub_5">&nbsp; Info. Input</div>
                </div>
            </div>
<input type="hidden" class="form-control" id="tipo_cliente" name="tipo_cliente" value="<?php echo $tipo_cliente; ?>">
<!-- Painel Fixo -->
<div class="panel2" id="painel1">
    <h5 class="form-header">INFORMAÇÕES DE PAGAMENTO</h5>
    <form method="POST">
        <div class="container">
            <div class="row g-3">
                <!-- Linha 1 -->
                <div class="col-md-4">
                    <label for="data-vencimento" class="form-label">Data de Vencimento</label>
                    <select class="form-select" id="data-vencimento" name="data-vencimento">
                        <option value="" disabled selected>Selecione</option>
                        <option value="01" <?php if ($data_vencimento == '01') echo 'selected'; ?>>01</option>
                        <option value="02" <?php if ($data_vencimento == '02') echo 'selected'; ?>>02</option>
                        <option value="03" <?php if ($data_vencimento == '03') echo 'selected'; ?>>03</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="forma-pagamento" class="form-label">Forma de Pagamento</label>
                    <select class="form-select" id="forma-pagamento" name="forma-pagamento" onchange="atualizarCamposPagamento()">
                        <option value="" disabled selected>Selecione</option>
                        <option value="boleto" <?php if ($forma_pagamento == 'boleto') echo 'selected'; ?>>Boleto Bancário</option>
                        <option value="debito" <?php if ($forma_pagamento == 'debito') echo 'selected'; ?>>Débito Automático</option>
                        <option value="fatura" <?php if ($forma_pagamento == 'fatura') echo 'selected'; ?>>Fatura Digital</option>
                    </select>
                </div>
                <div class="col-md-4 d-none" id="campo-email-fatura">
                    <label for="email-fatura" class="form-label">E-mail para Fatura</label>
                    <input type="email" class="form-control" id="email-fatura" name="email-fatura" value="<?php echo htmlspecialchars($email_fatura); ?>">
                </div>
            </div>
            <div class="row g-3 mt-3 d-none" id="campos-debito">
                <!-- Linha 2 -->
                <div class="col-md-4">
                    <label for="banco" class="form-label">Banco</label>
                    <select class="form-select" id="banco" name="banco">
                        <option value="" disabled selected>Selecione</option>
                        <option <?php if ($banco == 'ITAU') echo 'selected'; ?>>ITAÚ</option>
                        <option <?php if ($banco == 'SANTANDER') echo 'selected'; ?>>SANTANDER</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="agencia" class="form-label">Agência</label>
                    <input type="text" class="form-control" id="agencia" name="agencia" value="<?php echo htmlspecialchars($agencia); ?>">
                </div>
                <div class="col-md-4">
                    <label for="conta" class="form-label">Conta</label>
                    <input type="text" class="form-control" id="conta" name="conta" value="<?php echo htmlspecialchars($conta); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Painel Móvel -->
    <div class="panel2 mt-4" id="painel2">
        <h5 class="form-header">INFORMAÇÕES INPUT</h5>
        <div class="container">
            <div class="row g-3">
                <!-- Linha 1 -->
                <div class="col-md-3">
                    <label for="instancia" class="form-label">Instância</label>
                    <input type="text" class="form-control" id="instancia" name="instancia" value="<?php echo htmlspecialchars($instancia); ?>">
                </div>
                <div class="col-md-3">
                    <label for="ordem-servico" class="form-label">Ordem de Serviço</label>
                    <input type="text" class="form-control" id="ordem-servico" name="ordem-servico" value="<?php echo htmlspecialchars($ordem_servico); ?>">
                </div>
                <div class="col-md-3">
                    <label for="cnl" class="form-label">CNL</label>
                    <input type="text" class="form-control" id="cnl" name="cnl" value="<?php echo htmlspecialchars($cnl); ?>">
                </div>
                <div class="col-md-3">
                    <label for="at" class="form-label">AT</label>
                    <select class="form-select" id="at" name="at">
                        <option value="" disabled selected>Selecione</option>
                        <option value="Opção 1" <?php if ($at == 'Opção 1') echo 'selected'; ?>>Opção 1</option>
                        <option value="Opção 2" <?php if ($at == 'Opção 2') echo 'selected'; ?>>Opção 2</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <!-- Linha 2 -->
                <div class="col-md-6">
                    <label for="codigo-pap-mobile" class="form-label">Código PAP Mobile</label>
                    <input type="text" class="form-control" id="codigo-pap-mobile" name="codigo-pap-mobile"  value="<?php echo htmlspecialchars($codigo_venda); ?>">
                </div>
                <div class="col-md-6">
                    <label for="protocolo-smart-next" class="form-label">Protocolo Smart/Next</label>
                    <input type="text" class="form-control" id="protocolo-smart-next" name="protocolo-smart-next" value="<?php echo htmlspecialchars($protocolo_smart_next); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Botões abaixo do formulário -->
    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-warning text-white fw-bold">TABULAR</button>
        <div>
            <button type="submit" name="sair_venda" class="btn btn-success btn_fila_sair text-white fw-bold me-2">SAIR SEM ALTERAR</button>
            <button type="submit" name="voltar" class="btn btn-danger text-white fw-bold me-2">VOLTAR</button>
            <button type="submit" name="avancar" class="btn btn-success text-white fw-bold">AVANÇAR</button>
        </div>
    </div>
</form>

</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tipoCliente = document.getElementById('tipo_cliente').value;  // Obtém o valor do campo hidden
    const dataVencimentoSelect = document.getElementById('data-vencimento');

    console.log("Tipo Cliente:", tipoCliente);  // Log do tipo de cliente

    // Limpa as opções do select antes de adicionar novas
    dataVencimentoSelect.innerHTML = '<option value="" disabled>Selecione</option>';

    let options = [];

    // Verifica o tipo de cliente e define as opções correspondentes
    if (tipoCliente === 'BCB') {
        options = [1, 6, 10, 17, 21, 26];
    } else if (tipoCliente === 'B2B') {
        options = [2, 5, 8, 21, 25];
    } else if (tipoCliente === 'B2C') {
        options = [1, 3, 7, 14, 18];  // Exemplo de datas para B2C
    }

    console.log("Opções:", options);  // Log das opções geradas

    // Recupera o valor selecionado da sessão (PHP insere como um atributo HTML)
    const selectedValue = "<?php echo $data_vencimento; ?>";

    // Adiciona as opções no select
    options.forEach(function(optionValue) {
        const option = document.createElement('option');
        option.value = optionValue;
        option.textContent = optionValue;

        // Verifica se esta opção é a selecionada
        if (optionValue == selectedValue) {
            option.selected = true;
        }

        dataVencimentoSelect.appendChild(option);
    });

    // Atualiza o valor na sessão sempre que houver uma mudança
    dataVencimentoSelect.addEventListener('change', function() {
        const selectedOption = dataVencimentoSelect.value;
        fetch('atualizar_sessao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `campo=data-vencimento&valor=${selectedOption}`
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const tipoClienteInput = document.getElementById('tipo_cliente'); // Input hidden com o tipo do cliente
    const ordemServicoInput = document.getElementById('ordem-servico'); // Input para ordem de serviço

    // Verifica o tipo de cliente
    const tipoCliente = tipoClienteInput.value;

    // Função para formatar o campo de acordo com o tipo de cliente
    function formatarOrdemServico() {
        let valor = ordemServicoInput.value.toUpperCase(); // Converte para maiúsculas enquanto digita

        if (tipoCliente === 'B2B') {
            // Se for B2B, o valor deve seguir o formato: 8-11 caracteres-1
            if (!valor.startsWith("8-")) {
                ordemServicoInput.value = "8-" + valor.replace(/\D/g, '').slice(0, 11);  // Adiciona "8-" no começo e permite 11 caracteres
            } else if (valor.length === 13) {
                // Se tem 11 caracteres mais o "8-" e o "-", adiciona automaticamente o "-1" no final
                ordemServicoInput.value = valor + "-1";
            }

            // Limita a quantidade de caracteres para 15 (8-11-1)
            if (valor.length > 15) {
                ordemServicoInput.value = valor.slice(0, 15);
            }
        } else if (tipoCliente === 'B2C') {
            // Se for B2C, permite 9 caracteres + letra A no final
            if (valor.length === 9) {
                ordemServicoInput.value = valor + "A"; // Adiciona "A" no final
            }

            // Limita a quantidade de caracteres para 10 (9 caracteres + A)
            if (valor.length > 10) {
                ordemServicoInput.value = valor.slice(0, 10);
            }
        }
    }

    // Adiciona evento para quando o usuário digitar no campo
    ordemServicoInput.addEventListener('input', function(event) {
        // Apenas formata se o valor for alterado, mas não interfere ao apagar
        if (event.inputType !== 'deleteContentBackward') {
            formatarOrdemServico(); // Aplica formatação somente quando há inserção
        }
    });

    // Força o campo a exibir sempre em maiúsculas
    ordemServicoInput.addEventListener('input', function() {
        ordemServicoInput.value = ordemServicoInput.value.toUpperCase();
    });
});
</script>
<script>
    function atualizarCamposPagamento() {
        // Obter valor selecionado
        const formaPagamento = document.getElementById('forma-pagamento').value;

        // Referências aos campos
        const campoEmailFatura = document.getElementById('campo-email-fatura');
        const camposDebito = document.getElementById('campos-debito');

        // Resetar visibilidade
        campoEmailFatura.classList.add('d-none');
        camposDebito.classList.add('d-none');

        // Mostrar campos conforme seleção
        if (formaPagamento === 'fatura') {
            campoEmailFatura.classList.remove('d-none');
        } else if (formaPagamento === 'debito') {
            camposDebito.classList.remove('d-none');
        }
    }

    // Executar ao carregar a página para manter o estado
    document.addEventListener("DOMContentLoaded", () => {
        atualizarCamposPagamento();
    });
</script>

<script>
        // Atualização de Tempo Logado
        let minutosLogado = <?php echo floor($tempo_online / 60); ?>;
        function atualizarTempo() {
            minutosLogado++;
            document.getElementById('tempo-logado').innerText = minutosLogado < 60 ? minutosLogado + " minutos" : `${Math.floor(minutosLogado / 60)}:${minutosLogado % 60} horas`;
        }
        setInterval(atualizarTempo, 60000);

        // Toggle the logout submenu visibility
        function toggleLogoutMenu() {
            const submenu = document.getElementById('submenu-logout');
            submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
        }

        // Close the submenu when clicking outside
        document.addEventListener('click', function (event) {
            const userProfile = document.querySelector('.user-profile');
            const submenu = document.getElementById('submenu-logout');
            if (!userProfile.contains(event.target)) {
                submenu.style.display = 'none';
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggle-icon');
            const nexusText = document.getElementById('nexus_texto');
            sidebar.classList.toggle('expanded');

            // Modificar o display das classes .menu-text dentro dos itens Backoffice e dropdown-rh
            const menuText = document.querySelector('.menu-item.dropdown .menu-text');
            const menuTextRH = document.querySelector('.menu-item.dropdown-rh .menu-text');
            const menuTextHome = document.querySelector('.sidebar .menu-text');

            if (sidebar.classList.contains('expanded')) {
                // Se o menu estiver expandido, exibe o texto
                menuText.style.display = 'flex';
                menuTextRH.style.display = 'flex';
                menuTextHome.style.display = 'flex';
            } else {
                // Se o menu estiver recolhido, esconde o texto
                menuText.style.display = 'none';
                menuTextRH.style.display = 'none';
                menuTextHome.style.display = 'none';
            }

            if (sidebar.classList.contains('expanded')) {
                toggleIcon.classList.replace('fi-br-bars-sort', 'fi-br-angle-left'); // Muda para seta ao expandir
                toggleIcon.style.marginRight = '5px'; // Adiciona o margin-right
                nexusText.style.marginRight = '80px'; 
                nexusText.style.marginTop = '-6px'; 
            } else {
                toggleIcon.classList.replace('fi-br-angle-left', 'fi-br-bars-sort'); // Volta para hambúrguer ao retrair
                toggleIcon.style.marginRight = '4px'; // Remove o margin-right
            }
        }

        function showSubmenu(element) {
            const submenu = element.querySelector(".submenu-card");
            submenu.style.display = "block"; // Exibe o submenu
            setTimeout(() => {
                submenu.style.opacity = "1"; // Exibe o submenu gradualmente
            }, 10);

            // Ajusta a altura do card para acomodar o submenu
            element.style.height = "200px"; // Expande o card para mostrar o submenu

            // Ajusta a altura da sidebar para se adaptar ao conteúdo expandido
            adjustSidebarHeight();
        }

        function hideSubmenu(element) {
            const submenu = element.querySelector(".submenu-card");
            submenu.style.opacity = "0"; // Oculta o submenu gradualmente
            setTimeout(() => {
                submenu.style.display = "none"; // Esconde o submenu após a animação
            }, 300);

            // Retorna a altura do card ao tamanho original
            element.style.height = "120px"; // Retorna à altura original do card

            // Ajusta a altura da sidebar para se adaptar ao conteúdo contraído
            adjustSidebarHeight();
        }

        // Função para ajustar a altura do menu lateral conforme o conteúdo
        function adjustSidebarHeight() {
            const sidebar = document.querySelector('.sidebar');
            let totalHeight = 800;

            // Calcula a altura total necessária para o conteúdo da sidebar
            const cards = sidebar.querySelectorAll('.card');
            cards.forEach(card => {
                totalHeight += card.offsetHeight; // Soma a altura de cada card
            });

            // Ajusta a altura do menu lateral
            sidebar.style.height = totalHeight + 'px';
        }


        function hideSubmenu(element) {
            const submenu = element.querySelector(".submenu-card");
            submenu.style.opacity = "0"; // Oculta o submenu gradualmente
            setTimeout(() => {
                submenu.style.display = "none"; // Esconde o submenu após a animação
            }, 300);

            // Retorna a altura do card para o tamanho original
            element.style.height = "120px"; // Retorna à altura original

            // Se necessário, ajuste o menu lateral para diminuir conforme o submenu desaparece
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.height = "auto"; // O menu lateral se ajusta ao conteúdo
        }



        function toggleDropdown() {
            const menuItem = document.querySelector('.menu-item.dropdown');
            menuItem.classList.toggle('active'); // Alterna a classe para mostrar/ocultar o submenu
        }

        function toggleDropdownrh() {
            const menuItem = document.querySelector('.menu-item.dropdown-rh');
            menuItem.classList.toggle('active'); // Alterna a classe para mostrar/ocultar o submenu
        }

    </script>
    <script>
        // Função para pegar o IP da máquina (usando um serviço público)
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                document.getElementById('ip').textContent = `IP: ${data.ip}`;
            })
            .catch(error => console.log('Erro ao obter IP:', error));

        // Função para exibir a data e hora atuais
        function atualizarDataHora() {
            const agora = new Date();
            const data = agora.toLocaleDateString('pt-BR'); // Data no formato pt-BR
            const hora = agora.toLocaleTimeString('pt-BR'); // Hora no formato pt-BR
            document.getElementById('datetime').textContent = `${data} ${hora}`;
        }

        // Atualiza a data/hora a cada segundo
        setInterval(atualizarDataHora, 1000);
    </script>
<script>
$(document).ready(function() {
    // Carregar conteúdo inicial da página
    loadPage(location.pathname);

    // Detectar cliques nos links para carregar conteúdo via AJAX
    $('a.page-link').click(function(e) {
        e.preventDefault();  // Impede o comportamento padrão do link

        var page = $(this).attr('href');
        loadPage(page);
    });

    // Função para carregar o conteúdo da página via AJAX
    function loadPage(page) {
        $('#content').fadeOut(300, function() {
            // Realiza o carregamento assíncrono da nova página
            $.ajax({
                url: page,
                success: function(data) {
                    // Extrai apenas o conteúdo relevante da página (sem header, nav, etc.)
                    var newContent = $(data).find('#content').html();
                    
                    $('#content').html(newContent).fadeIn(300);

                    // Atualiza o histórico da URL no navegador
                    history.pushState({ page: page }, '', page);
                },
                error: function() {
                    $('#content').html('<p>Erro ao carregar a página!</p>').fadeIn(300);
                }
            });
        });
    }

    // Gerenciar navegação com o botão de voltar/avançar do navegador
    $(window).on('popstate', function() {
        loadPage(location.pathname);
    });
});
</script>
</body>
<script>
// Elementos
const toggleButton = document.getElementById('dark-mode-toggle');
const themeStylesheet = document.getElementById('theme-stylesheet');
const tableStylesheet = document.getElementById('table-stylesheet');
const pageStylesheet = document.getElementById('page-stylesheet');

// Função para aplicar o tema
function applyTheme(theme) {
    if (theme === 'dark') {
        themeStylesheet.setAttribute('href', '../css/principal_escuro.css');
        tableStylesheet.setAttribute('href', '../css/tabelas_escuro.css');
        pageStylesheet.setAttribute('href', '../css/page-auditoria_escuro.css');
        toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo claro';
    } else {
        themeStylesheet.setAttribute('href', '../css/principal.css');
        tableStylesheet.setAttribute('href', '../css/tabelas.css');
        pageStylesheet.setAttribute('href', '../css/page-auditoria.css');
        toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo escuro';
    }
}

// Verificar o tema salvo no localStorage ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);
});

// Alternar tema ao clicar no botão
toggleButton.addEventListener('click', () => {
    const currentTheme = themeStylesheet.getAttribute('href') === '../css/principal.css' ? 'light' : 'dark';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    // Aplicar e salvar o novo tema
    applyTheme(newTheme);
    localStorage.setItem('theme', newTheme);
});

// Função para redefinir o tema ao fazer logout
function resetTheme() {
    localStorage.removeItem('theme');
    applyTheme('light'); // Define como tema claro
}
</script>
</html>