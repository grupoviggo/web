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


// Recupera os dados da sessão
$dadosEmissao = $_SESSION['dados_emissao'] ?? [];

// Recuperar valores específicos das etapas anteriores (exemplo)
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? '';
$documento = $_SESSION['dados_emissao']['documento'] ?? '';
$nomeCompleto = $_SESSION['dados_emissao']['nomeCompleto'] ?? '';
$dataNascimento = $_SESSION['dados_emissao']['dataNascimento'] ?? '';
$nomeMae = $_SESSION['dados_emissao']['nomeMae'] ?? '';
$email = $_SESSION['dados_emissao']['email'] ?? '';
$celular = $_SESSION['dados_emissao']['celular'] ?? '';
$telefone1 = $_SESSION['dados_emissao']['telefone1'] ?? '';
$telefone2 = $_SESSION['dados_emissao']['telefone2'] ?? '';
$cep = $_SESSION['dados_emissao']['cep'] ?? '';
$numero = $_SESSION['dados_emissao']['numero'] ?? '';
$logradouro = $_SESSION['dados_emissao']['logradouro'] ?? '';
$bairro = $_SESSION['dados_emissao']['bairro'] ?? '';
$cidade = $_SESSION['dados_emissao']['cidade'] ?? '';
$uf = $_SESSION['dados_emissao']['uf'] ?? '';
$complemento1 = $_SESSION['dados_emissao']['complemento1'] ?? '';
$complemento2 = $_SESSION['dados_emissao']['complemento2'] ?? '';
$complemento3 = $_SESSION['dados_emissao']['complemento3'] ?? '';
$observacao = $_SESSION['dados_emissao']['observacao'] ?? '';
$tipoEndereco = $dadosEmissao['ftta'] === 'sim' ? 'FTTA' : ($dadosEmissao['ftth'] === 'sim' ? 'FTTH' : '');
$selecao_painel1 = $_SESSION['selecoes_cards'][$codigo_venda]['painel1'] ?? '';
$selecao_painel2 = $_SESSION['selecoes_cards'][$codigo_venda]['painel2'] ?? '';

// Verifica se os campos devem ser desabilitados
$desabilitarFixa = ($selecao_painel1 === 'NOVA VENDA' || $selecao_painel1 === 'NÃO POSSUI');
$desabilitarMovel = ($selecao_painel2 === 'NOVA VENDA' || $selecao_painel2 === 'NÃO POSSUI');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os campos enviados
    $dadosPainelFixa = [
        'plano_origem_fixa' => $_POST['fixa'] ?? '',
        'data_ativacao_fixa' => $_POST['data_a_f'] ?? '',
        'permanencia_fixa' => $_POST['p_fixa'] ?? '',
    ];

    $dadosPainelMovel = [
        'plano_origem_movel' => $_POST['movel'] ?? '',
        'data_ativacao_movel' => $_POST['data_a_m'] ?? '',
        'permanencia_movel' => $_POST['p_movel'] ?? '',
    ];

    // Atualiza os dados na sessão
    $_SESSION['dados_painel_fixa'] = $dadosPainelFixa;
    $_SESSION['dados_painel_movel'] = $dadosPainelMovel;

    // Redireciona conforme o botão clicado
    if (isset($_POST['avancar'])) {
        header("Location: emissao_05.php");
        exit();
    } elseif (isset($_POST['voltar'])) {
        header("Location: emissao_03.php");
        exit();
    }
    if (isset($_POST['sair_venda'])) {
        header("Location: fila.php");
        exit();
    }
}

// Recupera os dados da sessão para pré-preenchimento
$dadosPainelFixa = $_SESSION['dados_painel_fixa'] ?? [
    'plano_origem_fixa' => '',
    'data_ativacao_fixa' => '',
    'permanencia_fixa' => '',
];

$dadosPainelMovel = $_SESSION['dados_painel_movel'] ?? [
    'plano_origem_movel' => '',
    'data_ativacao_movel' => '',
    'permanencia_movel' => '',
];


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
                <div class="header-title sub3">&nbsp; Dados upgrade</div>
                </div>
            </div>
<!-- Painel Fixo -->
<div class="panel2" id="painel1">
    <h5 class="form-header">FIXA</h5>
    <form method="POST">
        <div class="content-center">
            <div class="form-row">
                <div class="form-group mr-3">
                    <label for="plano_origem_fixa">Valor de Face Plano Origem</label>
                    <input type="text" class="form-control" id="plano_origem_fixa" name="fixa" 
                        value="<?php echo htmlspecialchars($dadosPainelFixa['plano_origem_fixa']); ?>" 
                        <?php echo $desabilitarFixa ? 'disabled' : ''; ?>>
                </div>
                <div class="form-group mr-3">
                    <label for="data_ativacao_fixa">Data de Ativação Plano Origem</label>
                    <input type="date" class="form-control" id="data_ativacao_fixa" name="data_a_f" 
                        value="<?php echo htmlspecialchars($dadosPainelFixa['data_ativacao_fixa']); ?>" 
                        <?php echo $desabilitarFixa ? 'disabled' : ''; ?>>
                </div>
                <div class="form-group">
                    <label for="permanencia_fixa">Tempo de Permanência</label>
                    <input type="text" class="form-control" id="permanencia_fixa" name="p_fixa" 
                        value="<?php echo htmlspecialchars($dadosPainelFixa['permanencia_fixa']); ?>" 
                        readonly <?php echo $desabilitarFixa ? 'disabled' : ''; ?>>
                </div>
            </div>
        </div>

</div>

<!-- Painel Móvel -->
<div class="panel2" id="painel2">
    <h5 class="form-header">MÓVEL</h5>
    <div class="content-center">
        <div class="form-row">
            <div class="form-group mr-3">
                <label for="plano_origem_movel">Valor de Face Plano Origem</label>
                <input type="text" class="form-control" id="plano_origem_movel" name="movel" 
                    value="<?php echo htmlspecialchars($dadosPainelMovel['plano_origem_movel']); ?>" 
                    <?php echo $desabilitarMovel ? 'disabled' : ''; ?>>
            </div>
            <div class="form-group mr-3">
                <label for="data_ativacao_movel">Data de Ativação Plano Origem</label>
                <input type="date" class="form-control" id="data_ativacao_movel" name="data_a_m" 
                    value="<?php echo htmlspecialchars($dadosPainelMovel['data_ativacao_movel']); ?>" 
                    <?php echo $desabilitarMovel ? 'disabled' : ''; ?>>
            </div>
            <div class="form-group">
                <label for="permanencia_movel">Tempo de Permanência</label>
                <input type="text" class="form-control" id="permanencia_movel" name="p_movel" 
                    value="<?php echo htmlspecialchars($dadosPainelMovel['permanencia_movel']); ?>" 
                    readonly <?php echo $desabilitarMovel ? 'disabled' : ''; ?>>
            </div>
        </div>
    </div>
</div>
        <!-- Botões abaixo do formulário -->
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-warning text-white fw-bold">TABULAR</button>
            <div>
                <button type="submit" name="sair_venda" class="btn btn-success btn_fila_sair text-white fw-bold me-2">SAIR SEM ALTERAR</button>
                <button type="submit" name="voltar"  class="btn btn-danger text-white fw-bold me-2">VOLTAR</button>
                <button type="submit" name="avancar"  class="btn btn-success text-white fw-bold">AVANÇAR</button>
            </div>            
        </form>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Função para calcular a diferença de meses e atualizar o tooltip para um campo específico
function calcularPermanencia(campoDataAtivacaoId, campoPermanenciaId, tooltipId) {
    const dataAtivacao = document.getElementById(campoDataAtivacaoId).value;
    const tooltip = document.getElementById(tooltipId);
    const campoPermanencia = document.getElementById(campoPermanenciaId);

    if (dataAtivacao) {
        const dataAtivacaoObj = new Date(dataAtivacao);
        const dataAtual = new Date();

        // Calcula diferença de anos e meses
        let anosDiferenca = dataAtual.getFullYear() - dataAtivacaoObj.getFullYear();
        let mesesDiferenca = dataAtual.getMonth() - dataAtivacaoObj.getMonth();

        if (mesesDiferenca < 0) {
            anosDiferenca--;
            mesesDiferenca += 12;
        }

        let meses = anosDiferenca * 12 + mesesDiferenca;

        // Ajusta o cálculo do mês se o dia não tiver passado
        if (dataAtual.getDate() < dataAtivacaoObj.getDate()) {
            meses--;
        }

        // Evita valores negativos
        if (meses < 0) meses = 0;

        // Atualiza o valor do campo de permanência
        campoPermanencia.value = `${meses} Meses`;

        // Atualiza o tooltip
        if (meses < 12) {
            tooltip.style.display = 'block';
            tooltip.textContent = 'Cliente dentro do prazo de fidelidade!';
            tooltip.className = 'tooltip tooltip-red';
            tooltip.style.visibility = 'visible';
            tooltip.style.opacity = '1';
        } else {
            tooltip.style.display = 'block';
            tooltip.textContent = 'Cliente fora do prazo de fidelidade!';
            tooltip.className = 'tooltip tooltip-green';
            tooltip.style.visibility = 'visible';
            tooltip.style.opacity = '1';
        }
    } else {
        // Esconde o tooltip caso não haja data
        tooltip.style.visibility = 'hidden';
        tooltip.style.opacity = '0';
    }
}

// Adiciona eventos aos campos de data
document.getElementById('data_ativacao_fixa').addEventListener('change', () => {
    calcularPermanencia('data_ativacao_fixa', 'permanencia_fixa', 'tooltip');
});

document.getElementById('data_ativacao_movel').addEventListener('change', () => {
    calcularPermanencia('data_ativacao_movel', 'permanencia_movel', 'tooltip_movel');
});

// Executa o cálculo ao carregar a página
window.onload = () => {
    calcularPermanencia('data_ativacao_fixa', 'permanencia_fixa', 'tooltip');
    calcularPermanencia('data_ativacao_movel', 'permanencia_movel', 'tooltip_movel');
};
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