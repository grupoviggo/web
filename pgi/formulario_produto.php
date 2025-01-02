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
    <title>CADASTRO COLABORADOR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="table-stylesheet" rel="stylesheet" href="../css/tabelas.css">
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
                <h2><i class="fas fa-user-plus"></i> Cadastro de Produto
                    <div class="right-info">
                        <span id="ip" class="info"></span><span class="info">&nbsp; | &nbsp;</span>
                        <span id="datetime" class="info"></span>
                    </div>
                </h2>
            </div>

            <!-- Formulário de Cadastro -->
            <div class="panel2" style="overflow: hidden;">
                <form method="POST" action="cadastro_colaborador" class="p-4 bg-light rounded bg-custom">
                    <div class="row">
                        <!-- Coluna Esquerda -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="produto" class="form-label">Produto:</label>
                                <input type="text" class="form-control" id="produto" name="PRODUTO"
                                    placeholder="Insira o nome do produto" maxlength="50"
                                    style="text-transform: uppercase; height: 38px;"
                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').toUpperCase()"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="receita" class="form-label">Valor Receita:</label>
                                <input type="text" class="form-control" id="receita" name="RECEITA"
                                    placeholder="Receita do produto" maxlength="50"
                                    style=" height: 38px;"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="fator" class="form-label">Valor Fator:</label>
                                <input type="text" class="form-control" id="fator" name="FATOR"
                                    placeholder="Fator produto" maxlength="50"
                                    style=" height: 38px;"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="fatorcalculo" class="form-label">Valor Fator:</label>
                                <input type="text" class="form-control" id="fatorcalculo" name="FATORCALCULO"
                                    placeholder="Fator produto" maxlength="50"
                                    style=" height: 38px;"
                                    required>
                            </div>


                        </div>

                        <!-- Coluna Direita -->
                        <div class="col-md-6">


                        <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria:</label>
                                <select class="form-select" id="categoria" name="categoria" style="height: 38px;" required autocomplete="off">
                                    <option value="" selected>Selecione uma Categoria</option>
                                    <option value="FIXA">  FIXA   </option>
                                    <option value="MOVEL"> MÓVEL       </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="categoria" class="form-label">Vivo Total:</label>
                                <select class="form-select" id="total" name="total" style="height: 38px;" required autocomplete="off">
                                    <option value="" selected>Produto de Vivo Total?</option>
                                    <option value="TOTAL"> TOTAL   </option>
                                    <option value="-">      -      </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="tipo" class="form-label">Produto Tipo:</label>
                                <select class="form-select" id="tipo" name="tipo" style="height: 38px;" required autocomplete="off">
                                    <option value="" selected>Produto Tipo</option>
                                    <option value="CELULAR"> CELULAR   </option>
                                    <option value="DADOS">      DADOS     </option>
                                    <option value="SVA">      SVA     </option>
                                    <option value="TV">      TV     </option>
                                    <option value="VOZ">      VOZ     </option>
                                    <option value="ADICIONAL">      ADICIONAL  </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="clientdoc" class="form-label">Cliente Tipo Documento:</label>
                                <select class="form-select" id="clientedoc" name="total" style="height: 38px;" required autocomplete="off">
                                    <option value="" selected>B2C ou B2B</option>
                                    <option value="B2C"> B2C </option>
                                    <option value="B2B">      B2B     </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-end mt-3">
                      <a href="carga-lotecolaboradores" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success ms-2">Cadastrar</button>
                        <a href="colaboradores_rh" class="btn btn-primary ms-2">Ver Produtos</a>
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

// Atualizar o select de cargos baseado no perfil selecionado
perfilSelect.addEventListener("change", () => {
    const perfilSelecionado = perfilSelect.value;
    console.log(`Perfil selecionado: ${perfilSelecionado}`); // Log do perfil selecionado

    // Limpar os cargos atuais
    cargoSelect.innerHTML = '<option value="" selected>Selecione um departamento</option>';
    cargoSelect.disabled = true;

    // Preencher opções se houver perfil válido
    if (perfilSelecionado && departamentoPorPerfil[perfilSelecionado]) {
        const cargos = departamentoPorPerfil[perfilSelecionado];
        console.log(`Cargos encontrados: ${cargos}`); // Log dos cargos encontrados

        cargos.forEach((departamento) => {
            const option = document.createElement("option");
            option.value = departamento;
            option.textContent = departamento;
            cargoSelect.appendChild(option);
        });

        cargoSelect.disabled = false; // Habilitar após preencher
    } else {
        console.error("Nenhum cargo encontrado para o perfil selecionado"); // Log de erro
    }
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