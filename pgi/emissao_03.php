<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.php");
    exit();
}

// Verifica e registra o tempo de login
if (!isset($_SESSION['tempo_login'])) {
    $_SESSION['tempo_login'] = time();
}
$tempo_online = time() - $_SESSION['tempo_login'];
$tempo_online_formatado = ($tempo_online < 3600)
    ? "Tempo Logado: " . floor($tempo_online / 60) . " minutos"
    : "Tempo Logado: " . sprintf("%02d:%02d horas", floor($tempo_online / 3600), floor(($tempo_online % 3600) / 60));

// Define o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'] ?? null;

// Logout: Encerra a sessão e redireciona para o login
if (isset($_POST['sairnexus'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Inclui validações e carregamento de dados adicionais
require 'valida_entrada.php';
require 'carregar_foto_perfil.php';

// Carrega a foto de perfil do usuário
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);

// Recupera os dados de emissão da sessão
$dados_emissao = $_SESSION['dados_emissao'] ?? [];

$codigo_venda = $dados_emissao['codigo_venda'] ?? ''; 
$documento = $dados_emissao['documento'] ?? '';
$nomeCompleto = $dados_emissao['nomeCompleto'] ?? '';
$dataNascimento = $dados_emissao['dataNascimento'] ?? '';
$nomeMae = $dados_emissao['nomeMae'] ?? '';
$email = $dados_emissao['email'] ?? '';
$celular = $dados_emissao['celular'] ?? '';
$telefone1 = $dados_emissao['telefone1'] ?? '';
$telefone2 = $dados_emissao['telefone2'] ?? '';
$cep = $dados_emissao['cep'] ?? '';
$numero = $dados_emissao['numero'] ?? '';
$logradouro = $dados_emissao['logradouro'] ?? '';
$bairro = $dados_emissao['bairro'] ?? '';
$cidade = $dados_emissao['cidade'] ?? '';
$uf = $dados_emissao['uf'] ?? '';
$complemento1 = $dados_emissao['complemento1'] ?? '';
$complemento2 = $dados_emissao['complemento2'] ?? '';
$complemento3 = $dados_emissao['complemento3'] ?? '';
$observacao = $dados_emissao['observacao'] ?? '';
$tipoEndereco = $dadosEmissao['ftta'] === 'sim' ? 'FTTA' : ($dadosEmissao['ftth'] === 'sim' ? 'FTTH' : '');

// Recupera as seleções de cards armazenadas para esse código de venda
$selecao_painel1 = $_SESSION['selecoes_cards'][$codigo_venda]['painel1'] ?? '';
$selecao_painel2 = $_SESSION['selecoes_cards'][$codigo_venda]['painel2'] ?? '';

// Atualiza os dados na sessão após o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualiza os dados de emissão na sessão
    $_SESSION['dados_emissao'] = array_merge($dados_emissao, $_POST);

    // Armazena as seleções dos cards
    $_SESSION['selecoes_cards'][$codigo_venda]['painel1'] = $_POST['painel1_selecao'] ?? '';
    $_SESSION['selecoes_cards'][$codigo_venda]['painel2'] = $_POST['painel2_selecao'] ?? '';

    // Redireciona conforme o botão clicado
    if (isset($_POST['avancar'])) {
        header("Location: emissao_04.php");
        exit();
    }

    if (isset($_POST['voltar'])) {
        header("Location: emissao_02.php");
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


        <i class="fa-solid fa-house" onclick="window.location.href='menu.php'">
            <span class="menu-text">Página inicial</span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown">
            <i class="fi fi-sr-user-headset active" onclick="toggleDropdown(this)">
                <span class="menu-text" style="display: none;">Backoffice</span>
            </i>
            <!-- Submenu principal -->
            <div class="submenu" id="submenu">
                <!-- Seção AUDITORIA -->
                <div class="submenu-section">
                    <a href="#" class="section-link" onclick="toggleSubmenuLateral(this)">AUDITORIA</a>
                    <div class="submenu-items submenu-specific">
                     <a onclick="window.location.href='fila.php'" class="page-link">Fila de vendas</a>
                        <a onclick="window.location.href='importar_zip.php'" class="page-link">Importação PAP</a>
                        <a href="#" class="page-link">Relatórios</a>
                    </div>
                </div>

                <!-- Seção PÓS-VENDA -->
                <div class="submenu-section">
                    <a href="#" class="section-link" onclick="toggleSubmenuLateral(this)">PÓS-VENDA</a>
                    <div class="submenu-items submenu-specific">
                        <a href="#" class="page-link">Acompanhar Instalação</a>
                        <a href="paineltfp.php" class="page-link">TFP</a>
                        <a href="#" class="page-link">Exportação</a>
                        <a href="#" class="page-link">Relatórios</a>
                    </div>
                </div>

            </div>
        </div>

        <i class="fa-solid fa-shapes">
            <span class="menu-text"><a href="dashboards.php" class="page-link">Dashboards</a></span>
        </i>

                <!-- Item com dropdown COMERCIAL -->
                <div class="menu-item dropdown-com">
            <i class="fi fi-sr-boss" onclick="toggleDropdownCom()">
                <span class="menu-text" style="display: none;">Comercial</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenucom">
                <a href="#" class="page-link">Alterar Hierarquia</a>
                <a href="#" class="page-link">Gestão de vendas</a>
                <a href="#" class="page-link">Relatórios</a>
            </div>
        </div>

        <!-- Item com dropdown RH -->
        <div class="menu-item dropdown-rh">
            <i class="fi fi-sr-users" onclick="toggleDropdownrh()">
                <span class="menu-text" style="display: none;">Gestão de Pessoas</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenurh">
                <a onclick="window.location.href='colaboradores_rh.php'" class="page-link">Gestão de usuários</a>
                <a onclick="window.location.href='carga-lotecolaboradores.php'" class="page-link">Opção de Cadastro</a>
                <a onclick="window.location.href='Painel_hierarquia.php'" class="page-link">Hierarquia</a>
                <a href="#" class="page-link">Relatórios</a>
            </div>
        </div>


        <i class="fas fa-cog page-link" onclick="window.location.href='configuracoes.php'">
            <span class="menu-text"><a href="configuracoes.php" class="page-link">Configurações</a></span>
        </i>

        <!-- Texto de rodapé -->
        <div class="footer">
        <hr>
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
                        <div class="header-title"><span class="tittle"
                                onclick="window.location.href='emissao_01.php'">Dados Cadastrais</span></div>
                        <div class="header-title sub1"><span class="tittle"
                                onclick="window.location.href='emissao_02.php'">&nbsp; Tipo Endereço</span></div>
                        <div class="header-title sub2">&nbsp; Tipo Venda</div>
                    </div>
                </div>
<!-- Painel Fixo -->
<div class="panel2" id="painel1">

    <h5 class="form-header">Tipo de Venda - FIXA</h5>
    <form method="POST">
    <div class="content-center">
        <div class="button-container">
            <div class="card-wrapper">
            <input type="hidden" name="codigo_venda" value="<?php echo $_SESSION['dados_emissao']['codigo_venda'] ?? ''; ?>">
                <div class="card <?php echo ($tipo_venda_fixa == 'NOVA VENDA') ? 'highlight' : ''; ?>" onclick="selectCard('painel1', this)">
                    <div class="card-title">NOVA VENDA</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Nova Venda">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_fixa == 'MIGRAÇÃO METÁLICO') ? 'highlight' : ''; ?>" onclick="selectCard('painel1', this)">
                    <div class="card-title">MIGRAÇÃO METÁLICO</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Migração Metálico">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_fixa == 'UPGRADE') ? 'highlight' : ''; ?>" onclick="selectCard('painel1', this)">
                    <div class="card-title">UPGRADE</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Upgrade">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_fixa == 'NÃO POSSUI') ? 'highlight' : ''; ?>" onclick="selectCard('painel1', this)">
                    <div class="card-title">NÃO POSSUI</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Não Possui">?</div>
            </div>
        </div>
    </div>
</div>

<!-- Painel Móvel -->
<div class="panel2" id="painel2">
    <h5 class="form-header">Tipo de Venda - MÓVEL</h5>
    <div class="content-center">
        <div class="button-container">
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_movel == 'NOVA VENDA') ? 'highlight' : ''; ?>" onclick="selectCard('painel2', this)">
                    <div class="card-title">NOVA VENDA</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Nova Venda">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_movel == 'MIGRAÇÃO PRÉ') ? 'highlight' : ''; ?>" onclick="selectCard('painel2', this)">
                    <div class="card-title">MIGRAÇÃO PRÉ</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Migração Pré">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_movel == 'MIGRAÇÃO CONTROLE') ? 'highlight' : ''; ?>" onclick="selectCard('painel2', this)">
                    <div class="card-title">MIGRAÇÃO CONTROLE</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Migração Controle">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_movel == 'UPGRADE PÓS INDIVIDUAL') ? 'highlight' : ''; ?>" onclick="selectCard('painel2', this)">
                    <div class="card-title">UPGRADE PÓS INDIVIDUAL</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Upgrade Pós Individual">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_movel == 'UPGRADE PÓS FAMÍLIA') ? 'highlight' : ''; ?>" onclick="selectCard('painel2', this)">
                    <div class="card-title">UPGRADE PÓS FAMÍLIA</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Upgrade Pós Família">?</div>
            </div>
            <div class="card-wrapper">
                <div class="card <?php echo ($tipo_venda_movel == 'NÃO POSSUI') ? 'highlight' : ''; ?>" onclick="selectCard('painel2', this)">
                    <div class="card-title">NÃO POSSUI</div>
                </div>
                <div class="tooltip-icon" data-tooltip="Informação sobre Não Possui">?</div>
            </div>
        </div>
    </div>
</div>
<!-- Inputs ocultos para armazenar as seleções -->
<input type="hidden" id="painel1_selecao" name="painel1_selecao" value="<?php echo htmlspecialchars($selecao_painel1); ?>">
<input type="hidden" id="painel2_selecao" name="painel2_selecao" value="<?php echo htmlspecialchars($selecao_painel2); ?>">

<!-- Botões abaixo do formulário -->
<div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn btn-warning text-white fw-bold">TABULAR</button>
    <div>
        <!-- Botões de navegação com formulário -->
            <button type="submit" name="sair_venda" class="btn btn-success btn_fila_sair text-white fw-bold me-2">SAIR SEM ALTERAR</button>
            <button type="submit" name="voltar" class="btn btn-danger text-white fw-bold me-2">VOLTAR</button>
            <button type="submit" name="avancar" class="btn btn-success text-white fw-bold">AVANÇAR</button>
        </form>
    </div>
</div>


            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
document.addEventListener('DOMContentLoaded', () => {
    const painel2 = document.getElementById('painel2');

    // Inicia painel2 desabilitado
    painel2.classList.add('panel-disabled');

    // Verifica as seleções dos cards e marca os cards selecionados ao carregar a página
    const painel1Selecao = document.getElementById('painel1_selecao').value;
    const painel2Selecao = document.getElementById('painel2_selecao').value;

    // Marca os cards do painel 1 conforme a seleção armazenada na sessão
    if (painel1Selecao) {
        const painel1Cards = document.querySelectorAll('#painel1 .card');
        painel1Cards.forEach(card => {
            const cardTitle = card.querySelector('.card-title').innerText;
            if (cardTitle === painel1Selecao) {
                card.classList.add('highlight');
                card.classList.remove('disabled');
            } else {
                card.classList.add('disabled');
            }
        });

        // Ativa o painel2 se algum card foi selecionado no painel1
        painel2.classList.remove('panel-disabled');
    }

    // Marca os cards do painel 2 conforme a seleção armazenada na sessão
    if (painel2Selecao) {
        const painel2Cards = document.querySelectorAll('#painel2 .card');
        painel2Cards.forEach(card => {
            const cardTitle = card.querySelector('.card-title').innerText;
            if (cardTitle === painel2Selecao) {
                card.classList.add('highlight');
            }
        });
    }

    // Funcionalidade de seleção de card
    function selectCard(painelId, cardElement) {
        const currentPanel = document.getElementById(painelId);
        const otherPanelId = painelId === 'painel1' ? 'painel2' : 'painel1';
        const otherPanel = document.getElementById(otherPanelId);

        const isHighlighted = cardElement.classList.contains('highlight');

        if (isHighlighted) {
            // Desmarca o card e limpa o painel
            resetPanel(currentPanel);

            if (painelId === 'painel1') {
                // Desabilita painel2 até que algum card seja selecionado no painel1
                otherPanel.classList.add('panel-disabled');
                resetPanel(otherPanel);
            }
        } else {
            // Marca o card selecionado e desabilita os outros
            currentPanel.querySelectorAll('.card').forEach(card => {
                if (card === cardElement) {
                    card.classList.add('highlight');
                    card.classList.remove('disabled');
                    card.querySelector('.card-title').classList.add('highlight-title');
                } else {
                    card.classList.add('disabled');
                    card.classList.remove('highlight');
                    card.querySelector('.card-title').classList.remove('highlight-title');
                }
            });

            if (painelId === 'painel1') {
                // Quando painel1 é selecionado, libera painel2
                otherPanel.classList.remove('panel-disabled');
            }
        }

        // Grava a seleção no campo oculto correspondente
        let selecionado = currentPanel.querySelector('.highlight')?.querySelector('.card-title').innerText || '';
        if (painelId === 'painel1') {
            document.getElementById('painel1_selecao').value = selecionado;
        } else {
            document.getElementById('painel2_selecao').value = selecionado;
        }
    }

    // Função para resetar um painel
    function resetPanel(panel) {
        panel.querySelectorAll('.card').forEach(card => {
            card.classList.remove('highlight');
            card.classList.remove('disabled');
            card.querySelector('.card-title').classList.remove('highlight-title');
        });
    }

    // Adiciona o evento de clique aos cards
    const painel1Cards = document.querySelectorAll('#painel1 .card');
    painel1Cards.forEach(card => {
        card.addEventListener('click', function() {
            selectCard('painel1', this);
        });
    });

    const painel2Cards = document.querySelectorAll('#painel2 .card');
    painel2Cards.forEach(card => {
        card.addEventListener('click', function() {
            selectCard('painel2', this);
        });
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
            const menuTextCom = document.querySelector('.menu-item.dropdown-com .menu-text');
            const menuTextHome = document.querySelector('.sidebar .menu-text');

            if (sidebar.classList.contains('expanded')) {
                // Se o menu estiver expandido, exibe o texto
                menuText.style.display = 'flex';
                menuTextRH.style.display = 'flex';
                menuTextCom.style.display = 'flex';
                menuTextHome.style.display = 'flex';
            } else {
                // Se o menu estiver recolhido, esconde o texto
                menuText.style.display = 'none';
                menuTextRH.style.display = 'none';
                menuTextCom.style.display = 'none';
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
        // Toggle a visibilidade do submenu e o efeito de desfoque
        function toggleSubmenu(element) {
            const isActive = element.classList.contains("active");

            // Se o submenu está ativo, remove o efeito de desfoque e fecha o submenu
            if (isActive) {
                element.classList.remove("active");
                document.querySelector('.panel2').classList.remove("show-overlay");
                document.querySelectorAll('.favorite-item').forEach(item => {
                    item.classList.remove('blurred'); // Remove o efeito de desfoque de todos os cards
                });
            } else {
                // Caso contrário, aplica o efeito de desfoque e escurecimento
                element.classList.add("active");
                document.querySelector('.panel2').classList.add("show-overlay");
                document.querySelectorAll('.favorite-item').forEach(item => {
                    if (item !== element) {
                        item.classList.add('blurred'); // Aplica o efeito de desfoque nos cards não clicados
                    }
                });
            }
        }

        // Funcionalidade de esconder o submenu e remover o desfoque ao clicar fora
        document.addEventListener('click', function (event) {
            const favoriteItems = document.querySelectorAll('.favorite-item');
            let isClickedInsideAnyCard = false;

            favoriteItems.forEach(item => {
                if (item.contains(event.target)) {
                    isClickedInsideAnyCard = true;
                }
            });

            // Se o clique for fora de qualquer card, fecha todos os menus e remove o efeito de desfoque
            if (!isClickedInsideAnyCard) {
                favoriteItems.forEach(item => {
                    item.classList.remove("active");
                    item.classList.remove('blurred'); // Remove o efeito de desfoque de todos os cards
                });
                document.querySelector('.panel2').classList.remove("show-overlay"); // Remove o efeito de desfoque do panel2
            }
        });


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


        // Função para expandir ou recolher o submenu principal
        function toggleDropdown(icon) {
            const menuItem = icon.closest('.menu-item.dropdown');
            menuItem.classList.toggle('active'); // Alterna classe ativa
        }

        // Função para mostrar/ocultar as subseções específicas
        function toggleSubmenuLateral(sectionLink) {
            const submenuItems = sectionLink.nextElementSibling;
            submenuItems.classList.toggle('active'); // Exibe ou esconde os links
        }

        function toggleDropdownCom() {
            const menuItem = document.querySelector('.menu-item.dropdown-com');
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
            $(document).ready(function () {
                // Carregar conteúdo inicial da página
                loadPage(location.pathname);

                // Detectar cliques nos links para carregar conteúdo via AJAX
                $('a.page-link').click(function (e) {
                    e.preventDefault();  // Impede o comportamento padrão do link

                    var page = $(this).attr('href');
                    loadPage(page);
                });

                // Função para carregar o conteúdo da página via AJAX
                function loadPage(page) {
                    $('#content').fadeOut(300, function () {
                        // Realiza o carregamento assíncrono da nova página
                        $.ajax({
                            url: page,
                            success: function (data) {
                                // Extrai apenas o conteúdo relevante da página (sem header, nav, etc.)
                                var newContent = $(data).find('#content').html();

                                $('#content').html(newContent).fadeIn(300);

                                // Atualiza o histórico da URL no navegador
                                history.pushState({ page: page }, '', page);
                            },
                            error: function () {
                                $('#content').html('<p>Erro ao carregar a página!</p>').fadeIn(300);
                            }
                        });
                    });
                }

                // Gerenciar navegação com o botão de voltar/avançar do navegador
                $(window).on('popstate', function () {
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