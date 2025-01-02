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
    session_unset();  // Limpa a sessão quando mudar a venda
    $_SESSION['dados_emissao']['codigo_venda'] = $_POST['codigo_venda']; // Atualiza com o novo código de venda
}


// Recupera os dados da sessão
$dadosEmissao = $_SESSION['dados_emissao'] ?? [];

// Recuperar os dados da etapa anterior (emissao_05) da sessão
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? '';
$planoBaseNome = $_SESSION['dados_emissao']['planoBaseNome'] ?? '';  // Nome do plano base
$linhaFixaNome = $_SESSION['dados_emissao']['linhaFixaNome'] ?? '';  // Nome da linha fixa
$tvIptvNome = $_SESSION['dados_emissao']['tvIptvNome'] ?? '';  // Nome da TV (IPTV)
$servicosAdicionais1Nome = $_SESSION['dados_emissao']['servicosAdicionais1Nome'] ?? '';  // Nome dos serviços adicionais 1
$servicosAdicionais2Nome = $_SESSION['dados_emissao']['servicosAdicionais2Nome'] ?? '';  // Nome dos serviços adicionais 2
$servicosAdicionais3Nome = $_SESSION['dados_emissao']['servicosAdicionais3Nome'] ?? '';  // Nome dos serviços adicionais 3
$planoMovelNome = $_SESSION['dados_emissao']['planoMovelNome'] ?? '';  // Nome do plano móvel
$dependentes = $_SESSION['dados_emissao']['dependentes'] ?? '';  // Nome dos dados móveis
$receitaTotalFixa = $_SESSION['dados_emissao']['receitaTotalFixa'] ?? '';
$receitaTotalMovel = $_SESSION['dados_emissao']['receitaTotalMovel'] ?? '';
$receitaTotalPedido = $_SESSION['dados_emissao']['receitaTotalPedido'] ?? '';

$numeroPortadoFixa = $_SESSION['dados_emissao']['numeroPortadoFixa'] ?? '';
$operadoraDoadoraFixa = $_SESSION['dados_emissao']['operadoraDoadoraFixa'] ?? '';
$quantidadeChips = $_SESSION['dados_emissao']['quantidadeChips'] ?? '';
$chips = $_SESSION['dados_emissao']['chips'] ?? [];

// Se o método de requisição for POST, armazena os dados da página na sessão
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Armazena os dados de Fixa
    if (isset($_POST['numero-portado-fixa'])) {
        $_SESSION['dados_emissao']['numeroPortadoFixa'] = $_POST['numero-portado-fixa'];
    }
    if (isset($_POST['operadora-doadora-fixa'])) {
        $_SESSION['dados_emissao']['operadoraDoadoraFixa'] = $_POST['operadora-doadora-fixa'];
    }

    // Armazena os dados de Móvel
    if (isset($_POST['quantidade-chips'])) {
        $_SESSION['dados_emissao']['quantidadeChips'] = $_POST['quantidade-chips'];
    }
    if (isset($_POST['chips'])) {
        $_SESSION['dados_emissao']['chips'] = $_POST['chips'];
    }

    // Botão Avançar
    if (isset($_POST['avancar'])) {
        header("Location: emissao_07.php");
        exit();
    }

    // Botão Voltar
    if (isset($_POST['voltar'])) {
        header("Location: emissao_05.php");
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
    <style>
label, input {
    display: inline-block; 
    width: 180px; 
    text-align: left; 
}

.switch-container { 
    display: flex; 
    align-items: center;
}
.form-check{
    width: 100px;
}
.form-check-input{
    cursor: pointer;
    width: auto;
}

/* Switch em posição 'NÃO' (desmarcado) */
.form-check-input:not(:checked) {
    background-color: #f99ca5; 
    border-color: #f99ca5;
}

/* Switch em posição 'SIM' (marcado) */
.form-check-input:checked {
    background-color: #28a745; 
    border-color: #28a745;
}

/* Estilo da label associada ao switch */
.form-check-label {
    font-weight: bold;
    transition: color 0.3s ease;
}

/* Cor da label quando switch está em 'NÃO' */
.form-check-input:not(:checked) ~ .form-check-label {
    color: #dc3545; 
}

/* Cor da label quando switch está em 'SIM' */
.form-check-input:checked ~ .form-check-label {
    color: #28a745; 
}
.dir {
    margin-left: 10px; 
}
.up{
    margin-top: -8px;
}
/* Estilo adicional para garantir que o texto mude de cor */
.text-success {
    color: green !important;
    font-weight: bold;
}
.text-danger {
    color: red !important;
    font-weight: bold;
}
/* Configuração inicial do contêiner de chips */
.chips-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr); /* Inicialmente, uma coluna */
    gap: 16px; /* Espaço entre os campos */
}

/* Quando houver mais de 5 chips, use 2 colunas */
.chips-grid.more-than-5 {
    grid-template-columns: repeat(2, 1fr); /* Duas colunas */
}

/* Estilo opcional para os campos */
.chip-group {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

</style>

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
                <div class="header-title sub04">&nbsp; Portabilidade</div>
                </div>
            </div>
<!-- Painel Fixa -->
<div class="panel2 mt-4">
    <h5 class="form-header">Fixa</h5>
    <form method="POST">
        <!-- Seção superior: Toggle e Inputs -->
        <div class="d-flex align-items-center">
            <!-- Toggle Switch SIM/NÃO -->
            <div class="form-check form-switch me-3">
                <input class="form-check-input" type="checkbox" id="toggle-fixa">
                <label class="form-check-label" for="toggle-fixa">NÃO</label>
            </div>
            <!-- Inputs ao lado -->
            <div class="me-3 dir up">
                <label for="numero-portado-fixa" class="form-label">Número Portado</label>
                <input type="text" class="form-control" id="numero-portado-fixa" name="numero-portado-fixa" placeholder="Digite o número" value="<?php echo htmlspecialchars($numeroPortadoFixa); ?>" disabled>
            </div>
            <div class="up">
                <label for="operadora-doadora-fixa" class="form-label">Operadora Doadora</label>
                <select class="form-select" id="operadora-doadora-fixa" name="operadora-doadora-fixa" disabled>
                    <option value="" disabled selected>Selecione</option>
                    <option value="TIM" <?php echo ($operadoraDoadoraFixa == 'TIM') ? 'selected' : ''; ?>>TIM</option>
                    <option value="CLARO" <?php echo ($operadoraDoadoraFixa == 'CLARO') ? 'selected' : ''; ?>>CLARO</option>
                    <option value="OI" <?php echo ($operadoraDoadoraFixa == 'OI') ? 'selected' : ''; ?>>OI</option>
                    <option value="ALGAR" <?php echo ($operadoraDoadoraFixa == 'ALGAR') ? 'selected' : ''; ?>>ALGAR</option>
                </select>
            </div>
        </div>
</div>

<!-- Painel Móvel -->
<div class="panel2 mt-4">
    <h5 class="form-header">Móvel</h5>
    <!-- Seção superior: Toggle e Select -->
    <div class="d-flex align-items-center">
        <!-- Toggle Switch SIM/NÃO -->
        <div class="form-check form-switch me-3">
            <input class="form-check-input" type="checkbox" id="toggle-sim-nao">
            <label class="form-check-label" for="toggle-sim-nao">NÃO</label>
        </div>
        <!-- Select Quantidade de Chips -->
        <div class="me-3 dir up">
            <label for="quantidade-chips" class="form-label">Quantidade de CHIPS</label>
            <select class="form-select" id="quantidade-chips" name="quantidade-chips" disabled>
                <option value="" disabled selected>Selecione</option>
                <script>
                    for (let i = 1; i <= 10; i++) {
                        let selected = <?php echo json_encode($quantidadeChips); ?> == i ? 'selected' : '';
                        document.write(`<option value="${i}" ${selected}>${i}</option>`);
                    }
                </script>
            </select>
        </div>
    </div>

    <!-- Campos Dinâmicos -->
    <div id="chips-container" class="chips-grid mt-4"></div>
</div>

<!-- Botões abaixo do formulário -->
<div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn btn-warning text-white fw-bold">TABULAR</button>
    <div>
        <button type="submit" name="sair_venda" class="btn btn-success btn_fila_sair text-white fw-bold me-2">SAIR SEM ALTERAR</button>
        <button type="submit" name="voltar"  class="btn btn-danger text-white fw-bold me-2">VOLTAR</button>
        <button type="submit" name="avancar"  class="btn btn-success text-white fw-bold">AVANÇAR</button>
    </div>
</div>


</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<script>
    // Alternar entre SIM e NÃO no toggle switch (Painel Móvel)
    const toggleSwitchMovel = document.getElementById('toggle-sim-nao');
    const toggleLabelMovel = document.querySelector('label[for="toggle-sim-nao"]');
    const chipsSelect = document.getElementById('quantidade-chips');
    const chipsContainer = document.getElementById('chips-container');

    toggleSwitchMovel.addEventListener('change', function () {
        const isSim = this.checked;
        toggleLabelMovel.textContent = isSim ? 'SIM' : 'NÃO';
        chipsSelect.disabled = !isSim; // Habilita/Desabilita select
        
        if (!isSim) {
            chipsSelect.value = ''; // Redefine o valor para vazio, que irá selecionar "Selecione"
            chipsContainer.innerHTML = ''; // Limpa os campos de chips
        }
    });

// Dinâmica para os campos baseados na quantidade de chips
chipsSelect.addEventListener('change', function () {
    const quantidade = parseInt(this.value, 10);
    const fields = chipsContainer.querySelectorAll('.chip-group'); // Seleciona os grupos de chips

    // Atualiza o layout para duas colunas se quantidade > 5
    if (quantidade > 1) {
        chipsContainer.classList.add('more-than-5');
    } else {
        chipsContainer.classList.remove('more-than-5');
    }

    // Adiciona novos campos se a quantidade for maior
    for (let i = fields.length; i < quantidade; i++) {
        const chipGroup = document.createElement('div');
        chipGroup.className = 'chip-group';

        chipGroup.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <label for="numero-portado-${i+1}" class="form-label">Número Portado <strong>${i+1}</strong></label>
                    <input type="text" class="form-control" id="numero-portado-${i+1}" name="numero-portado-movel" placeholder="Digite o número">
                </div>
                <div class="me-3">
                    <label for="operadora-doadora-${i+1}" class="form-label">Operadora Doadora</label>
                    <input type="text" class="form-control" id="operadora-doadora-${i+1}" placeholder="Operadora" maxlength="11">
                </div>
                <div>
                    <label for="mesma-titularidade-${i+1}" class="form-label">Mesma titularidade?</label>
                    <select class="form-select" id="mesma-titularidade-${i+1}" onchange="mudarCor(this)">
                        <option value="" disabled selected>Selecione</option>
                        <option value="sim">Sim</option>
                        <option value="nao">Não</option>
                    </select>
                </div>
            </div>
        `;

        chipsContainer.appendChild(chipGroup);
    }

    // Remove campos extras se a quantidade diminuir
    for (let i = quantidade; i < fields.length; i++) {
        fields[i].remove();
    }
});
;

    function mudarCor(select) {
        // Remove classes de estilo previamente aplicadas
        select.classList.remove('text-success', 'text-danger');
        
        // Aplica a cor com base no valor selecionado
        if (select.value === "sim") {
            select.classList.add('text-success'); // Verde
        } else if (select.value === "nao") {
            select.classList.add('text-danger'); // Vermelho
        }
    }

// Alternar entre SIM e NÃO no toggle switch (Painel Fixa)
const toggleSwitchFixa = document.getElementById('toggle-fixa');
const toggleLabelFixa = document.querySelector('label[for="toggle-fixa"]');
const numeroPortadoFixa = document.getElementById('numero-portado-fixa');
const operadoraDoadoraFixa = document.getElementById('operadora-doadora-fixa');

toggleSwitchFixa.addEventListener('change', function () {
    const isSim = this.checked; // Verifica se o toggle está marcado
    toggleLabelFixa.textContent = isSim ? 'SIM' : 'NÃO'; // Altera o texto do label
    
    // Habilita ou desabilita os campos
    numeroPortadoFixa.disabled = !isSim;
    operadoraDoadoraFixa.disabled = !isSim;

    // Redefine os valores dos campos caso esteja desmarcado
    if (!isSim) {
        numeroPortadoFixa.value = ''; // Limpa o campo do número portado
        operadoraDoadoraFixa.value = ''; // Volta para a opção "Selecione"
    }
});

</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Seleciona os campos pelo atributo name
    const numeroPortadoFixa = document.querySelector('input[name="numero-portado-fixa"]');
    const numeroPortadoMovel = document.querySelector('input[name="numero-portado-movel"]');
    
    // Aplica a máscara para número fixo (formato: (11) 2748-3776)
    const maskFixo = IMask(numeroPortadoFixa, {
        mask: '(00) 0000-0000'
    });

    // Aplica a máscara para número móvel (formato: (11) 9 8776-2638)
    const maskMovel = IMask(numeroPortadoMovel, {
        mask: '(00) 9 0000-0000'
    });
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