<?php
// Inclui o arquivo de conexão com o banco de dados
require 'conexao_admpgi.php';

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login");
    exit();
}

// Obtenha informações da sessão
$nivel_acesso = $_SESSION['nivel'];
$username_acesso = $_SESSION['nome'];
$user_id = $_SESSION['ID'];

if (!isset($_SESSION['tempo_login'])) {
    $_SESSION['tempo_login'] = time();
}

$tempo_online = time() - $_SESSION['tempo_login'];
$tempo_online_formatado = ($tempo_online < 3600)
    ? "Tempo Logado: " . floor($tempo_online / 60) . " minutos"
    : "Tempo Logado: " . sprintf("%02d:%02d horas", floor($tempo_online / 3600), floor(($tempo_online % 3600) / 60));


// Consulta para buscar os dados do usuário logado
$sql = "SELECT nome, cpf, email, cargo, departamento, foto_perfil FROM usuarios_pgi WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();



if (isset($_GET['reset_sucesso']) && $_GET['reset_sucesso'] == '1') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#modalSucesso').modal('show');
        });
    </script>";
}


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
    <title>CONFIGURACOES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS do Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/config.css">
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
                        <a href="fila.php" class="page-link">Fila de vendas</a>
                        <a href="importar_zip.php" class="page-link">Importação PAP</a>
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
                <a onclick="window.location.href='painelrh.php'"                class="page-link">Cadastro de colaboradores</a>
                <a onclick="window.location.href='colaboradores_rh.php'"        class="page-link">Gestão de usuários</a>
                <a onclick="window.location.href='carga-lotecolaboradores.php'" class="page-link">Opção de Cadastro</a>
                <a onclick="window.location.href='Painel_hierarquia.php'"       class="page-link">Hierarquia</a>
                <a href="#" class="page-link">Relatórios</a>
            </div>
        </div>


        <i class="fas fa-cog page-link active" >
            <span class="menu-text"><a class="page-link">Configurações</a></span>
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
                <!-- Display time on the far left -->
                <div class="vertical-separator"></div> <!-- Vertical separator -->
                <span id="user-name"><?php echo $_SESSION['nome']; ?></span> <!-- User name in the middle -->
                <img src="<?php echo $foto_perfil; ?>" alt=""
                    style="cursor: pointer; border-radius: 50%; border: 3px solid #53bdc1;">
                <!-- User image on the far right -->

                <!-- Submenu para Logout e Configurações -->
                <?php include 'submenu.php'; ?>
            </div>
        </div>
        <!-- Conteúdo principal -->
        <div class="content">
            <div class="panel">
                <h2><i class="fa-solid fa-user-pen"></i> Informações do usuário</h2>
            </div>
            <p>
            <div class="panel2">
                <!-- Exibir informações do usuário -->
                <p><strong>Nome:</strong> <?php echo $userData['nome']; ?></p>
                <p><strong>CPF:</strong> <?php echo $userData['cpf']; ?></p>
                <p><strong>Email:</strong> <?php echo $userData['email']; ?></p>
                <p><strong>Cargo:</strong> <?php echo $userData['cargo']; ?></p>
                <p><strong>Departamento:</strong> <?php echo $userData['departamento']; ?></p>

                <?php if (!empty($userData['foto_perfil'])): ?>
                    <p><strong>Foto de Perfil:</strong></p>
                    <img src="<?php echo $userData['foto_perfil']; ?>" alt="Foto de Perfil"
                        style="width: 90px; height: 90px; border-radius: 50%; border: 5px solid #53bdc1;">
                <?php endif; ?>
                <br>
                <hr>
                <div class="d-flex flex-column align-items-start">
                <p>Alterar Foto do Perfil</p>
                    <!-- Formulário de upload de imagem -->
                    <form id="uploadForm" enctype="multipart/form-data" class="d-flex align-items-center mb-3">
                        <input class="form-control mr-2" type="file" id="formFile" name="foto_perfil" accept=".jpg, .jpeg, .png" required>&nbsp;&nbsp;&nbsp;&nbsp;
                    </form>
                </div>
            </div>
            <!-- Modal de Edição -->
            <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="cropModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cropModalLabel">Editar Foto de Perfil</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <hr class="modal-line"> <!-- Linha abaixo do título -->
                        <div class="modal-body">
                            <img id="image" src="#" alt="Imagem para edição">
                        </div>
                        <hr class="modal-line"> <!-- Linha acima dos botões -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="saveChanges">Salvar Alterações</button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <!-- JS do Cropper.js -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
            <!-- Script personalizado -->
            <script>
const formFile = document.getElementById('formFile');
const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
const imageElement = document.getElementById('image');
let cropper;

// Quando um arquivo é selecionado no input
formFile.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            imageElement.src = reader.result; // Define a imagem no modal
            cropModal.show(); // Mostra o modal de edição

            // Destroi um cropper anterior, se houver
            if (cropper) cropper.destroy();

            // Inicia o cropper
            cropper = new Cropper(imageElement, {
                aspectRatio: 1, // Mantém proporção 1:1
                viewMode: 1, // Garante que a imagem se ajuste ao container inicial
                dragMode: 'move', // Permite mover a imagem
                autoCropArea: 1, // Aumenta a área inicial de corte para 100%
                responsive: true, // Adapta-se ao redimensionar
                background: false, // Remove fundo cinza
                zoomOnWheel: true, // Permite zoom no scroll
                minContainerWidth: 800, // Largura mínima para o container
                minContainerHeight: 400, // Altura mínima para o container
            });
        };
        reader.readAsDataURL(file); // Lê o arquivo como uma URL
    }
});

// Quando o botão "Salvar Alterações" é clicado
document.getElementById('saveChanges').addEventListener('click', () => {
    if (!cropper) {
        alert('Nenhuma imagem para salvar.');
        return;
    }

    // Obtém a imagem recortada como blob
    const canvas = cropper.getCroppedCanvas();
    canvas.toBlob((blob) => {
        if (!blob) {
            alert('Erro ao processar a imagem.');
            return;
        }

        // Cria um FormData para enviar o arquivo
        const formData = new FormData();
        formData.append('foto_perfil', blob);

        // Exibe o blob no console para depuração
        console.log('Blob gerado:', blob);

        // Envia os dados para o servidor
        fetch('https://viggonexus.online/pgi/alterar_foto_perfil', {
            method: 'POST',
            body: formData,
        })
            .then(response => {
                console.log('Resposta HTTP recebida:', response);
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Resposta do servidor:', result);
                if (result.status === 'success') {
                    alert('Foto atualizada com sucesso!');
                    cropModal.hide(); // Fecha o modal
                    window.location.reload(); // Recarrega a página para refletir mudanças
                } else {
                    alert('Erro ao salvar a foto: ' + (result.message || 'Erro desconhecido.'));
                    console.error('Erro na resposta do servidor:', result);
                }
            })
            .catch(error => {
                console.error('Erro no envio:', error);
                alert('Erro ao comunicar com o servidor. Verifique os logs.');
            });
    }, 'image/jpeg'); // Define o formato de saída como JPEG
});

</script>


            <script>
                $(document).ready(function () {
                    <?php if (isset($foto_atualizada) && $foto_atualizada): ?>
                        $('#modalSucesso').modal('show');
                    <?php endif; ?>
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

    // Função para aplicar o tema
    function applyTheme(theme) {
        if (theme === 'dark') {
            themeStylesheet.setAttribute('href', '../css/principal_escuro.css');
            toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo claro';
        } else {
            themeStylesheet.setAttribute('href', '../css/principal.css');
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