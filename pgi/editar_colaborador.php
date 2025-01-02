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

// Incluiu a Validação de senha alterada - Primeiro Acesso!
require 'valida_entrada.php';

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

// Inclui o arquivo que carrega foto de perfil
require 'carregar_foto_perfil.php';
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);

// Configurações de conexão com o banco de dados
$servidor = "200.147.61.78"; // Endereço do servidor de banco de dados
$usuario = "viggoadm2"; // Usuário do banco de dados
$senha = "Viggo2024@"; // Senha do banco de dados
$banco = "nexus"; // Nome do banco de dados

// Cria a conexão
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
//if ($conn->connect_error) {
//    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
//}

// Consulta ao banco
$consultoresAtivos = [];
$query = "SELECT CONSULTOR_BASE_NOME FROM bases WHERE STATUS_BASE = 'ATIVO'"; // Ajuste 'sua_tabela' para o nome correto
$resultado = $conexao->query($query);


if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $consultoresAtivos[] = $row['CONSULTOR_BASE_NOME'];
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR COLABORADOR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="table-stylesheet" rel="stylesheet" href="../css/tabelas.css">
</head>

<body>

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
                <i class="fi fi-sr-user-headset" onclick="toggleDropdown()">
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
                <i class="fa-solid fa-user-group active" onclick="toggleDropdownrh()">
                    <span class="menu-text" style="display: none;">Recursos Humanos</span>
                </i>
                <!-- Submenu -->
                <div class="submenu" id="submenurh">
                    <a href="painelrh" class="page-link" onclick="window.location.href='painelrh'">Cadastrar
                        Colaborador</a>
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
                    <img src="<?php echo $foto_perfil; ?>" alt="" style="cursor: pointer; border: 3px solid #ffa500;">

                    <!-- Submenu para Logout e Configurações -->
                    <?php include 'submenu.php'; ?>
                </div>
            </div>


            <!-- Conteúdo principal -->
            <div class="content">
                <div class="panel">
                    <h2><i class="fas fa-user-plus"></i> Editar informações do colaborador
                        <div class="right-info">
                            <span id="ip" class="info"></span><span class="info">&nbsp; | &nbsp;</span>
                            <span id="datetime" class="info"></span>
                        </div>
                    </h2>
                </div>


                <?php
                // Conexão com o banco de dados
                $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

                if (!$connect) {
                    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
                }

                // Verifica se o ID foi passado na URL
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];

                    // Consulta para buscar os dados do usuário específico
                    $query = "SELECT * FROM dados_colaborador WHERE id = $id";
                    $result = mysqli_query($connect, $query);

                    if (mysqli_num_rows($result) == 1) {
                        $user = mysqli_fetch_assoc($result);
                    } else {
                        echo "<p class='alert alert-danger'>Usuário não encontrado.</p>";
                        exit;
                    }
                } else {
                    echo "<p class='alert alert-danger'>ID do usuário não foi especificado.</p>";
                    exit;
                }
                ?>

                <!-- Formulário de Cadastro -->
                <div class="panel2" style="overflow: hidden;">
                    <form method="POST" action="atualizar_colaborador" class="p-4 bg-light rounded bg-custom">
                        <div class="row">
                            <!-- Coluna Esquerda -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome:</label>
                                    <input type="text" class="form-control" id="NOME" name="NOME"
                                        placeholder="Nome Completo" maxlength="50"
                                        style="text-transform: uppercase; height: 38px;"
                                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').toUpperCase()"
                                        value="<?php echo htmlspecialchars($user['NOME'], ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF:</label>
                                    <input type="text" class="form-control" id="CPF" name="CPF"
                                        placeholder="000.000.000-00" maxlength="14" style="height: 38px;"
                                        oninput="mascaraCPF(this)"
                                        value="<?php echo htmlspecialchars($user['CPF'], ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail:</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="email@exemplo.com" maxlength="50" style="height: 38px;"
                                        value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="data_nascimento" class="form-label">Data de Nascimento:</label>
                                    <input type="date" class="form-control" id="DATA_NASCIMENTO" name="DATA_NASCIMENTO"
                                        style="height: 38px;"
                                        value="<?php echo htmlspecialchars($user['DT_NASCIMENTO'], ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="data_admissao" class="form-label">Data de Admissão:</label>
                                    <input type="date" class="form-control" id="DATA_ADMISSAO" name="DATA_ADMISSAO"
                                        style="height: 38px;"
                                        value="<?php echo htmlspecialchars($user['DT_ADMISSAO'], ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>
                            </div>

                            <!-- Coluna Direita -->
                            <div class="col-md-6">
                                <div class="mb-3"> <label for="telefone" class="form-label">Telefone:</label> <input
                                        type="tel" class="form-control" id="telefone" name="telefone"
                                        placeholder="(00) 00000-0000" maxlength="15" style="height: 38px;" required
                                        oninput="mascaraTelefone(this)"
                                        value="<?php echo htmlspecialchars($user['telefone'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="consultor_base_nome" class="form-label">Consultor Base Nome:</label>
                                    <select class="form-select" id="CONSULTOR_BASE_NOME" name="CONSULTOR_BASE_NOME"
                                        style="height: 38px;" required>
                                        <option value="" selected>Selecione</option>
                                        <?php foreach ($consultoresAtivos as $consultor): ?>
                                            <option value="<?php echo htmlspecialchars($consultor, ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php if ($user['CONSULTOR_BASE_NOME'] == $consultor)
                                                    echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($consultor, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="cargo" class="form-label">Perfil:</label>
                                    <select class="form-select" id="cargo" name="cargo" style="height: 38px;" required
                                        autocomplete="off">
                                        <option value="" selected>Selecione um perfil</option>
                                        <option value="ADMINISTRATIVO" <?php if ($user['cargo'] == 'ADMINISTRATIVO')
                                            echo 'selected'; ?>>ADMINISTRATIVO</option>
                                        <option value="COMERCIAL" <?php if ($user['cargo'] == 'COMERCIAL')
                                            echo 'selected'; ?>>COMERCIAL</option>
                                        <option value="AUDITORIA" <?php if ($user['cargo'] == 'AUDITORIA')
                                            echo 'selected'; ?>>BKO/AUDITORIA</option>
                                        <option value="POSVENDA" <?php if ($user['cargo'] == 'POSVENDA')
                                            echo 'selected'; ?>>BKO/PÓS-VENDA</option>
                                        <option value="GERENCIAL" <?php if ($user['cargo'] == 'GERENCIAL')
                                            echo 'selected'; ?>>GERENCIAL</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="departamento" class="form-label">Departamento:</label>
                                    <select class="form-select" id="departamento" name="departamento"
                                        style="height: 38px;" required autocomplete="off">
                                        <option value="" selected>Selecione um departamento</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="nivel" class="form-label">Nível:</label>
                                    <select class="form-select" id="nivel" name="nivel" style="height: 38px;">
                                        <option value="" selected>Nível</option>
                                        <option value="1" <?php if ($user['nivel'] == '1')
                                            echo 'selected'; ?>>1</option>
                                        <option value="2" <?php if ($user['nivel'] == '2')
                                            echo 'selected'; ?>>2</option>
                                        <option value="3" <?php if ($user['nivel'] == '3')
                                            echo 'selected'; ?>>3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary ms-2">Salvar alterações</button>
                            <a href="colaboradores_rh" class="btn btn-secondary ms-2">Cancelar</a>
                        </div>
                    </form>
                </div>
                <!-- Fim do Formulário de Cadastro -->

            </div>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script src="../js/mascara_cpf.js"></script>
            <script src="../js/Rh.js"></script>
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
        const tableStylesheet = document.getElementById('table-stylesheet'); // Novo elemento para tabelas

        // Função para aplicar o tema
        function applyTheme(theme) {
            if (theme === 'dark') {
                themeStylesheet.setAttribute('href', '../css/principal_escuro.css');
                tableStylesheet.setAttribute('href', '../css/tabelas_escuro.css'); // Aplica o CSS das tabelas
                toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo claro';
            } else {
                themeStylesheet.setAttribute('href', '../css/principal.css');
                tableStylesheet.setAttribute('href', '../css/tabelas.css'); // Retorna ao CSS padrão das tabelas
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

    <script>
        const departamentoAtual = "<?php echo htmlspecialchars($user['departamento'], ENT_QUOTES, 'UTF-8'); ?>";
    </script>

    <script>
        // Mapear os cargos de acordo com o perfil
        const departamentoPorPerfil = {
            ADMINISTRATIVO: ["RH/DP"],
            COMERCIAL: [
                "VENDEDOR",
                "LÍDER",
                "SUPERVISOR",
                "COORDENADOR",
                "GERENTE BASE",
                "GERENTE TERRITÓRIO",
                "DIRETOR",
            ],
            AUDITORIA: [
                "GERENTE BKO",
                "COORDENADOR BKO",
                "SUPERVISOR BKO",
                "FOCAL",
                "AUDITOR",
                "PÓS-VENDA",
            ],
            POSVENDA: [
                "GERENTE BKO",
                "COORDENADOR BKO",
                "SUPERVISOR BKO",
                "FOCAL",
                "PÓS-VENDA",
            ],
            GERENCIAL: [
                "GERENCIAL GERAL",
                "GERENCIAL ADMINISTRATIVO",
                "GERENCIAL BKO",
                "GERENCIAL COMERCIAL",
            ],
        };

        // Capturar os elementos de Perfil e Cargo
        const perfilSelect = document.getElementById("cargo"); // ID correto do elemento de Perfil
        const cargoSelect = document.getElementById("departamento"); // ID correto do elemento de Cargo

        // Preencher o select de cargos baseado no perfil selecionado
        const preencherCargos = () => {
            const perfilSelecionado = perfilSelect.value;

            // Limpar os cargos atuais
            cargoSelect.innerHTML = '<option value="" selected>Selecione um departamento</option>';
            cargoSelect.disabled = true;

            // Preencher opções se houver perfil válido
            if (perfilSelecionado && departamentoPorPerfil[perfilSelecionado]) {
                const cargos = departamentoPorPerfil[perfilSelecionado];
                cargos.forEach((departamento) => {
                    const option = document.createElement("option");
                    option.value = departamento;
                    option.textContent = departamento;
                    // Selecionar o valor atual
                    if (departamento === departamentoAtual) {
                        option.selected = true;
                    }
                    cargoSelect.appendChild(option);
                });

                cargoSelect.disabled = false; // Habilitar após preencher
            }
        };

        // Atualizar cargos ao alterar o perfil
        perfilSelect.addEventListener("change", preencherCargos);

        // Preencher cargos ao carregar o formulário
        window.addEventListener("DOMContentLoaded", () => {
            preencherCargos();
        });


    </script>

    <script>
        const cargoSelect = document.getElementById("cargo");

        // Converter o valor selecionado para maiúsculo ao mudar a seleção
        cargoSelect.addEventListener("change", () => {
            cargoSelect.value = cargoSelect.value.toUpperCase();
        });
    </script>

    <script>
        // Função para formatar o telefone com DDD
        function mascaraTelefone(input) {
            const value = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos

            if (value.length <= 10) {
                // Formato: (00) 0000-0000
                input.value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else {
                // Formato: (00) 00000-0000
                input.value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            }
        }
    </script>

    <script>
        // Função para formatar o telefone com DDD
        function mascaraTelefone(input) {
            const value = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos

            if (value.length <= 10) {
                // Formato: (00) 0000-0000
                input.value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else {
                // Formato: (00) 00000-0000
                input.value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            }
        }
    </script>

</html>