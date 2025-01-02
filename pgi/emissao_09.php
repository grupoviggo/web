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

// Recupera o código de venda da sessão
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? '';

// Se o código de venda mudar, limpa os dados da sessão para garantir consistência
if (isset($_POST['codigo_venda']) && $_POST['codigo_venda'] != $codigo_venda) {
    session_unset(); // Limpa a sessão quando mudar a venda
    $_SESSION['dados_emissao']['codigo_venda'] = $_POST['codigo_venda']; // Atualiza com o novo código de venda
}

// Recupera os dados da sessão salvos na página emissao_08
$dados_sessao = $_SESSION['dados_emissao'] ?? [];
$codigo_venda = $dados_sessao['codigo_venda'] ?? '';
$data_vencimento = $dados_sessao['data_vencimento'] ?? '';
$forma_pagamento = $dados_sessao['forma_pagamento'] ?? '';
$email_fatura = $dados_sessao['email_fatura'] ?? '';
$banco = $dados_sessao['banco'] ?? '';
$agencia = $dados_sessao['agencia'] ?? '';
$conta = $dados_sessao['conta'] ?? '';
$instancia = $dados_sessao['instancia'] ?? '';
$ordem_servico = $dados_sessao['ordem_servico'] ?? '';
$cnl = $dados_sessao['cnl'] ?? '';
$at = $dados_sessao['at'] ?? '';
$protocolo_smart_next = $dados_sessao['protocolo_smart_next'] ?? '';
$consultor = $dados_sessao['consultor'] ?? '';
$supervisor = $dados_sessao['supervisor'] ?? '';
$coordenador = $dados_sessao['coordenador'] ?? '';
$gerente_base = $dados_sessao['gerente_base'] ?? '';
$gerente_territorio = $dados_sessao['gerente_territorio'] ?? '';
$diretor = $dados_sessao['diretor'] ?? '';
$operacao = $dados_sessao['operacao'] ?? '';
$backoffice = $dados_sessao['backoffice'] ?? '';

// Dados da página emissao_01 até a 08
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

// Dados do painel Fixo e Móvel
$selecao_painel1 = $_SESSION['selecoes_cards'][$codigo_venda]['painel1'] ?? '';
$selecao_painel2 = $_SESSION['selecoes_cards'][$codigo_venda]['painel2'] ?? '';

$dadosPainelFixa = $_SESSION['dados_painel_fixa'] ?? [];
$dadosPainelMovel = $_SESSION['dados_painel_movel'] ?? [];

// Dados do painel Fixo
$planoOrigemFixa = $dadosPainelFixa['plano_origem_fixa'] ?? '';
$dataAtivacaoFixa = $dadosPainelFixa['data_ativacao_fixa'] ?? '';
$tempoPermanenciaFixa = $dadosPainelFixa['permanencia_fixa'] ?? '';

// Dados do painel Móvel
$planoOrigemMovel = $dadosPainelMovel['plano_origem_movel'] ?? '';
$dataAtivacaoMovel = $dadosPainelMovel['data_ativacao_movel'] ?? '';
$tempoPermanenciaMovel = $dadosPainelMovel['permanencia_movel'] ?? '';

// Dados adicionais
$planoBaseNome = $_SESSION['dados_emissao']['planoBaseNome'] ?? '';  // Nome do plano base
$linhaFixaNome = $_SESSION['dados_emissao']['linhaFixaNome'] ?? '';  // Nome da linha fixa
$tvIptvNome = $_SESSION['dados_emissao']['tvIptvNome'] ?? '';  // Nome da TV (IPTV)
$servicosAdicionais1Nome = $_SESSION['dados_emissao']['servicosAdicionais1Nome'] ?? '';  // Nome dos serviços adicionais 1
$servicosAdicionais2Nome = $_SESSION['dados_emissao']['servicosAdicionais2Nome'] ?? '';  // Nome dos serviços adicionais 2
$servicosAdicionais3Nome = $_SESSION['dados_emissao']['servicosAdicionais3Nome'] ?? '';  // Nome dos serviços adicionais 3
$planoMovelNome = $_SESSION['dados_emissao']['planoMovelNome'] ?? '';  // Nome do plano móvel
$dependentes = $_SESSION['dados_emissao']['dependentes'] ?? '';  // Nome dos dados móveis
$receitaTotalFixa = $_SESSION['dados_emissao']['receitaTotalFixa'] ?? '';
$receitaTotalMovel = $_SESSION['dados_emissao']['receitaTotalMovel'] ?? '';
$receitaTotalPedido = $_SESSION['dados_emissao']['receitaTotalPedido'] ?? '';

$numeroPortadoFixa = $_SESSION['dados_emissao']['numeroPortadoFixa'] ?? '';
$operadoraDoadoraFixa = $_SESSION['dados_emissao']['operadoraDoadoraFixa'] ?? '';
$quantidadeChips = $_SESSION['dados_emissao']['quantidadeChips'] ?? '';
$chips = $_SESSION['dados_emissao']['chips'] ?? [];

// Define valores padrão para os campos baseados nos dados da sessão
$codigo_venda = $dados_sessao['codigo_venda'] ?? '';


// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualiza a sessão com os dados enviados
        // Obtém os valores de $dadosPainelFixa e $dadosPainelMovel enviados pelo formulário

        $_SESSION['dados_emissao'] = array_merge($dados_sessao, [
        'codigo_venda' => $_POST['codigo_venda'] ?? $codigo_venda,
        'backoffice' => $_POST['backoffice'] ?? $backoffice,
        'nomeCompleto' => $_POST['nomeCompleto'] ?? $nomeCompleto,
        'doc_cliente' => $_POST['doc_cliente'] ?? $doc_cliente,
        'plano_origem_fixa' => $_POST['plano_origem_fixa'] ?? $planoOrigemFixa,
 

    ]);

    // Conexão com o banco de dados
    include 'conexao_admpgi.php';

    // Recupera os dados da sessão
    $codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? '';
    $backoffice = $_SESSION['dados_emissao']['backoffice'] ?? '';
    $nomeCompleto = $_SESSION['dados_emissao']['nomeCompleto'] ?? '';
    $documento = $_SESSION['dados_emissao']['documento'] ?? '';
    $planoBaseNome = $_SESSION['dados_emissao']['planoBaseNome'] ?? '';


    // Verifica se todos os dados necessários estão preenchidos
    if ($codigo_venda && $backoffice && $nomeCompleto && $documento && $planoBaseNome) {
        // Consulta SQL para inserir os dados
        $query = "INSERT INTO dados_provisorios (CODIGO_VENDA, BACKOFFICE, NOME_CLIENTE, DOC_CLIENTE, OFERTA)
                  VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sssss", $codigo_venda, $backoffice, $nomeCompleto, $documento, $planoBaseNome);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redireciona conforme a ação
    if (isset($_POST['avancar'])) {
        header("Location: emissao_10.php");
        exit();
    } elseif (isset($_POST['voltar'])) {
        header("Location: emissao_08.php");
        exit();
    } elseif (isset($_POST['sair_venda'])) {
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

        <i class="fi fi-sr-boss">
            <span class="menu-text">Comercial</span>
        </i>

        <!-- Item com dropdown -->
        <div class="menu-item dropdown-rh">
            <i class="fi fi-sr-users" onclick="toggleDropdownrh()">
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
                <div class="header-title sub3"><span class="tittle" onclick="window.location.href='emissao_04.php'">&nbsp; Dados upgrade</span></div>
                <div class="header-title sub03"><span class="tittle" onclick="window.location.href='emissao_05.php'">&nbsp; Produtos</span></div>
                <div class="header-title sub04"><span class="tittle" onclick="window.location.href='emissao_06.php'">&nbsp; Portabilidade</span></div>
                <div class="header-title sub_5"><span class="tittle" onclick="window.location.href='emissao_07.php'">&nbsp; Info. Input</span></div>
                <div class="header-title sub06"><span class="tittle" onclick="window.location.href='emissao_08.php'">&nbsp; Responsável Venda</span></div>
                <div class="header-title sub_7">&nbsp; Resumo</div>
                </div>
            </div>
<!-- Painel Fixo -->
<div class="panel2" id="painel1">
    <h5 class="form-header text-center" style="text-align: center;">RESUMO DA VENDA</h5>
    <form method="POST" action="sua_pagina">
        <div class="container mt-4">
    <div class="row">
        <!-- Coluna da Esquerda (Parte Superior) -->
        <div class="col-md-6 mb-3" style="border-right: 2px solid #ddd; padding-right: 15px;">
            <h5 class="text-primary">Dados Pessoais</h5>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Documento:</strong> <?= $documento ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Nome Completo:</strong> <?= $nomeCompleto ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Data de Nascimento:</strong> <?= $dataNascimento ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Nome da Mãe:</strong> <?= $nomeMae ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Email:</strong> <?= $email ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Celular:</strong> <?= $celular ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Telefone 1:</strong> <?= $telefone1 ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Telefone 2:</strong> <?= $telefone2 ?></p>
        </div>

        <!-- Coluna da Direita (Parte Superior) -->
        <div class="col-md-6 mb-3" style="padding-left: 15px;">
            <h5 class="text-success">Endereço</h5>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>CEP:</strong> <?= $cep ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Número:</strong> <?= $numero ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Logradouro:</strong> <?= $logradouro ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Bairro:</strong> <?= $bairro ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Cidade:</strong> <?= $cidade ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>UF:</strong> <?= $uf ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Complemento 1:</strong> <?= $complemento1 ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Complemento 2:</strong> <?= $complemento2 ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Complemento 3:</strong> <?= $complemento3 ?></p>
        </div>
    </div>

    <hr> <!-- Linha Horizontal para separar as seções -->

    <div class="row">
        <!-- Coluna da Esquerda (Parte Inferior) -->
        <div class="col-md-6 mb-3" style="border-right: 2px solid #ddd; padding-right: 15px;">
            <h5 class="text-info">Detalhes de Venda</h5>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Código da Venda:</strong> <?= $codigo_venda ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Data de Vencimento:</strong> <?= $data_vencimento ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Forma de Pagamento:</strong> <?= $forma_pagamento ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Email da Fatura:</strong> <?= $email_fatura ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Banco:</strong> <?= $banco ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Agência:</strong> <?= $agencia ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Conta:</strong> <?= $conta ?></p>
        </div>

        <!-- Coluna da Direita (Parte Inferior) -->
        <div class="col-md-6 mb-3" style="padding-left: 15px;">
            <h5 class="text-warning">Informações de Consultor</h5>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Consultor:</strong> <?= $consultor ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Supervisor:</strong> <?= $supervisor ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Coordenador:</strong> <?= $coordenador ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Gerente de Base:</strong> <?= $gerente_base ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Gerente de Território:</strong> <?= $gerente_territorio ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Diretor:</strong> <?= $diretor ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Operação:</strong> <?= $operacao ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Backoffice:</strong> <?= $backoffice ?></p>
        </div>
    </div>

    <hr> <!-- Linha Horizontal para separar as seções -->

    <div class="row">
        <div class="col-md-6 mb-3" style="border-right: 2px solid #ddd; padding-right: 15px;">
            <h5 class="text-primary">Informações de Venda</h5>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Plano Base:</strong> <?= $planoBaseNome ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Linha Fixa:</strong> <?= $linhaFixaNome ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>TV (IPTV):</strong> <?= $tvIptvNome ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Serviços Adicionais 1:</strong> <?= $servicosAdicionais1Nome ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Serviços Adicionais 2:</strong> <?= $servicosAdicionais2Nome ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Serviços Adicionais 3:</strong> <?= $servicosAdicionais3Nome ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Plano Móvel:</strong> <?= $planoMovelNome ?></p>
        </div>

        <div class="col-md-6 mb-3" style="padding-left: 15px;">
            <h5 class="text-success">Receitas e Detalhes Finais</h5>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Dependentes:</strong> <?= $dependentes ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Receita Total Fixa:</strong> <?= $receitaTotalFixa ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Receita Total Móvel:</strong> <?= $receitaTotalMovel ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Receita Total Pedido:</strong> <?= $receitaTotalPedido ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Número Portado Fixa:</strong> <?= $numeroPortadoFixa ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Operadora Doadora Fixa:</strong> <?= $operadoraDoadoraFixa ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Quantidade de Chips:</strong> <?= $quantidadeChips ?></p>
            <p style="line-height: 1.3; margin-bottom: 5px;"><strong>Chips:</strong> <?= implode(', ', $chips) ?></p>
        </div>
    </div>
</div>


</div>

    <input type="hidden" name="codigo_venda" value="<?= $codigo_venda ?>">
    <input type="hidden" name="backoffice" value="<?= $backoffice ?>">
    <input type="hidden" name="nomeCompleto" value="<?= $nomeCompleto ?>">
    <input type="hidden" name="documento" value="<?= $documento ?>">
    <input type="hidden" name="planoBaseNome" value="<?= $planoBaseNome ?>">

  




        <!-- Botões -->
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-warning text-white fw-bold">TABULAR</button>
            <div>
                <button type="submit" name="sair_venda" class="btn btn-success btn_fila_sair text-white fw-bold me-2">SAIR SEM ALTERAR</button>
                <button type="submit" name="voltar" class="btn btn-danger text-white fw-bold me-2">VOLTAR</button>
                <button type="submit" name="avancar" class="btn btn-success text-white fw-bold">AVANÇAR</button>
            </div>
        </div>
    </form>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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