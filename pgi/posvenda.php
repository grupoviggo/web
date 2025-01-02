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

// Nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Botão de sair
if (isset($_POST['sairnexus'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Conexão com o banco de dados
$conn = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$conn) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Definir a variável de busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Obter os parâmetros de ordenação
$order_column = isset($_GET['order_column']) ? $_GET['order_column'] : 'horario_venda';
$order_direction = isset($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC';

// Validação de colunas e direções
$valid_columns = ['horario_venda', 'valor_venda']; // Colunas permitidas
$valid_directions = ['ASC', 'DESC']; // Direções permitidas

if (!in_array($order_column, $valid_columns)) {
    $order_column = 'horario_venda';
}
if (!in_array($order_direction, $valid_directions)) {
    $order_direction = 'DESC';
}

// Construção da consulta SQL com filtro inicial
$query = "SELECT v.codigo_venda, v.tipo_cliente, v.cpf_vendedor, v.valor_venda, v.data_input, v.horario_venda, 
                 v.status_venda, v.backoffice, c.CONSULTOR_CPF, c.CONSULTOR_BASE_NOME, c.CONSULTOR_NOME, 
                 u.nome AS backoffice_nome, dp.BACKOFFICE, dp.NOME_CLIENTE, dp.DOC_CLIENTE
          FROM vendas v
          INNER JOIN Colaborador_hierarquia c ON v.cpf_vendedor = c.CONSULTOR_CPF
          LEFT JOIN usuarios_pgi u ON v.backoffice = u.cpf
          LEFT JOIN dados_provisorios dp ON v.codigo_venda = dp.CODIGO_VENDA
          WHERE v.status_venda IN ('ATIVO', 'PENDENTE', 'CANCELADO')"; // Filtra os status

// Se houver busca, adicionar ao SQL
if (!empty($busca)) {
    $busca = mysqli_real_escape_string($conn, $busca);
    $query .= " AND v.codigo_venda LIKE '%$busca%'";
}

// Adicionar ordenação
$query .= " ORDER BY " . mysqli_real_escape_string($conn, $order_column) . " " . mysqli_real_escape_string($conn, $order_direction);

// Executar a consulta
$result = mysqli_query($conn, $query);

// Verificar se a consulta foi bem-sucedida
if (!$result) {
    die("Erro na consulta SQL: " . mysqli_error($conn));
}

// Verificar se há resultados
$temResultados = mysqli_num_rows($result) > 0;

// Inclui as funções auxiliares necessárias
require 'valida_entrada.php';
require 'consulta_status_pos.php';
require 'carregar_foto_perfil.php';

$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$backoffice = $_SESSION['nome'] ?? null;
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pós Venda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Publica+Sans:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <style>
/* Estilo geral do contêiner do submenu */
.sub_pos-container {
    background: #e9f5ff; /* Fundo com tom claro */
    border: 1px solid #b3d7ff; /* Borda com azul suave */
    border-radius: 8px; /* Cantos arredondados */
    padding: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave */
    display: inline-block; /* Mantém o submenu alinhado ao botão pai */
    position: absolute; /* Para posicionamento correto */
    z-index: 1000;
}

/* Estilo dos itens do submenu */
.sub_pos {
    list-style: none; /* Remove as bolinhas da lista */
    padding: 0;
    margin: 0;
}

.sub_pos li {
    margin-bottom: 8px; /* Espaçamento entre os botões */
}

.sub_pos li:last-child {
    margin-bottom: 0; /* Remove o espaçamento do último item */
}

/* Botões dentro do submenu */
.sub_pos button {
    display: flex;
    justify-content: space-between; /* Espaça o texto e a seta */
    align-items: center;
    width: 100%; /* Botões ocupam toda a largura */
    background: #ffffff; /* Fundo branco */
    color: #003366; /* Texto azul escuro */
    font-weight: bold;
    border: 1px solid #b3d7ff;
    border-radius: 5px;
    padding: 10px 15px;
    cursor: pointer;
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

.sub_pos button:hover {
    background: #f0f8ff; /* Fundo mais claro ao passar o mouse */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

/* Adiciona uma seta estilizada apontando para a direita */
.sub_pos button::after {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-top: 2px solid #003366; /* Linha de cima da seta */
    border-right: 2px solid #003366; /* Linha da direita da seta */
    transform: rotate(45deg); /* Gira para criar seta à direita */
    margin-left: 10px; /* Espaçamento entre o texto e a seta */
}


/* Caixa de observação */
.observation-box {
    position: absolute; /* Para posicionamento relativo ao botão */
    top: 0;
    left: 150%; /* Ajusta para ir mais à direita */
    background: #ffffff;
    border: 1px solid #b3d7ff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 350px; /* Largura ajustada */
    z-index: 1500;
}

/* Campo de texto dentro da caixa de observação */
.observation-box textarea {
    width: 100%; /* Ocupa toda a largura */
    height: 70px; /* Altura suficiente */
    border: 1px solid #b3d7ff;
    border-radius: 5px;
    padding: 8px;
    font-size: 14px;
}
.observation-box textarea:focus {
    border-color: #b3d7ff !important;
    outline: none !important;
}

/* Botão "OK" na caixa de observação */
.observation-box button {
    margin-top: 10px;
    width: 100%;
    background: #28a745;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    padding: 10px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease;
}

.observation-box button:hover {
    background: #218838;
}
.modal{
    z-index: 999999 !important;
}
.modal-backdrop {
    z-index: 99999 !important;
}

.modal-dialog {
    z-index: 99998 !important;
}

</style>
    <script>
        // Verifica o tema salvo e aplica no carregamento da página
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            }
        })();
    </script>

    <link id="theme-stylesheet" rel="stylesheet" href="../css/principal.css">
    <link id="table-stylesheet" rel="stylesheet" href="../css/tabelas.css">
    <link id="tab-stylesheet" rel="stylesheet" href="../css/tab_fila.css">
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
                        <a class="page-link">Fila de vendas</>
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
                <a href="painelrh.php" class="page-link">Cadastro de colaboradores</a>
                <a href="colaboradores_rh.php" class="page-link">Gestão de usuários</a>
                <a href="carga-lotecolaboradores.php" class="page-link">Opção de Cadastro</a>
                <a href="Painel_hierarquia.php" class="page-link">Hierarquia</a>
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
        <div class="content">
            <div class="panel titulo">
                <div class="header-container_2">
                    <h2 class="subpanel_h2"><i class="fa-solid fa-pen-to-square fa-xl"></i> Pós-Venda</h2>
                    <!-- Status auditoria -->
                    <?php foreach ($data as $status): ?>
                        <div class="status-item" data-status="<?php echo $status['status']; ?>">
                            <div class="circle-progress"
                                style="--percentage: <?php echo $status['percentual']; ?>; --circle-border-color: <?php echo $status['cor']; ?>;">
                                <span><?php echo $status['total']; ?></span>
                            </div>
                            <span class="status-label"><?php echo $status['status']; ?></span>
                        </div>
                    <?php endforeach; ?>
                    <!-- Adicionando o botão de recarregar -->
                    <div class="vertical-separator_2"></div>
                    <button id="reload-button" class="reload-button" aria-label="Atualizar">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button><span style="width: 402px;"></span>

                    <div class="button-container">
                        <form method="GET" class="d-flex align-items-center gap-6 custom-form" id="filter-form">
                            <!-- Botão de filtro -->
                            <div style="position: relative;"> <!-- Define o contêiner como referência -->
                                <button type="button" id="filter-menu-button" class="btn btn-success"
                                    style="width: 80px;">
                                    Filtrar <i class="fa-solid fa-filter"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="filter-dropdown" class="filter-dropdown">
                                    <h4 class="dropdown-title">Aplicar Filtros</h4>
                                    <hr style="border: none; border-top: 1px solid #79888c; margin: 10px 0;">
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle"
                                            data-target="operation-filters">OPERAÇÃO</button>
                                        <div id="operation-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="operation[]" value="VIGGO VILA MATILDE">
                                                VIGGO VILA MATILDE</label>
                                            <label><input type="checkbox" name="operation[]" value="VIGGO LAPA"> VIGGO
                                                LAPA</label>
                                            <label><input type="checkbox" name="operation[]" value="VIGGO TUCURUVI">
                                                VIGGO TUCURUVI</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle"
                                            data-target="backoffice-filters">BACKOFFICE</button>
                                        <div id="backoffice-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="backoffice[]"
                                                    value="BACKOFFICE VILA MATILDE">
                                                BACKOFFICE VILA MATILDE</label>
                                            <label><input type="checkbox" name="backoffice[]"
                                                    value="BACKOFFICE LITORAL">
                                                BACKOFFICE LITORAL</label>
                                            <label><input type="checkbox" name="backoffice[]" value="BACKOFFICE CARRÃO">
                                                BACKOFFICE CARRÃO</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle"
                                            data-target="diretoria-filters">DIRETORIA</button>
                                        <div id="diretoria-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="diretoria[]" value="REGIONAL I">
                                                REGIONAL I</label>
                                            <label><input type="checkbox" name="diretoria[]" value="REGIONAL II">
                                                REGIONAL II</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle"
                                            data-target="stvendas-filters">STATUS VENDA</button>
                                        <div id="stvendas-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="diretoria[]" value="EM FILA">
                                                EM FILA</label>
                                            <label><input type="checkbox" name="diretoria[]" value="CANCELADO">
                                                CANCELADO</label>
                                            <label><input type="checkbox" name="diretoria[]" value="AG. RETORNO">
                                                AG. RETORNO</label>
                                            <label><input type="checkbox" name="diretoria[]" value="REENTRADA">
                                                REENTRADA</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle"
                                            data-target="segmento-filters">SEGMENTO</button>
                                        <div id="segmento-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="diretoria[]" value="DIRETORIA 1">
                                                DIRETORIA 1</label>
                                            <label><input type="checkbox" name="diretoria[]" value="DIRETORIA 2">
                                                DIRETORIA 2</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle" data-target="dtenvio-filters">DATA
                                            ENVIO</button>
                                        <div id="dtenvio-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="diretoria[]" value="DIRETORIA 1">
                                                DIRETORIA 1</label>
                                            <label><input type="checkbox" name="diretoria[]" value="DIRETORIA 2">
                                                DIRETORIA 2</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <button type="button" class="dropdown-toggle" data-target="dtfinal-filters">DATA
                                            FINALIZAÇÃO</button>
                                        <div id="dtfinal-filters" class="checkbox-list" style="display: none;">
                                            <!-- Mantém inicialmente fechado -->
                                            <label><input type="checkbox" name="diretoria[]" value="DIRETORIA 1">
                                                DIRETORIA 1</label>
                                            <label><input type="checkbox" name="diretoria[]" value="DIRETORIA 2">
                                                DIRETORIA 2</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Busca -->
                            <input type="text" id="searchInput" name="busca" class="form-control search-input"
                                placeholder=" código da venda" value="<?php echo htmlspecialchars($busca); ?>">
                            <button type="submit" class="btn btn-primary search-btn" style="width: 60px;"><i
                                    class="fa-solid fa-magnifying-glass"></i></button>
                            <button type="button" id="clear-search" class="btn btn-secondary eraser-btn"
                                style="width: 60px;"><i class="fa-solid fa-delete-left"></i></button>
                        </form>
                    </div>

                </div>
            </div>
            <div class="panel2" style="z-index: 0 !important; position: relative;">
                <table id="tabela-mista" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                <a
                                    href="?order_column=horario_venda&order_direction=<?php echo ($order_column === 'horario_venda' && $order_direction === 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    DATA INPUT
                                    <?php if ($order_column === 'horario_venda') { ?>
                                        <i
                                            class="fi fi-br-angle-<?php echo $order_direction === 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php } ?>
                                </a>
                            </th>
                            <th style="text-align: center;">TABULAÇÃO</th>
                            <th>DATA STATUS</th>
                            <th>O.S</th>
                            <th>DOCUMENTO</th>
                            <th>CLIENTE</th>
                            <th>OPERAÇÃO</th>
                            <th>OFERTA</th>
                            <th>
                                <a
                                    href="?order_column=valor_venda&order_direction=<?php echo ($order_column === 'valor_venda' && $order_direction === 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    VALOR
                                    <?php if ($order_column === 'valor_venda') { ?>
                                        <i
                                            class="fi fi-br-angle-<?php echo $order_direction === 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php } ?>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                        if ($temResultados) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $codigo_venda = htmlspecialchars($row['codigo_venda'], ENT_QUOTES, 'UTF-8');
                                $status_venda = htmlspecialchars($row['status_venda']); // Status de vendas
                                $status_dados_provisorios = htmlspecialchars($row['STATUS_VENDA']); // Status de dados_provisorios
                                $base_nome = htmlspecialchars($row['CONSULTOR_BASE_NOME']);
                                $base_nome_resumido = str_replace("VIGGO", "", $base_nome); // Remove a palavra "VIGGO"
                                $base_nome_resumido = trim($base_nome_resumido); // Remove espaços em branco extras no início ou no fim
                                $backoffice_nome = htmlspecialchars($row['backoffice_nome']);
                                $nome_com_quebra = nl2br(wordwrap(htmlspecialchars($row['backoffice']), 13, "\n", true));  // Quebra a string após 13 caracteres e converte novas linhas para <br>
                                // Processa o campo NOME_CLIENTE e quebra a linha após 13 caracteres
                                $nome_cliente = htmlspecialchars($row['NOME_CLIENTE']);
                                $nome_cliente_com_quebra = nl2br(wordwrap($nome_cliente, 13, "\n", true)); // Quebra a linha após 13 caracteres e converte novas linhas para <br>
                        
                                // Verifica se o status da tabela dados_provisorios é "FINALIZADA"
                                if ($status_dados_provisorios === "FINALIZADA") {
                                    // Se for FINALIZADA, usa o status de dados_provisorios
                                    $status_venda_final = $status_dados_provisorios;
                                } else {
                                    // Caso contrário, usa o status da tabela vendas
                                    $status_venda_final = $status_venda;
                                }

                                

                                // Define a classe do botão com base no status
                                $btn_class = '';

                                switch ($status_venda_final) {
                                    case 'ATIVO':
                                        $btn_class = 'btn btn-success btn-sm'; // Verde
                                        break;
                                    case 'CANCELADO':
                                        $btn_class = 'btn btn-cancel btn-sm'; // vermelho
                                        break;
                                    case 'PENDENTE':
                                        $btn_class = 'btn btn-purple btn-sm'; // Roxo
                                        break;
                                    default:
                                        $btn_class = 'btn btn-secondary btn-sm'; // Cor padrão
                                        break;
                                }

                                echo "<tr class='custom'>";
                                echo "<td class='hidden-cell'>" . htmlspecialchars($row['tipo_cliente']) . "</td>";
                                echo "<td class='hidden-cell'>" . htmlspecialchars($row['codigo_venda']) . "</td>";
                                echo "<td class='no-left-border-left'>" . htmlspecialchars($row['data_input']) . "<br>";
                                echo "<td id='no-right-border'>
                                <div style='align-items: right;'>
                                    <a  onclick='event.preventDefault(); loadSubMenu(this, \"$codigo_venda\", \"$status_venda_final\")' class='same-size-btn $btn_class'>$status_venda_final</a>
                                    <div class='sub_pos-container-$codigo_venda'></div>
                                </div>
                                      </td>";                           
                                echo "<td id='no-right-border'>" . htmlspecialchars($row['data_input']) . "<br>";
                                echo "<td id='no-right-border'>" . htmlspecialchars($row['PERFIL']) . "<br>";
                                echo "<td id='no-right-border' style='font-size: 10px !important;'>" . htmlspecialchars($row['DOC_CLIENTE']) . "</td>";
                                echo "<td id='no-right-border' style='font-size: 10px !important; text-align: center; vertical-align: middle;'>" . $nome_cliente_com_quebra . "</td>";
                                echo "<td style='font-size: 11px !important; position: relative;' id='no-right-border'>" . $base_nome_resumido . "</td>";
                                echo "<td id='no-right-border' style='font-size: 10px !important; text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['OFERTA']) . "</td>";
                                echo "<td class='no-left-border-right'>R$ " . htmlspecialchars($row['valor_venda']) . "<br>";
                                echo "</tr>";
                            }
                        }
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
            </form>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../js/tabulacoes.js"></script>
        <script src="../js/submenu_pos.js" defer></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var myModal = new bootstrap.Modal(document.getElementById('tabularModal'));

                // Evento ao exibir o modal
                document.getElementById('tabularModal').addEventListener('show.bs.modal', function (event) {
                    const codigoVenda = event.relatedTarget.getAttribute('data-codigo-venda');

                    if (codigoVenda) {
                        // Atualiza o conteúdo do modal com o código da venda no span com id "codigo_venda_display"
                        const codigoVendaElement = document.getElementById('codigo_venda_display');
                        codigoVendaElement.textContent = codigoVenda;

                        // Também atualiza o campo oculto no formulário com o código da venda
                        const codigoVendaInput = document.getElementById('codigo_venda');
                        codigoVendaInput.value = codigoVenda;
                    }
                });

                // Manipulador para envio do formulário
                document.querySelector('#formTabulacao').addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch('tabular_venda', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showModal('Sucesso', data.message, 'success');
                            } else {
                                showModal('Erro', data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            showModal('Erro', 'Ocorreu um erro inesperado ao tentar atualizar.', 'danger');
                        });
                });

                // Exibe uma mensagem no modal
                function showModal(title, message, type) {
                    const modalHeader = document.querySelector('#tabularModal .modal-header');
                    const modalBody = document.querySelector('#tabularModal .modal-body');

                    // Atualiza o título do modal
                    const codigoVenda = document.getElementById('codigo_venda_display').textContent;

                    modalHeader.innerHTML = `
            <h5 class="modal-title">Pendenciar Venda: <span id="codigo_venda_display">${codigoVenda}</span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        `;
                    modalBody.innerHTML = `<p>${message}</p>`;

                    // Adiciona classes para destacar o status
                    if (type === 'success') {
                        modalHeader.classList.add('bg-success', 'text-white');
                        modalHeader.classList.remove('bg-danger', 'text-white');
                    } else {
                        modalHeader.classList.add('bg-danger', 'text-white');
                        modalHeader.classList.remove('bg-success', 'text-white');
                    }

                    // Exibe o modal
                    myModal.show();

                    // Fecha o modal automaticamente após 2 segundos
                    setTimeout(function () {
                        myModal.hide();
                    }, 1000);
                }

                // Corrige o fechamento do modal
                document.getElementById('tabularModal').addEventListener('hidden.bs.modal', function () {
                    // Remove classes adicionais do cabeçalho
                    const modalHeader = document.querySelector('#tabularModal .modal-header');
                    modalHeader.classList.remove('bg-success', 'bg-danger', 'text-white');

                    // Limpa o conteúdo do corpo do modal
                    const modalBody = document.querySelector('#tabularModal .modal-body');
                    modalBody.innerHTML = '';

                    // Recarrega a página para atualizar os dados
                    location.reload();
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterMenuButton = document.getElementById('filter-menu-button');
                const filterDropdown = document.getElementById('filter-dropdown');
                const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
                const checkboxLists = document.querySelectorAll('.checkbox-list');

                // Inicialmente, todas as seções de checkbox devem estar ocultas
                checkboxLists.forEach((list) => {
                    list.style.display = 'none';  // Garante que as seções estão fechadas ao carregar a página
                });

                // Exibe ou oculta o menu principal ao clicar no botão
                filterMenuButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isVisible = filterDropdown.style.display === 'block';
                    filterDropdown.style.display = isVisible ? 'none' : 'block';
                });

                // Fecha o menu ao clicar fora
                document.addEventListener('click', () => {
                    filterDropdown.style.display = 'none';
                });

                // Impede que cliques no menu fechem ele
                filterDropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                // Alterna exibição das listas de checkboxes
                dropdownToggles.forEach((toggle) => {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault(); // Impede a submissão do formulário
                        e.stopPropagation();
                        const targetId = toggle.getAttribute('data-target');
                        const targetList = document.getElementById(targetId);

                        if (targetList) {
                            const isVisible = targetList.style.display === 'block';
                            checkboxLists.forEach((list) => (list.style.display = 'none')); // Fecha outras listas
                            targetList.style.display = isVisible ? 'none' : 'block'; // Alterna visibilidade
                        }
                    });
                });

                // Impede que cliques nos checkboxes fechem o menu
                checkboxLists.forEach((checkboxList) => {
                    checkboxList.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                });
            });

        </script>
        <script>
            document.getElementById('searchInput').addEventListener('focus', async function () {
                try {
                    // Acessa o conteúdo da área de transferência
                    const text = await navigator.clipboard.readText();
                    // Define o valor do input como o texto copiado
                    this.value = text;
                } catch (err) {
                    console.error('Erro ao acessar a área de transferência: ', err);
                }
            });
        </script>
        <script>
            document.getElementById('clear-search').addEventListener('click', function () {
                // Redireciona para a página 'fila.php' sem parâmetros de busca
                window.location.href = 'fila';
            });
        </script>
        <script>
            document.getElementById('reload-button').addEventListener('click', function () {
                const panels = document.querySelectorAll('.panel');
                const targetPanel = panels[0]; // Seleciona a segunda "panel" (índice 1)

                // Adiciona a classe de carregamento à segunda "panel"
                targetPanel.classList.add('loading');

                // Adiciona a classe 'clicked' para esconder o tooltip imediatamente ao clicar
                document.getElementById('reload-button').classList.add('clicked');

                // Faz um delay para a animação acontecer
                setTimeout(() => {
                    // Recarrega a página após a animação
                    location.reload();
                }, 2000); // Espera 2 segundos (ajustável) para mostrar o efeito de carregamento
            });
        </script>
        <script>
            function showMenu(element) {
                const parent = element.closest('.dots-menu');
                const submenu = parent.querySelector('.submenu_fila');

                // Fecha todos os outros submenus
                document.querySelectorAll('.submenu_fila').forEach(menu => {
                    menu.style.display = 'none';
                });

                // Extrai o ID completo, incluindo o que está após o hífen
                const rowId = parent.querySelector('.submenu_fila').id.replace('submenu-container-', '');

                // Valida se o ID é válido
                if (!rowId) {
                    console.error('Código de venda inválido.');
                    return;
                }

                // Carrega o submenu dinamicamente usando POST
                fetch('submenu_fila', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded', // Define que o conteúdo será enviado via URL-encoded
                    },
                    body: 'codigo_venda=' + encodeURIComponent(rowId) // Passa o parâmetro via POST
                })
                    .then(response => response.text())
                    .then(data => {
                        submenu.innerHTML = data;
                        submenu.style.display = 'block';

                        // Adiciona evento de clique fora do submenu para fechá-lo
                        document.addEventListener('click', closeSubmenuOnClickOutside, { once: true });
                    })
                    .catch(err => console.error('Erro ao carregar o submenu:', err));
            }

            function closeSubmenuOnClickOutside(event) {
                // Fecha todos os submenus se clicar fora
                if (!event.target.closest('.dots-menu')) {
                    document.querySelectorAll('.submenu_fila').forEach(menu => {
                        menu.style.display = 'none';
                    });
                }
            }
        </script>
        <script>
            window.onload = function () {
                var elemento = document.getElementById('tabela-mista');
                if (elemento) {
                    elemento.innerHTML = elemento.innerHTML
                        .replace(/MIGRA\?\?O/g, 'MIGRAÇÃO')
                        .replace(/N\?O/g, 'NÃO')
                        .replace(/PR\?/g, 'PRÉ')
                        .replace(/M\?VEL/g, 'MÓVEL')
                        .replace(/AVAN\?ADO/g, 'AVANÇADO')
                        .replace(/N\?MERO/g, 'NÚMERO')
                        .replace(/J\?/g, 'JÁ')
                        .replace(/OBSERVA\?\?O/g, 'OBSERVAÇÃO')
                        .replace(/ENDERE\?O/g, 'ÇO')
                        .replace(/CORRE\?\?O/g, 'CORREÇÃO');
                }
            };
        </script>
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
    const tabStylesheet = document.getElementById('tab-stylesheet');
    const tabelaMista = document.getElementById('tabela-mista');

    // Função para aplicar o tema
    function applyTheme(theme) {
        const root = document.documentElement; // Elemento <html>

        if (theme === 'dark') {
            root.classList.add('dark-theme');
            themeStylesheet.setAttribute('href', '../css/principal_escuro.css');
            tableStylesheet.setAttribute('href', '../css/tabelas_escuro.css');
            tabStylesheet.setAttribute('href', '../css/tab_fila_escuro.css');
            toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo claro';

            // Troca a classe da tabela para 'table-dark'
            if (tabelaMista) {
                tabelaMista.classList.add('table-dark');
                tabelaMista.classList.remove('table-light');
            }
        } else {
            root.classList.remove('dark-theme');
            themeStylesheet.setAttribute('href', '../css/principal.css');
            tableStylesheet.setAttribute('href', '../css/tabelas.css');
            tabStylesheet.setAttribute('href', '../css/tab_fila.css');
            toggleButton.innerHTML = '<i class="fa-solid fa-palette" style="margin-right: 10px;"></i> Modo escuro';

            // Remove a classe 'table-dark' quando o tema for claro
            if (tabelaMista) {
                tabelaMista.classList.remove('table-dark');
            }
        }
    }

    // Verificar o tema salvo no localStorage ao carregar a página
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        applyTheme(savedTheme);
    });

    // Alternar tema ao clicar no botão
    toggleButton.addEventListener('click', () => {
        const currentTheme = document.documentElement.classList.contains('dark-theme') ? 'dark' : 'light';
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