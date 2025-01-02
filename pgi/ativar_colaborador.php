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
    ? floor($tempo_online / 60) . " minutos"
    : sprintf("%02d:%02d horas", floor($tempo_online / 3600), floor(($tempo_online % 3600) / 60));


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

// Define uma variável para controlar o estado da atualização
$statusAtualizado = false;

// Verifica se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Atualiza o status do colaborador para "ATIVO"
    $query = "UPDATE dados_colaborador SET STATUS_COLABORADOR = 'ATIVO' WHERE id = $id";

    if (mysqli_query($connect, $query)) {
        $statusAtualizado = true; // Atualização bem-sucedida
    } else {
        echo "<p class='alert alert-danger'>Erro ao ativar colaborador: " . mysqli_error($connect) . "</p>";
    }
} else {
    echo "<p class='alert alert-danger'>ID do colaborador não foi especificado.</p>";
}

// Define a variável de busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Remove pontos e traços da variável de busca para lidar com CPF formatado ou não
$busca = preg_replace("/[.\-]/", "", $busca);

// Consulta para buscar os usuários com base na busca por CPF (sem formatação) ou Nome
$query = "SELECT id, NOME, CPF, DT_NASCIMENTO, DT_ADMISSAO, PERFIL, STATUS_COLABORADOR FROM dados_colaborador";
if (!empty($busca)) {
    $query .= " WHERE REPLACE(REPLACE(CPF, '.', ''), '-', '') LIKE '%" . mysqli_real_escape_string($connect, $busca) . "%' 
                OR NOME LIKE '%" . mysqli_real_escape_string($connect, $busca) . "%'";
}
$result = mysqli_query($connect, $query);

// Verifica se a busca retornou resultados
//$temResultados = mysqli_num_rows($result) > 0;

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
    <title>RH Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/tabelas.css">
    <style>
        .panel {
            padding: 1px;
        }

        .form-control.search-input,
        .btn.search-btn {
            height: 38px;
            padding: 0 12px;
            font-size: 14px;
            border-radius: 4px;
        }

        .d-flex.align-items-center {
            gap: 8px;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal-dialog {
            z-index: 1050 !important;
        }
        .same-size-btn {
            width: 100px; /* Set a fixed width that suits both buttons */
            text-align: center;
        }

    </style>
</head>

<body>


    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars" id="toggle-icon" style="color: #697891; margin-right: 5px;"></i>
            <span class="menu-text" style="display: none;">NEXUS</span>
        </button>

        <i class="fa-solid fa-house" onclick="window.location.href='menu.php'">
            <span class="menu-text"><a href="menu">Página inicial</a></span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown">
            <i class="fa-solid fa-phone" onclick="toggleDropdown()">
                <span class="menu-text" style="display: none;">Backoffice</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenu">
                <a href="paineltfp.php" class="page-link">Painel TFP</a>
                <a href="#">Pós venda</a>
                <a href="#">Fila de vendas</a>
            </div>
        </div>

        <i class="fa-solid fa-shapes">
            <span class="menu-text">Dashboards</span>
        </i>

        <i class="fa-solid fa-pen-to-square">
            <span class="menu-text">Comercial</span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown-rh">
            <i class="fa-solid fa-user-group active" onclick="toggleDropdownRh()">
                <span class="menu-text" style="display: none;">Recursos Humanos</span>
            </i>
            <!-- Submenu -->
            <div class="submenu" id="submenurh">
                <a href="painelrh_.php" class="page-link">Cadastrar Colaborador</a>
            </div>
        </div>

        <i class="fas fa-cog" onclick="window.location.href='configuracoes.php'">
            <span class="menu-text">Configurações</span>
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
                <span id="tempo-logado">Logado há: <?php echo $tempo_online_formatado; ?></span>
                <div class="vertical-separator"></div>
                <span id="user-name"><?php echo $_SESSION['nome']; ?></span>
                <img src="<?php echo $foto_perfil; ?>" alt="" style="cursor: pointer; border: 3px solid #ffa500;">

                <!-- Submenu for Logout -->
                <div class="submenu-logout" id="submenu-logout">
                    <form method="POST" action="sairnexus" id="FormSair">
                        <button type="submit"
                            style="background:none; border:none; width:150px; padding:10px; cursor:pointer; display: flex; align-items: center;">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="color: red; margin-right: 8px;"></i>
                            <strong>Sair</strong>
                        </button>
                    </form>
                    <form method="POST" action="configuracoes.php" id="FormMudarFoto" style="margin-top: 10px;">
                        <button type="submit"
                            style="background:none; border:none; width:150px; padding:10px; cursor:pointer; display: flex; align-items: center;">
                            <i class="fa-solid fa-image-portrait" style="color: #1695c0; margin-right: 8px;"></i>
                            <strong>Mudar foto</strong>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="content">
            <div class="panel" style="padding: 1px;">
                <div class="header-container">
                    <h2>&nbsp;<i class="fa-solid fa-list-check"></i> Lista de Colaboradores</h2>
                    <div class="button-container">
                        <form method="GET" class="d-flex align-items-center gap-10">
                            <input type="text" name="busca" class="form-control search-input"
                                placeholder="Buscar por CPF ou Nome" value="<?php echo htmlspecialchars($busca); ?>">
                            <button type="submit" class="btn btn-primary search-btn">Buscar</button>
                            <button type="button" class="btn btn-secondary search-btn"
                                onclick="window.location.href='colaboradores_rh.php'"><i
                                    class="fa-solid fa-eraser"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="panel2">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($temResultados) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td id='no-left-border-middle'>" . htmlspecialchars($row['NOME']) . "</td>";
                                echo "<td id='no-left-border'>" . htmlspecialchars($row['CPF']) . "</td>";
                                echo "<td id='no-left-border'>" . htmlspecialchars($row['PERFIL']) . "</td>";
                                echo "<td id='no-left-border'>" . htmlspecialchars($row['STATUS_COLABORADOR']) . "</td>";

                                // Define o botão de ativar/inativar com base no status
                                if ($row['STATUS_COLABORADOR'] == 'ATIVO') {
                                    echo "<td class='action-buttons' id='no-left-border-middle'>
                                <a href='#' onclick='confirmarInativacao(" . $row['id'] . ")' class='btn btn-danger btn-sm same-size-btn'>Inativar</a>
                                <a href='editar_colaborador.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Editar</a>
                              </td>";
                                } else {
                                    echo "<td class='action-buttons' id='no-left-border'>
                                <a href='#' onclick='confirmarAtivacao(" . $row['id'] . ")' class='btn btn-success btn-sm same-size-btn'>Ativar</a>
                                <a href='editar_colaborador.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Editar</a>
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
    <!-- Modal de sucesso -->
    <div class="modal fade" id="modalSucesso" tabindex="-1" role="dialog" aria-labelledby="modalSucessoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSucessoLabel">Sucesso</h5>
                </div>
                <div class="modal-body">
                    Colaborador ativado com sucesso!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="redirecionar()">OK</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Exibe o modal automaticamente se o status foi atualizado com sucesso
        <?php if ($statusAtualizado): ?>
            $(document).ready(function() {
                $('#modalSucesso').modal('show');
            });
        <?php endif; ?>

        // Função para redirecionar após fechar o modal
        function redirecionar() {
            window.location.href = 'colaboradores_rh.php';
        }
    </script>
<script>
            // Update Logged Time
            let minutosLogado = <?php echo floor($tempo_online / 60); ?>;
            function atualizarTempo() {
                minutosLogado++;
                const tempoLogadoElem = document.getElementById('tempo-logado');
                if (minutosLogado < 60) {
                    tempoLogadoElem.innerText = minutosLogado + " minutos";
                } else {
                    let horas = Math.floor(minutosLogado / 60);
                    let minutos = minutosLogado % 60;
                    tempoLogadoElem.innerText = `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')} horas`;
                }
            }
            setInterval(atualizarTempo, 60000);

            // Toggle Sidebar and Submenu
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const toggleIcon = document.getElementById('toggle-icon');
                sidebar.classList.toggle('expanded');
                const menuTexts = document.querySelectorAll('.menu-item .menu-text');
                menuTexts.forEach(menuText => menuText.style.display = sidebar.classList.contains('expanded') ? 'flex' : 'none');
                toggleIcon.classList.toggle('fa-bars');
                toggleIcon.classList.toggle('fa-chevron-left');
            }

            function toggleLogoutMenu() {
                const submenu = document.getElementById('submenu-logout');
                submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
            }

            document.addEventListener('click', function (event) {
                const userProfile = document.querySelector('.user-profile');
                const submenu = document.getElementById('submenu-logout');
                if (!userProfile.contains(event.target)) {
                    submenu.style.display = 'none';
                }
            });

            // Submenu Display
            function showSubmenu(element) {
                const submenu = element.querySelector(".submenu-card");
                submenu.style.display = "block";
                setTimeout(() => submenu.style.opacity = "1", 10);
                element.style.height = "200px";
                adjustSidebarHeight();
            }

            function hideSubmenu(element) {
                const submenu = element.querySelector(".submenu-card");
                submenu.style.opacity = "0";
                setTimeout(() => submenu.style.display = "none", 300);
                element.style.height = "120px";
                adjustSidebarHeight();
            }

            function adjustSidebarHeight() {
                const sidebar = document.querySelector('.sidebar');
                let totalHeight = 800;
                const cards = sidebar.querySelectorAll('.card');
                cards.forEach(card => totalHeight += card.offsetHeight);
                sidebar.style.height = totalHeight + 'px';
            }

            function toggleDropdown() {
                document.querySelector('.menu-item.dropdown').classList.toggle('active');
            }

            function toggleDropdownrh() {
                document.querySelector('.menu-item.dropdown-rh').classList.toggle('active');
            }
        </script>

        <script>
            // Fetch IP Address
            fetch('https://api.ipify.org?format=json')
                .then(response => response.json())
                .then(data => document.getElementById('ip').textContent = `IP: ${data.ip}`)
                .catch(error => console.log('Erro ao obter IP:', error));

            // Show Date and Time
            function atualizarDataHora() {
                const agora = new Date();
                document.getElementById('datetime').textContent = `${agora.toLocaleDateString('pt-BR')} ${agora.toLocaleTimeString('pt-BR')}`;
            }
            setInterval(atualizarDataHora, 1000);
        </script>

        <script>
            $(document).ready(function () {
                loadPage(location.pathname);

                $('a.page-link').click(function (e) {
                    e.preventDefault();
                    var page = $(this).attr('href');
                    loadPage(page);
                });

                function loadPage(page) {
                    $('#content').fadeOut(300, function () {
                        $.ajax({
                            url: page,
                            success: function (data) {
                                $('#content').html($(data).find('#content').html()).fadeIn(300);
                                history.pushState({ page: page }, '', page);
                            },
                            error: function () {
                                $('#content').html('<p>Erro ao carregar a página!</p>').fadeIn(300);
                            }
                        });
                    });
                }

                $(window).on('popstate', function () {
                    loadPage(location.pathname);
                });
            });
        </script>

        <script>
            function uploadFile() {
                const fileInput = document.getElementById('fileUpload');
                const file = fileInput.files[0];

                if (file) {
                    const formData = new FormData();
                    formData.append('fileUpload', file);

                    fetch('upload.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(result => {
                            alert(result.message);
                            if (!result.error) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao enviar o arquivo.');
                        });
                }
            }
        </script>

        <script>
            document.getElementById("PERFIL").addEventListener("change", function () {
                this.value = this.value.toUpperCase();
            });
        </script>
</body>
</html>
