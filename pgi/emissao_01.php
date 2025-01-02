<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.php");
    exit();
}

// Controle de tempo de login
if (!isset($_SESSION['tempo_login'])) {
    $_SESSION['tempo_login'] = time();
}

$tempo_online = time() - $_SESSION['tempo_login'];
$tempo_online_formatado = ($tempo_online < 3600)
    ? "Tempo Logado: " . floor($tempo_online / 60) . " minutos"
    : "Tempo Logado: " . sprintf("%02d:%02d horas", floor($tempo_online / 3600), floor(($tempo_online % 3600) / 60));

// Nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Ação de logout
if (isset($_POST['sairnexus'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Recupera ou armazena o código da venda na sessão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Salva todos os dados do formulário na sessão
    $_SESSION['dados_emissao'] = array_merge($_SESSION['dados_emissao'] ?? [], $_POST);

    // Adiciona cada campo individualmente, exceto `codigo_venda`
    $_SESSION['dados_emissao']['documento'] = $_POST['documento'] ?? '';
    $_SESSION['dados_emissao']['nomeCompleto'] = $_POST['nomeCompleto'] ?? '';
    $_SESSION['dados_emissao']['dataNascimento'] = $_POST['dataNascimento'] ?? '';
    $_SESSION['dados_emissao']['nomeMae'] = $_POST['nomeMae'] ?? '';
    $_SESSION['dados_emissao']['email'] = $_POST['email'] ?? '';
    $_SESSION['dados_emissao']['celular'] = $_POST['celular'] ?? '';
    $_SESSION['dados_emissao']['telefone1'] = $_POST['telefone1'] ?? '';
    $_SESSION['dados_emissao']['telefone2'] = $_POST['telefone2'] ?? '';
    $_SESSION['dados_emissao']['cep'] = $_POST['cep'] ?? '';
    $_SESSION['dados_emissao']['numero'] = $_POST['numero'] ?? '';
    $_SESSION['dados_emissao']['logradouro'] = $_POST['logradouro'] ?? '';
    $_SESSION['dados_emissao']['bairro'] = $_POST['bairro'] ?? '';
    $_SESSION['dados_emissao']['cidade'] = $_POST['cidade'] ?? '';
    $_SESSION['dados_emissao']['uf'] = $_POST['uf'] ?? '';
    $_SESSION['dados_emissao']['complemento1'] = $_POST['complemento1'] ?? '';
    $_SESSION['dados_emissao']['complemento2'] = $_POST['complemento2'] ?? '';
    $_SESSION['dados_emissao']['complemento3'] = $_POST['complemento3'] ?? '';
    $_SESSION['dados_emissao']['observacao'] = $_POST['observacao'] ?? '';

    // Redireciona para emissao_02.php se o botão de avançar foi clicado
    if (isset($_POST['avancar'])) {
        header("Location: emissao_02.php");
        exit();
    }
    if (isset($_POST['voltar'])) {
        header("Location: fila.php");
        exit();
    }
    if (isset($_POST['sair_venda'])) {
        header("Location: fila.php");
        exit();
    }

} elseif (isset($_GET['codigo_venda'])) {
    $_SESSION['dados_emissao']['codigo_venda'] = $_GET['codigo_venda'];
}

// Recupera o código da venda da sessão, se existir
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? null;

// Conexão com banco de dados
require 'conexao_admpgi.php';
$tipo_cliente = '';

// Carrega dados do banco de dados
if ($codigo_venda) {
    $query = "SELECT tipo_cliente, cep_cliente FROM vendas WHERE codigo_venda = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $codigo_venda);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tipo_cliente = htmlspecialchars($row['tipo_cliente']);
    } else {
        $tipo_cliente = "Não encontrado";
    }

    $cep_cliente = htmlspecialchars($row['cep_cliente']);
    // Verifica se o CEP tem 7 dígitos e adiciona um 0 na frente
if (strlen($cep_cliente) === 7) {
    $cep_cliente = str_pad($cep_cliente, 8, '0', STR_PAD_LEFT);
}
    $stmt->close();
}

$conn->close();

// Inclui validações e configurações adicionais
require 'valida_entrada.php';
require 'carregar_foto_perfil.php';

// Foto de perfil do usuário
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);
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
                        <div class="header-title">Dados Cadastrais</div>
                    </div>
                </div>

                <div class="panel2">
                    <h5 class="form-header">Dados do Cliente</h5>
                    <form action="emissao_02" method="POST">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
            <input type="hidden" name="codigo_venda" value="<?php echo $_SESSION['dados_emissao']['codigo_venda'] ?? ''; ?>">
            <input type="text" id="tipoDocumento" class="form-control" value="<?php echo $tipo_cliente; ?>" disabled>
        </div>
        <div class="col-md-3">
            <label for="documento" class="form-label">Documento</label>
            <input type="text" class="form-control" name="documento" id="documento" placeholder="000.000.000-00" maxlength="14" style="height: 38px;" oninput="mascaraCPF(this)" required value="<?php echo $_SESSION['dados_emissao']['documento'] ?? ''; ?>">
        </div>
        <div class="col-md-4">
            <label for="nomeCompleto" class="form-label">Nome Completo</label>
            <input type="text" class="form-control" name="nomeCompleto" id="nomeCompleto" placeholder="Nome Completo" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+" title="Apenas letras e espaços são permitidos" oninput="this.value = this.value.toUpperCase()" value="<?php echo $_SESSION['dados_emissao']['nomeCompleto'] ?? ''; ?>">
        </div>
        <div class="col-md-2">
            <label for="dataNascimento" class="form-label">Data de Nascimento</label>
            <input type="date" class="form-control" name="dataNascimento" id="dataNascimento" required value="<?php echo $_SESSION['dados_emissao']['dataNascimento'] ?? ''; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nomeMae" class="form-label">Nome da Mãe</label>
            <input type="text" class="form-control" name="nomeMae" id="nomeMae" placeholder="Nome da Mãe" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+" title="Apenas letras e espaços são permitidos" oninput="this.value = this.value.toUpperCase()" value="<?php echo $_SESSION['dados_emissao']['nomeMae'] ?? ''; ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="email" class="form-label">E-Mail</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="E-Mail" required oninput="this.value = this.value.toLowerCase()" value="<?php echo $_SESSION['dados_emissao']['email'] ?? ''; ?>">
        </div>
        <div class="col-md-2">
            <label for="celular" class="form-label">Celular (WhatsApp)</label>
            <input type="text" class="form-control" name="celular" id="celular" placeholder="Celular (WhatsApp)" oninput="mascaraCelular(this)" maxlength="16" required value="<?php echo $_SESSION['dados_emissao']['celular'] ?? ''; ?>">
        </div>
        <div class="col-md-2">
            <label for="telefone2" class="form-label">Telefone 1</label>
            <input type="text" class="form-control" name="telefone1" id="telefone1" placeholder="Telefone 2" oninput="mascaraTel(this)" maxlength="15" value="<?php echo $_SESSION['dados_emissao']['telefone1'] ?? ''; ?>">
        </div>
        <div class="col-md-2">
            <label for="telefone3" class="form-label">Telefone 2</label>
            <input type="text" class="form-control" name="telefone2" id="telefone2" placeholder="Telefone 3" oninput="mascaraTel(this)" maxlength="15" value="<?php echo $_SESSION['dados_emissao']['telefone2'] ?? ''; ?>">
        </div>
    </div>
    </div>
    <!-- Segundo Formulário -->
    <div class="panel2">
        <h5 class="form-header">Endereço da Venda</h5>
        <div class="row mb-3">
            <div class="col-md-2">
                <label for="cep" class="form-label">CEP</label>
                <input type="text" class="form-control" name="cep" id="cep" placeholder="CEP" required oninput="consultarCEP()"  maxlength="8" value="<?php echo $cep_cliente; ?>">
            </div>
            <div class="col-md-2">
                <label for="numero" class="form-label">Número</label>
                <input type="text" class="form-control" name="numero" id="numero" placeholder="Número" oninput="consultarCEP()" required value="<?php echo $_SESSION['dados_emissao']['numero'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <label for="logradouro" class="form-label">Logradouro</label>
                <input type="text" class="form-control" name="logradouro" id="logradouro" placeholder="Logradouro" required value="<?php echo $_SESSION['dados_emissao']['logradouro'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" class="form-control" name="bairro" id="bairro" placeholder="Bairro" required value="<?php echo $_SESSION['dados_emissao']['bairro'] ?? ''; ?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="cidade" class="form-label">Cidade</label>
                <input type="text" class="form-control" name="cidade" id="cidade" placeholder="Cidade" required value="<?php echo $_SESSION['dados_emissao']['cidade'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="uf" class="form-label">UF</label>
                <input type="text" class="form-control" name="uf" id="uf" placeholder="UF" required value="<?php echo $_SESSION['dados_emissao']['uf'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="complemento1" class="form-label">Complemento 1</label>
                <input type="text" class="form-control" name="complemento1" id="complemento1" placeholder="Complemento 1" value="<?php echo $_SESSION['dados_emissao']['complemento1'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="complemento2" class="form-label">Complemento 2</label>
                <input type="text" class="form-control" name="complemento2" id="complemento2" placeholder="Complemento 2" value="<?php echo $_SESSION['dados_emissao']['complemento2'] ?? ''; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="complemento3" class="form-label">Complemento 3</label>
                <input type="text" class="form-control" name="complemento3" id="complemento3" placeholder="Complemento 3" value="<?php echo $_SESSION['dados_emissao']['complemento3'] ?? ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="observacao" class="form-label">Observação</label>
                <input type="text" class="form-control" name="observacao" id="observacao" placeholder="Observação" value="<?php echo $_SESSION['dados_emissao']['observacao'] ?? ''; ?>">
            </div>
        </div>
        <input type="checkbox" style="display: none;" id="ftta_switch" name="ftta" value="sim" <?= isset($dados['ftta']) && $dados['ftta'] === 'sim' ? 'checked' : '' ?>>
        <input type="checkbox" style="display: none;" id="ftth_switch" name="ftth" value="sim" <?= isset($dados['ftth']) && $dados['ftth'] === 'sim' ? 'checked' : '' ?>>
    </div>
    <!-- Botões abaixo do formulário -->
    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-warning text-white fw-bold">TABULAR</button>
        <div>
            <button type="submit" name="sair_venda" class="btn btn-success btn_fila_sair text-white fw-bold me-2">SAIR SEM ALTERAR</button>
            <button type="submit" name="voltar" class="btn btn-danger text-white fw-bold me-2">VOLTAR</button>
            <button type="submit" class="btn btn-success text-white fw-bold">AVANÇAR</button>
        </div>
    </div>
</form>

                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../js/consulta_cep.js"></script>
        <script src="../js/mascara_cpf.js"></script>
        <script src="../js/mascara_celular.js"></script>
        <script src="../js/mascara_tel.js"></script>
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