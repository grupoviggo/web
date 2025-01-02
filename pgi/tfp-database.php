<?php
session_start();
if (!isset($_SESSION['cpf'])) {
    header("Location: login");
    exit();
}

if (!isset($_SESSION['tempo_login'])) {
    $_SESSION['tempo_login'] = time();
}

$tempo_online = time() - $_SESSION['tempo_login'];
$tempo_online_formatado = ($tempo_online < 3600)
    ? "Tempo Logado: " . floor($tempo_online / 60) . " minutos"
    : "Tempo Logado: " . sprintf("%02d:%02d horas", floor($tempo_online / 3600), floor(($tempo_online % 3600) / 60));

// Conexão com o banco de dados
require 'conexao_admpgi.php';

// Recupera o nível de acesso e nome de usuário para uso posterior
$nivel_acesso = $_SESSION['nivel'];
$username_acesso = $_SESSION['nome'];

// Verifica se há uma requisição POST para atualizar os dados dos clientes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar'])) {
    $id_cliente = intval($_POST['cliente_id']);  // Garante que o ID seja um número inteiro
    $isSelected = intval($_POST['is_selected']); // Captura o valor de is_selected (0 ou 1)
    $userName = $_POST['bko']; // Captura o nome do usuário que fez a alteração

    // Atualiza o campo is_selected e BKO no banco de dados
    $query = "UPDATE dados_tfp SET is_selected = ?, BKO = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Vincula os parâmetros para execução
    $stmt->bind_param("isi", $isSelected, $userName, $id_cliente);

    if (!$stmt->execute()) {
        echo "Erro na atualização: " . $stmt->error;
    }

    $stmt->close();
}

// Verifica se o filtro de "Disponível" foi ativado (sem passar parâmetro na URL)
$disponivel = isset($_GET['disponivel']) && $_GET['disponivel'] == '1';

// Cria a consulta SQL com base na disponibilidade
$query = "SELECT 
            ID, POSVENDA_DATA_INSTALACAO, CLIENTE_NOME_RAZAO_SOCIAL, 
            CLIENTE_DOCUMENTO, VENDA_ORDEM_DE_SERVICO, PRODUTO_VALOR_GERENCIAL, 
            VENDA_DATA_FATURA, `1_FATURA`, `2_FATURA`, `3_FATURA`, `4_FATURA`, 
            is_selected, BKO 
          FROM dados_tfp";

// Se o filtro "Disponível" estiver ativo, filtra clientes com is_selected = 0
if ($disponivel) {
    $query .= " WHERE is_selected = 0";
} else {
    // Se o filtro não estiver ativo, traz todos os clientes (com is_selected = 0 e 1)
    $query .= " WHERE is_selected IN (0, 1)";
}

$result = $conn->query($query);

if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
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
    <title>TFP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="table-stylesheet" rel="stylesheet" href="../css/tabelas.css">

    <style>
.header-container_3 {
    display: flex !important; /* Define o contêiner como flex */
    justify-content: space-between !important; /* Espaça o título e os botões */
    align-items: center !important; /* Centraliza verticalmente */
    width: 100% !important; /* Garante que ocupe toda a largura */
}

.button-container-custom {
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
    gap: 10px !important; /* Espaçamento entre os botões */
    padding-right: 20px;
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
                <a href="colaboradores_rh" class="page-link" onclick="window.location.href='colaboradores_rh'">Ver Colaboradores</a>
                <a href="carga-lotecolaboradores" class="page-link" onclick="window.location.href='carga-lotecolaboradores'">Opções de cadastro</a>
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
                <!-- Display time on the far left -->
                <div class="vertical-separator"></div> <!-- Vertical separator -->
                <span id="user-name"><?php echo $_SESSION['nome']; ?></span> <!-- User name in the middle -->
                <img src="<?php echo $foto_perfil; ?>" alt="" style="cursor: pointer; border: 3px solid #53bdc1;"> <!-- User image on the far right -->

                <!-- Submenu for Logout -->
                <?php include 'submenu.php'; ?>
            </div>
        </div>

        <!-- Conteúdo principal -->
        
        <div  class="content">
            <div class="panel" style="padding: 3px;">
            <div class="header-container_3">
    <h2>&nbsp;<i class="fa-solid fa-list-check"></i> Lista de Clientes</h2>
    <div class="button-container-custom">
        <form method="POST" class="d-flex align-items-center gap-6 custom-form">
            <button type="submit" name="atualizar" class="btn btn-primary btn-sm d-flex align-items-center gap-6" style="margin-right: 10px;" >
                <i class="fa-solid fa-arrows-rotate"></i> Atualizar
            </button>
            <div class="dropdown">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="#" onclick="toggleFiltro('todas')">Todas</a></li>
                    <li><a class="dropdown-item" href="#" onclick="toggleFiltro('disponivel')">Disponível</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterRows('1naoPaga')">Apenas 1 Não paga</a></li>
                </ul>
            </div>
        </form>
    </div>
</div>
</div>

            <div class="panel2" style="z-index: 0;">
            <table class="table table-bordered">
    <thead>
        <tr>
            <th class="col-vazio"></th>
            <th class="col-checkbox"></th>
            <th>DATA ALTA</th>
            <th class="col-client">CLIENTE</th>
            <th>CPF/CNPJ</th>
            <th>OS</th>
            <th>VALOR PLANO</th>
            <th>VENC.</th>
            <th>1ª FATURA</th>
            <th>2ª FATURA</th>
            <th>3ª FATURA</th>
            <th>4ª FATURA</th>
            <th class="col-vazio"></th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr id="cliente_<?php echo $row['ID']; ?>"> <!-- Adicionando ID exclusivo para cada linha -->
        <td class="no-left-border-left"></td>
        <td class="col-checkbox" id="no-right-border">
    <div class="form-check form-switch">
        <input class="form-check-input custom-switch" style="margin-top: 8px;" 
               type="checkbox" name="cliente_id" value="<?php echo $row['ID']; ?>"
               data-id="<?php echo $row['ID']; ?>" 
               onchange="updateSelectionStatus(this)" 
               onclick="highlightRow(this); applyFilter()">
        <span class="user-status" id="status-<?php echo $row['ID']; ?>"><?php echo $row['BKO']; ?></span> <!-- Exibe o usuário que alterou -->
    </div>
</td>


        <td class="col-center" id="no-left-border"><?php echo $row['POSVENDA_DATA_INSTALACAO']; ?></td>
        <td class="col-client" id="no-left-border-middle"><?php echo $row['CLIENTE_NOME_RAZAO_SOCIAL']; ?></td>
        <td class="col-center-doc" id="cliente_documento_<?php echo $row['ID']; ?>" onclick="copiarTexto(this)" tabindex="0" style="position: relative;">
            <?php echo $row['CLIENTE_DOCUMENTO']; ?>
            <span class="tooltip">Copiado!</span>
        </td>
        <td class="col-center" id="no-left-border-middle" onclick="copiarTexto(this)" tabindex="0" style="position: relative;">
            <?php echo $row['VENDA_ORDEM_DE_SERVICO']; ?>
            <span class="tooltip">Copiado!</span>
        </td>
        <td class="col-center" id="no-left-border-middle">R$ <?php echo $row['PRODUTO_VALOR_GERENCIAL']; ?></td>
        <td class="col-venc" id="no-left-border-middle"><?php echo $row['VENDA_DATA_FATURA']; ?></td>

        <?php for ($i = 1; $i <= 4; $i++): ?>
            <td class="col-radio-group" id="no-left-border">
    <div class="radio-group" style="display: flex; flex-direction: column; gap: 5px;">
        <div style="display: inline-flex; align-items: center;">
            <input class="form-check-input radio-update fatura-status custom-radio-pago" type="radio" 
                   name="<?php echo $i . '_FATURA_' . $row['ID']; ?>" 
                   value="Pago" 
                   data-fatura="<?php echo $i; ?>_FATURA" 
                   disabled <?php echo $row[$i . '_FATURA'] == 'Pago' ? 'checked' : ''; ?>
                   onclick="toggleSelect(this, '<?php echo $i; ?>', <?php echo $row['ID']; ?>)">
            <label class="form-check-label" style="margin-left: 5px; white-space: nowrap;">Pago</label>
        </div>
        <div style="display: inline-flex; align-items: center;">
            <input class="form-check-input radio-update fatura-status custom-radio-npago" type="radio" 
                   name="<?php echo $i . '_FATURA_' . $row['ID']; ?>" 
                   value="Nao pago" 
                   data-fatura="<?php echo $i; ?>_FATURA" 
                   disabled <?php echo $row[$i . '_FATURA'] == 'Nao pago' ? 'checked' : ''; ?>
                   onclick="toggleSelect(this, '<?php echo $i; ?>', <?php echo $row['ID']; ?>)">
            <label class="form-check-label" style="margin-left: 5px; white-space: nowrap;">Não pago</label>
        </div>
        <label class="small-date-label" style="font-size: 0.8em; color: #888; margin-top: 5px;">
            <?php echo $row['POSVENDA_DATA_INSTALACAO']; ?>
        </label>
        <select name="SUB_STATUS_F<?php echo $i; ?>_<?php echo $row['ID']; ?>" 
                id="subStatusSelect_<?php echo $i; ?>_<?php echo $row['ID']; ?>" 
                class="sub-status-select form-select rounded shadow-sm" 
                style="display: <?php echo $row[$i . '_FATURA'] == 'Nao pago' ? 'block' : 'none'; ?>; margin-top: 5px;">
            <option value="">Selecione</option>
            <option value="FATURA ENVIADA">Fatura enviada</option>
            <option value="DESCONECTADO">Desconectado</option>
            <option value="EM ACORDO">Em acordo</option>
        </select>
    </div>
</td>


        <?php endfor; ?>
        <td class="no-left-border-right"></td>
    </tr>
<?php endwhile; ?>

</tbody>
</table>
            </div>
            </form>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
            function copiarTexto(elemento) {
                // Obtém o texto da célula, excluindo o texto da tooltip
                const texto = Array.from(elemento.childNodes)
                    .filter(node => node.nodeType === Node.TEXT_NODE) // Filtra apenas os nós de texto
                    .map(node => node.nodeValue.trim()) // Remove espaços extras
                    .join(''); // Combina os textos, se houver mais de um nó de texto

                // Copia o texto para a área de transferência
                navigator.clipboard.writeText(texto).then(() => {
                    // Localiza o elemento com a classe tooltip dentro da célula
                    const tooltip = elemento.querySelector('.tooltip');
                    if (tooltip) {
                        // Adiciona a classe .show para exibir o tooltip
                        tooltip.classList.add('show');

                        // Remove a classe .show após 2 segundos
                        setTimeout(() => {
                            tooltip.classList.remove('show');
                        }, 2000);
                    }
                }).catch(err => {
                    console.error('Erro ao copiar texto: ', err);
                });
            }
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

<script>
// Função para permitir apenas um checkbox selecionado por vez
function highlightRow(checkbox) {
    console.log("Checkbox clicado:", checkbox);
    const userName = "<?php echo $_SESSION['nome']; ?>";

    // Desmarcar todos os checkboxes, ocultar os statusSpans e desabilitar os radio buttons
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="cliente_id"]');
    checkboxes.forEach(cb => {
        const statusSpan = cb.closest('.form-check').querySelector('.user-status');
        
        if (cb !== checkbox) {
            cb.checked = false;
            cb.closest('tr').classList.remove('highlighted');
            
            // Remover a classe 'highlighted' de cada td da linha desmarcada
            const tds = cb.closest('tr').querySelectorAll('td');
            tds.forEach(td => td.classList.remove('highlighted'));
            
            // Ocultar o statusSpan do checkbox desmarcado
            statusSpan.style.display = 'none';
            
            // Desabilitar os radio buttons
            const radioButtons = cb.closest('tr').querySelectorAll('input[type="radio"]');
            radioButtons.forEach(radio => {
                radio.disabled = true; // Desabilitar o rádio da linha
            });
        }
    });

    // Marcar a linha do checkbox atual, se estiver marcado
    const selectedRow = checkbox.closest('tr');
    const statusSpan = checkbox.closest('.form-check').querySelector('.user-status');

    if (checkbox.checked) {
        selectedRow.classList.add('highlighted');
        statusSpan.innerHTML = `<span class="icon">👤</span> Em uso por:<strong> ${userName}</strong>`;
        statusSpan.style.display = 'inline';

        // Adicionar a classe 'highlighted' em cada td da linha marcada
        const tds = selectedRow.querySelectorAll('td');
        tds.forEach(td => td.classList.add('highlighted'));
        console.log("Classe highlighted adicionada às células");

        // Habilitar os radio buttons apenas na linha do checkbox marcado
        const radioButtons = selectedRow.querySelectorAll('input[type="radio"]');
        radioButtons.forEach(radio => {
            radio.disabled = false; // Habilitar o rádio
        });
    } else {
        selectedRow.classList.remove('highlighted');
        statusSpan.style.display = 'none';

        // Remover a classe 'highlighted' de cada td da linha desmarcada
        const tds = selectedRow.querySelectorAll('td');
        tds.forEach(td => td.classList.remove('highlighted'));
        console.log("Classe highlighted removida das células");

        // Desabilitar os radio buttons
        const radioButtons = selectedRow.querySelectorAll('input[type="radio"]');
        radioButtons.forEach(radio => {
            radio.disabled = true; // Desabilitar o rádio
        });
    }
}

function toggleFiltro(opcao) {
    const urlParams = new URLSearchParams(window.location.search);

    if (opcao === 'disponivel') {
        // Define o filtro de 'Disponível' apenas para is_selected = 0
        urlParams.set('disponivel', '1');
    } else if (opcao === 'todas') {
        // Remove o filtro 'disponivel' para exibir todos os registros
        urlParams.delete('disponivel');
    }

    // Atualiza a URL sem recarregar a página
    window.history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
    
    // Atualiza a página para aplicar o novo filtro
    location.reload();
}


    // Função para alternar exibição do select de sub-status
    function toggleSelect(radio, faturaNum, clientId) {
        const selectId = 'subStatusSelect_' + faturaNum + '_' + clientId;
        const selectElement = document.getElementById(selectId);
        if (radio.value === "Nao pago") {
            selectElement.style.display = "block";
        } else {
            selectElement.style.display = "none";
            selectElement.value = "-";
        }
    }

    // Título fixo com mudança de cor ao rolar a página
    window.addEventListener("scroll", function() {
        const title = document.querySelector(".title");
        if (window.scrollY > 50) {
            title.classList.add("scrolled");
        } else {
            title.classList.remove("scrolled");
        }
    });

// Função para habilitar radios de cliente selecionado
document.addEventListener("DOMContentLoaded", function () {
    const radioUpdateElements = document.querySelectorAll(".radio-update");

    // Desabilitar todos os radio buttons inicialmente
    radioUpdateElements.forEach(radio => radio.disabled = true);

    // Função para habilitar os radios da linha selecionada
    function enableRadioOptions(clienteId) {
        radioUpdateElements.forEach(radio => radio.disabled = true);  // Desabilitar todos os radios
        const selectedRowRadios = document.querySelectorAll(`#cliente_${clienteId} .radio-update`);
        selectedRowRadios.forEach(radio => radio.disabled = false);   // Habilitar radios da linha selecionada
    }

    // Adicionar evento de clique nos checkboxes
    document.querySelectorAll("input[name='cliente_id']").forEach(checkbox => {
        checkbox.addEventListener("click", function () {
            const clienteId = this.value;
            
            if (this.checked) {
                // Se o checkbox for marcado, habilitar os radio buttons dessa linha
                enableRadioOptions(clienteId);
            } else {
                // Se o checkbox for desmarcado, desabilitar os radio buttons dessa linha
                const radiosInRow = document.querySelectorAll(`#cliente_${clienteId} .radio-update`);
                radiosInRow.forEach(radio => radio.disabled = true);
            }
        });
    });
});
</script>
<script>
   function filterRows(filterType) {
            // Seleciona todas as linhas de clientes
            const rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                // Seleciona todas as faturas na linha
                const faturas = Array.from(row.querySelectorAll(".fatura-status")).map(fatura => fatura.value);
                
                // Lógica de exibição
                let showRow = true;
                if (filterType === 'disponivel') {
                    // Exibir apenas se todas as faturas estão como "Pago"
                    showRow = faturas.every(f => f === 'Pago');
                } else if (filterType === '1naoPaga') {
                    // Exibir apenas se houver exatamente uma fatura "Nao pago"
                    showRow = faturas.filter(f => f === 'Nao pago').length === 1;
                }

                // Exibir ou ocultar a linha com base no filtro
                row.style.display = showRow ? '' : 'none';
            });
        }
</script>
<script>
function updateSelectionStatus(checkbox) {
    const clienteId = checkbox.getAttribute('data-id');
    const isSelected = checkbox.checked ? 1 : 0;
    const userName = isSelected ? "<?php echo $_SESSION['nome']; ?>" : "-";

    // Configuração da requisição AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_selection_status", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Função para tratar a resposta
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                console.log("Resposta do servidor:", xhr.responseText); // Verifica a resposta do PHP
            } else {
                console.error("Erro na requisição:", xhr.status, xhr.statusText);
            }
        }
    };

    // Envia os dados
    xhr.send("id=" + clienteId + "&is_selected=" + isSelected + "&bko=" + encodeURIComponent(userName));
}
</script>
<script>
// Função que verifica periodicamente o status dos checkboxes
function fetchCheckboxStatus() {
    const pollingInterval = 3000; // 3 segundos

    setInterval(function () {
        fetch('get_checkbox_status.php') // Chama o script PHP
            .then(response => response.json())
            .then(data => {
                // Atualiza os checkboxes e o nome do usuário com base nos dados retornados
                data.forEach(item => {
                    const checkbox = document.querySelector(`input[data-id="${item.ID}"]`);
                    const userStatusSpan = document.querySelector(`#status-${item.ID}`); // Referência ao elemento span

                    if (checkbox) {
                        // Marca/desmarca o checkbox
                        checkbox.checked = item.is_selected == 1;

                        // Se o checkbox estiver marcado, chama a função highlightRow para aplicar o estilo
                        highlightRow(checkbox);
                    }

                    if (userStatusSpan) {
                        userStatusSpan.textContent = item.BKO ? item.BKO : ''; // Atualiza o nome do usuário que alterou
                    }
                });
            })
            .catch(error => {
                console.error("Erro ao buscar status:", error);
            });
    }, pollingInterval);
}

// Inicia o polling quando a página carregar
document.addEventListener("DOMContentLoaded", function() {
    fetchCheckboxStatus();
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
</html>
