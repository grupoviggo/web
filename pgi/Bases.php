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

// Conexão com o banco de dados
$connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$connect) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define a variável de busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';


$query = "SELECT id_base, CONSULTOR_BASE_NOME, STATUS_BASE FROM bases";
if (!empty($busca)) {
    $query .= " WHERE CONSULTOR_BASE_NOME LIKE '%" . mysqli_real_escape_string($connect, $busca) . "%'";
}

$result = mysqli_query($connect, $query);
if (!$result) {
    die("Erro na consulta SQL: " . mysqli_error($connect));
}

// Verifica se a busca retornou resultados
$temResultados = mysqli_num_rows($result) > 0;

// Incluiu a Validação de senha alterada - Primeiro Acesso!
require 'valida_entrada.php';  

// Inclui o arquivo que carrega foto de perfil
require 'carregar_foto_perfil.php';
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BASES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="table-stylesheet" rel="stylesheet" href="../css/tabelas.css">
    <link id="tab-stylesheet" rel="stylesheet" href="../css/tab_fila.css">
    <style>
        .table .custom_td{
            text-align: left;
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


        <i class="fa-solid fa-house" onclick="window.location.href='menu.php'">
            <span class="menu-text">Página inicial</span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown">
            <i class="fi fi-sr-user-headset" onclick="toggleDropdown(this)">
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
            <i class="fi fi-sr-users active" onclick="toggleDropdownrh()">
                <span class="menu-text" style="display: none;">Gestão de Pessoas</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenurh">
            <a onclick="window.location.href='painelrh.php'" class="page-link">Cadastro de colaboradores</a>
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
                <img src="<?php echo $foto_perfil; ?>" alt="" style="cursor: pointer; border: 3px solid #ffa500;">

                <!-- Submenu para Logout e Configurações -->
                <?php include 'submenu.php'; ?>
            </div>
        </div>
        <!-- Conteúdo principal -->
        <div class="content">
            <div class="panel">
                <div class="header-container_3">
                    <h2 style="margin: 0;">&nbsp;<i class="fa-solid fa-list-check"></i> Lista de Bases</h2>
                    <div class="button-container">
                    <form method="GET" class="d-flex align-items-center gap-6 custom-form">
                            <input type="text" name="busca" class="form-control search-input" placeholder=" Buscar base"
                                value="<?php echo htmlspecialchars($busca); ?>" style="width: 200px;">
                            <button type="submit" class="btn btn-primary search-btn" style="width: 60px;">
                                <i class="fa-solid fa-magnifying-glass"></i></button>
                            <button type="button" class="btn btn-secondary eraser-btn" style="width: 60px;" onclick="window.location.href='Bases.php'">
                                <i class="fa-solid fa-delete-left"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="panel2">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Base</th>
                            <th>Status</th>
                            <th></th>
                            <th></th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($temResultados) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='custom'>";
                                echo "<td class='no-left-border-left custom_td'>" . htmlspecialchars($row['CONSULTOR_BASE_NOME']) . "</td>";
                                echo "<td id='no-right-border'>" . htmlspecialchars($row['STATUS_BASE']) . "</td>";
                                echo "<td id='no-right-border'></td>";
                                echo "<td id='no-right-border'></td>";

                                // Define o botão de ativar/inativar com base no status
                                if ($row['STATUS_BASE'] == 'ATIVO') {
                                    echo "<td class='action-buttons no-left-border-right'>
                                <a href='#' onclick='confirmarInativacao(" . $row['id_base'] . ")' class='btn btn-danger btn-sm same-size-btn'>Inativar</a>
                                <a href='editar_base.php?id_base=" . $row['id_base'] . "' class='btn btn-primary btn-sm'>Editar</a>
                              </td>";
                                } else {
                                    echo "<td class='action-buttons no-left-border-right'>
                                <a href='#' onclick='confirmarAtivacao(" . $row['id_base'] . ")' class='btn btn-success btn-sm same-size-btn'>Ativar</a>
                                <a href='editar_base.php?id_base=" . $row['id_base'] . "' class='btn btn-primary btn-sm'>Editar</a>
                              </td>";
                                }

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Nenhum usuário encontrado.</td></tr>";
                        }
                        // Fecha a conexão com o banco de dados
                        mysqli_close($connect);
                        ?>
                    </tbody>
                </table>
            </div>
            </form>
        </div>

        <!-- Modal de Dados Não Encontrados -->
        <div class="modal fade" id="noDataModal" tabindex="-1" aria-labelledby="noDataModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="noDataModalLabel">Atenção</h5>
                    </div>
                    <div class="modal-body">
                        DADOS NÃO ENCONTRADO.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Confirmar Inativação -->
        <div class="modal fade" id="confirmInativarModal" tabindex="-1" aria-labelledby="confirmInativarModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmInativarModalLabel">Confirmar Inativação</h5>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja inativar esta base?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmInativarBtn">Inativar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Confirmar Ativação -->
        <div class="modal fade" id="confirmAtivarModal" tabindex="-1" aria-labelledby="confirmAtivarModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmAtivarModalLabel">Confirmar Ativação</h5>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja ativar esta base?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="confirmAtivarBtn">Ativar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Exibe o modal de dados não encontrados se não houver resultados na busca
            <?php if (!$temResultados && !empty($busca)): ?>
                $(document).ready(function () {
                    $('#noDataModal').modal('show');
                });
            <?php endif; ?>
        </script>

        <script>
            function confirmarInativacao(id_base) {
                var confirmar = confirm("Tem certeza de que deseja inativar esta base?");
                if (confirmar) {
                    window.location.href = "Inativar_base.php?id_base=" + id_base;
                }
            }

            function confirmarAtivacao(id_base) {
                var confirmar = confirm("Tem certeza de que deseja ativar esta base?");
                if (confirmar) {
                    window.location.href = "ativar_base.php?id_base=" + id_base;
                }
            }
        </script>

        <script>
            // Função para abrir o modal de confirmação de inativação e armazenar o ID
            function confirmarInativacao(id_base) {
                $('#confirmInativarModal').modal('show');
                $('#confirmInativarBtn').data('id_base', id_base); // Armazena o ID no botão de confirmação
            }

            // Função para abrir o modal de confirmação de ativação e armazenar o ID
            function confirmarAtivacao(id_base) {
                $('#confirmAtivarModal').modal('show');
                $('#confirmAtivarBtn').data('id_base', id_base); // Armazena o ID no botão de confirmação
            }

            // Evento de clique no botão de confirmação de inativação
            $('#confirmInativarBtn').click(function () {
                var id_base = $(this).data('id_base');
                window.location.href = "Inativar_base.php?id_base=" + id_base;
            });

            // Evento de clique no botão de confirmação de ativação
            $('#confirmAtivarBtn').click(function () {
                var id_base = $(this).data('id_base');
                window.location.href = "ativar_base.php?id_base=" + id_base;
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
    const tableStylesheet = document.getElementById('table-stylesheet'); // Novo elemento para tabelas
    const tabStylesheet = document.getElementById('tab-stylesheet');
    const tabelaMista = document.getElementById('tabela-mista'); // A tabela com o id 'tabela-mista'

    // Função para aplicar o tema
    function applyTheme(theme) {
        if (theme === 'dark') {
            themeStylesheet.setAttribute('href', '../css/principal_escuro.css');
            tableStylesheet.setAttribute('href', '../css/tabelas_escuro.css'); // Aplica o CSS das tabelas
            tabStylesheet.setAttribute('href', '../css/tab_fila_escuro.css');
            toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo claro';

            // Troca a classe da tabela para 'table-dark'
            tabelaMista.classList.add('table-dark'); // Adiciona a classe 'table-dark'
            tabelaMista.classList.remove('table-light'); // Remove qualquer classe 'table-light' existente (caso tenha)
        } else {
            themeStylesheet.setAttribute('href', '../css/principal.css');
            tableStylesheet.setAttribute('href', '../css/tabelas.css'); // Retorna ao CSS padrão das tabelas
            tabStylesheet.setAttribute('href', '../css/tab_fila.css');
            toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo escuro';

            // Remove a classe 'table-dark' quando o tema for claro
            tabelaMista.classList.remove('table-dark');

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

       