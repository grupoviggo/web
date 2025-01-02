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

// inclui a consulta da tabela de produtos
include 'carregar_produtos.php';
$produtos = carregarProdutos();

// Inclui o arquivo que carrega foto de perfil
require 'carregar_foto_perfil.php';
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$foto_perfil = obterFotoPerfilDoUsuarioPorId($usuario_id);

// Recupera o código de venda da sessão
$codigo_venda = $_SESSION['dados_emissao']['codigo_venda'] ?? '';

// Se o código de venda mudar, limpa os dados da sessão para garantir consistência
if (isset($_POST['codigo_venda']) && $_POST['codigo_venda'] != $codigo_venda) {
    session_unset();  // Limpa a sessão quando mudar a venda
    $_SESSION['dados_emissao']['codigo_venda'] = $_POST['codigo_venda']; // Atualiza com o novo código de venda
}


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
$dadosPainelFixa = $_SESSION['dados_painel_fixa'] ?? [];
$dadosPainelMovel = $_SESSION['dados_painel_movel'] ?? [];

// Recupera os valores dos campos gravados na página anterior
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




// Se o método de requisição for POST, processa os dados
// Armazenar os valores no session ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Armazenar o valor e o nome do plano base
    $_SESSION['dados_emissao']['planoBase'] = $_POST['planoBase'] ?? '';
    $_SESSION['dados_emissao']['planoBaseNome'] = $_POST['planoBaseNome'] ?? '';

    // Armazenar o valor e o nome da linha fixa
    $_SESSION['dados_emissao']['linhaFixa'] = $_POST['linhaFixa'] ?? '';
    $_SESSION['dados_emissao']['linhaFixaNome'] = $_POST['linhaFixaNome'] ?? '';

    // Armazenar o valor e o nome da TV (IPTV)
    $_SESSION['dados_emissao']['tvIptv'] = $_POST['tvIptv'] ?? '';
    $_SESSION['dados_emissao']['tvIptvNome'] = $_POST['tvIptvNome'] ?? '';

    // Armazenar o valor e o nome dos serviços adicionais
    $_SESSION['dados_emissao']['servicosAdicionais1'] = $_POST['servicosAdicionais1'] ?? '';
    $_SESSION['dados_emissao']['servicosAdicionais1Nome'] = $_POST['servicosAdicionais1Nome'] ?? '';

    $_SESSION['dados_emissao']['servicosAdicionais2'] = $_POST['servicosAdicionais2'] ?? '';
    $_SESSION['dados_emissao']['servicosAdicionais2Nome'] = $_POST['servicosAdicionais2Nome'] ?? '';

    $_SESSION['dados_emissao']['servicosAdicionais3'] = $_POST['servicosAdicionais3'] ?? '';
    $_SESSION['dados_emissao']['servicosAdicionais3Nome'] = $_POST['servicosAdicionais3Nome'] ?? '';

    // Armazenar o valor e o nome do plano móvel
    $_SESSION['dados_emissao']['planoMovel'] = $_POST['planoMovel'] ?? '';
    $_SESSION['dados_emissao']['planoMovelNome'] = $_POST['planoMovelNome'] ?? '';

    // Armazenar o valor e o nome dos dependentes
    $_SESSION['dados_emissao']['dependentes'] = $_POST['dependentes'] ?? '';
    $_SESSION['dados_emissao']['dependentesNome'] = $_POST['dependentesNome'] ?? '';

    // Armazenar os valores dos serviços adicionais móveis
    $_SESSION['dados_emissao']['servicosAdicionaisMovel1'] = $_POST['servicosAdicionaisMovel1'] ?? '';
    $_SESSION['dados_emissao']['servicosAdicionaisMovel1Nome'] = $_POST['servicosAdicionaisMovel1Nome'] ?? '';

    $_SESSION['dados_emissao']['servicosAdicionaisMovel2'] = $_POST['servicosAdicionaisMovel2'] ?? '';
    $_SESSION['dados_emissao']['servicosAdicionaisMovel2Nome'] = $_POST['servicosAdicionaisMovel2Nome'] ?? '';

    // Armazena os valores totais fixa e Móvel
    $_SESSION['dados_emissao']['receitaTotalFixa'] = $_POST['receitaTotalFixaHidden'];
    $_SESSION['dados_emissao']['receitaTotalMovel'] = $_POST['receitaTotalMovelHidden'];
    $_SESSION['dados_emissao']['receitaTotalPedido'] = $_POST['receitaTotalPedidoHidden'];


    // Botão Avançar
    if (isset($_POST['avancar'])) {
        header("Location: emissao_06.php");
        exit();
    }

    // Botão Voltar
    if (isset($_POST['voltar'])) {
        header("Location: emissao_04.php");
        exit();
    }
    if (isset($_POST['sair_venda'])) {
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
                <div class="header-title sub3"><span class="tittle" onclick="window.location.href='emissao_04.php'">&nbsp; Dados upgrade</span></div>
                <div class="header-title sub03">&nbsp; Produtos</div>
                </div>
            </div>
<!-- Painel Fixa -->
<!-- Código Venda (oculto) -->
<input type="hidden" name="codigo_venda" value="<?= $codigo_venda ?>">
<div class="panel2">
    <h5 class="form-header">Fixa</h5>
    <form method="POST">
        <div class="row mb-3">
            <!-- Plano Base -->
            <div class="col-md-3">
                <label for="planoBase" class="form-label">Plano Base</label>
                <select class="form-select" id="planoBase" name="planoBase" onchange="atualizarPlanoMovel()">
                    <option value="" selected>Selecione</option>
                    <?php foreach ($produtos['DADOS'] as $produto): ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['planoBase']) && $_SESSION['dados_emissao']['planoBase'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= htmlspecialchars(trim($produto['nome'])) ?>">
                            <?= htmlspecialchars($produto['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Linha Fixa -->
            <div class="col-md-3">
                <label for="linhaFixa" class="form-label">Linha Fixa</label>
                <select class="form-select" id="linhaFixa" name="linhaFixa" onchange="calcularTotal()">
                    <option value="" selected>Selecione</option>
                    <?php foreach ($produtos['VOZ'] as $produto): ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['linhaFixa']) && $_SESSION['dados_emissao']['linhaFixa'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="linhaFixaValor" name="linhaFixaValor">
            </div>

            <!-- TV (IPTV) -->
            <div class="col-md-3">
                <label for="tvIptv" class="form-label">TV (IPTV)</label>
                <select class="form-select" id="tvIptv" name="tvIptv" onchange="calcularTotal()">
                    <option value="" selected>Selecione</option>
                    <?php foreach ($produtos['TV'] as $produto): ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['tvIptv']) && $_SESSION['dados_emissao']['tvIptv'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="tvIptvValor" name="tvIptvValor">
            </div>

            <!-- Receita Total Fixa -->
            <div class="col-md-3">
                <label for="receitaTotalFixa" class="form-label">Receita Total Fixa</label>
                <input type="text" id="receitaTotalFixa" name="receitaTotalFixa" class="form-control" value="R$ 0,00" disabled>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Serviços Adicionais Fixa 1 -->
            <div class="col-md-4">
                <label for="servicosAdicionais1" class="form-label">Serviços Adicionais Fixa 1</label>
                <select class="form-select" id="servicosAdicionais1" name="servicosAdicionais1" onchange="calcularTotal()">
                    <option value="" selected>Selecione</option>
                    <?php foreach ($produtos['ADICIONAL'] as $produto): ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['servicosAdicionais1']) && $_SESSION['dados_emissao']['servicosAdicionais1'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="servicosAdicionais1Valor" name="servicosAdicionais1Valor">
            </div>

            <!-- Serviços Adicionais Fixa 2 -->
            <div class="col-md-4">
                <label for="servicosAdicionais2" class="form-label">Serviços Adicionais Fixa 2</label>
                <select id="servicosAdicionais2" name="servicosAdicionais2" class="form-select" onchange="calcularTotal()">
                    <option value="" selected>Selecione</option>
                    <?php foreach ($produtos['ADICIONAL'] as $produto): ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['servicosAdicionais2']) && $_SESSION['dados_emissao']['servicosAdicionais2'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="servicosAdicionais2Valor" name="servicosAdicionais2Valor">
            </div>

            <!-- Serviços Adicionais Fixa 3 -->
            <div class="col-md-4">
                <label for="servicosAdicionais3" class="form-label">Serviços Adicionais Fixa 3</label>
                <select id="servicosAdicionais3" class="form-select" name="servicosAdicionais3" onchange="calcularTotal()">
                    <option value="" selected>Selecione</option>
                    <?php foreach ($produtos['ADICIONAL'] as $produto): ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['servicosAdicionais3']) && $_SESSION['dados_emissao']['servicosAdicionais3'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="servicosAdicionais3Valor" name="servicosAdicionais3Valor">
            </div>
        </div>

</div>

<!-- Painel Móvel -->
<div class="panel2">
    <h5 class="form-header">Móvel</h5>
    <div class="row mb-3">
        <!-- Plano Móvel -->
        <div class="col-md-3">
            <label for="planoMovel" class="form-label">Plano Móvel</label>
            <select id="planoMovel" class="form-select" name="planoMovel">
                <option value="">Selecione</option>
                <?php foreach ($produtos['CELULAR'] as $produto): ?>
                    <option value="<?= $produto['valor'] ?>"
                        <?= (isset($_SESSION['dados_emissao']['planoMovel']) && $_SESSION['dados_emissao']['planoMovel'] == $produto['valor']) ? 'selected' : '' ?>
                        data-nome="<?= htmlspecialchars(trim($produto['nome'])) ?>">
                        <?= htmlspecialchars($produto['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Dados Móveis -->
        <div class="col-md-3">
            <label for="dependentes" class="form-label">Possui Dependentes?</label>
            <select id="dependentes" class="form-select" name="dependentes" onchange="calcularTotal()">
                <option value="">Selecione</option>
                <option value="SIM" <?= (isset($_SESSION['dados_emissao']['dependentes']) && $_SESSION['dados_emissao']['dependentes'] == 'SIM') ? 'selected' : '' ?>>SIM</option>
                <option value="NÃO" <?= (isset($_SESSION['dados_emissao']['dependentes']) && $_SESSION['dados_emissao']['dependentes'] == 'NÃO') ? 'selected' : '' ?>>NÃO</option>
            </select>
        </div>

        <!-- Receita Total Móvel -->
        <div class="col-md-3">
            <label for="receitaTotalMovel" class="form-label">Receita Total Móvel</label>
            <input type="text" id="receitaTotalMovel" name="receitaTotalMovel" class="form-control" value="R$ 0,00" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Serviços Adicionais Móvel 1 -->
        <div class="col-md-4">
            <label for="servicosAdicionaisMovel1" class="form-label">Serviços Adicionais Móvel 1</label>
            <select id="servicosAdicionaisMovel1" class="form-select" name="servicosAdicionaisMovel1" onchange="calcularTotal()">
                <option value="0">Selecione</option>
                <?php
                // Carregar dados de serviços adicionais com a categoria "CELULAR"
                $categoria = 'CELULAR';
                foreach ($produtos['ADICIONAL'] as $produto):
                    if ($produto['categoria'] == $categoria):
                        ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['servicosAdicionaisMovel1']) && $_SESSION['dados_emissao']['servicosAdicionaisMovel1'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endif; endforeach; ?>
            </select>
            <input type="hidden" id="servicosAdicionaisMovel1Valor" name="servicosAdicionaisMovel1Valor">
        </div>

        <!-- Serviços Adicionais Móvel 2 -->
        <div class="col-md-4">
            <label for="servicosAdicionaisMovel2" class="form-label">Serviços Adicionais Móvel 2</label>
            <select id="servicosAdicionaisMovel2" class="form-select" name="servicosAdicionaisMovel2" onchange="calcularTotal()">
                <option value="0">Selecione</option>
                <?php
                // Carregar dados de serviços adicionais com a categoria "CELULAR"
                foreach ($produtos['ADICIONAL'] as $produto):
                    if ($produto['categoria'] == $categoria):
                        ?>
                        <option value="<?= $produto['valor'] ?>"
                            <?= (isset($_SESSION['dados_emissao']['servicosAdicionaisMovel2']) && $_SESSION['dados_emissao']['servicosAdicionaisMovel2'] == $produto['valor']) ? 'selected' : '' ?>
                            data-nome="<?= $produto['nome'] ?>">
                            <?= $produto['nome'] ?>
                        </option>
                    <?php endif; endforeach; ?>
            </select>
            <input type="hidden" id="servicosAdicionaisMovel2Valor" name="servicosAdicionaisMovel2Valor">
        </div>
    </div>
</div>

<!-- TOTAL -->
<div class="content-center">
    <div class="col-md-3">
        <label for="receitaTotalPedido" class="form-label custom-label"
            style="font-weight: 500; color: #003366; display: block; text-align: center;">Receita Total Pedido</label>
        <input type="text" class="form-control" id="receitaTotalPedido" name="receitaTotalPedido" style="display: block; text-align: center;" value="R$ 0,00" disabled>
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
</div>

<!-- pega todos os select Nome e valores totais -->
<input type="hidden" id="planoBaseNome" name="planoBaseNome" value="">
<input type="hidden" id="linhaFixaNome" name="linhaFixaNome" value="">
<input type="hidden" id="tvIptvNome" name="tvIptvNome" value="">
<input type="hidden" id="servicosAdicionais1Nome" name="servicosAdicionais1Nome" value="">
<input type="hidden" id="servicosAdicionais2Nome" name="servicosAdicionais2Nome" value="">
<input type="hidden" id="servicosAdicionais3Nome" name="servicosAdicionais3Nome" value="">
<input type="hidden" id="planoMovelNome" name="planoMovelNome" value="">
<input type="hidden" id="dependentesNome" name="dependentesNome" value="">
<input type="hidden" id="receitaTotalFixaHidden" name="receitaTotalFixaHidden" value="">
<input type="hidden" id="receitaTotalMovelHidden" name="receitaTotalMovelHidden" value="">
<input type="hidden" id="receitaTotalPedidoHidden" name="receitaTotalPedidoHidden" value="">
<input type="hidden" id="servicosAdicionaisMovel1Nome" name="servicosAdicionaisMovel1Nome" value="">
<input type="hidden" id="servicosAdicionaisMovel2Nome" name="servicosAdicionaisMovel2Nome" value="">

</form>
</div>
</div>

</div>
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script>
document.addEventListener("DOMContentLoaded", function () {
    function calcularTotal() {
        let receitaFixa = 0;
        let receitaMovel = 0;

        // Obter valores selecionados para os produtos (direto dos selects)
        let planoBase = parseFloat(document.getElementById("planoBase").value) || 0;
        let linhaFixa = parseFloat(document.getElementById("linhaFixa").value) || 0;
        let tvIptv = parseFloat(document.getElementById("tvIptv").value) || 0;

        // Obter os valores dos serviços adicionais diretamente dos selects ou hidden (valores de reserva)
        let servicosAdicionais1 = parseFloat(document.getElementById("servicosAdicionais1").value) || 0;
        let servicosAdicionais2 = parseFloat(document.getElementById("servicosAdicionais2").value) || 0;
        let servicosAdicionais3 = parseFloat(document.getElementById("servicosAdicionais3").value) || 0;

        // Atualizar os campos ocultos com os nomes selecionados
        document.getElementById('planoBaseNome').value = document.querySelector('#planoBase option:checked').getAttribute('data-nome') || '';
        document.getElementById('linhaFixaNome').value = document.querySelector('#linhaFixa option:checked').getAttribute('data-nome') || '';
        document.getElementById('tvIptvNome').value = document.querySelector('#tvIptv option:checked').getAttribute('data-nome') || '';
        document.getElementById('servicosAdicionais1Nome').value = document.querySelector('#servicosAdicionais1 option:checked').getAttribute('data-nome') || '';
        document.getElementById('servicosAdicionais2Nome').value = document.querySelector('#servicosAdicionais2 option:checked').getAttribute('data-nome') || '';
        document.getElementById('servicosAdicionais3Nome').value = document.querySelector('#servicosAdicionais3 option:checked').getAttribute('data-nome') || '';

        // Somar valores Fixa
        receitaFixa = planoBase + linhaFixa + tvIptv + servicosAdicionais1 + servicosAdicionais2 + servicosAdicionais3;
        document.getElementById("receitaTotalFixa").value = "R$ " + receitaFixa.toFixed(2);

        // Atualiza o valor do campo oculto para envio no formulário
        document.getElementById('receitaTotalFixaHidden').value = receitaFixa.toFixed(2);

        // Somar valores Móvel
        let planoMovel = parseFloat(document.getElementById("planoMovel").value) || 0;
        let dependentes = parseFloat(document.getElementById("dependentes").value) || 0;
        let servicosAdicionaisMovel1 = parseFloat(document.getElementById("servicosAdicionaisMovel1").value) || 0;
        let servicosAdicionaisMovel2 = parseFloat(document.getElementById("servicosAdicionaisMovel2").value) || 0;

        // Atualizar os campos ocultos com os nomes selecionados
        document.getElementById('planoMovelNome').value = document.querySelector('#planoMovel option:checked').getAttribute('data-nome') || '';
        document.getElementById('dependentesNome').value = document.querySelector('#dependentes option:checked').getAttribute('data-nome') || '';

        // Somar valores Móvel
        receitaMovel = planoMovel + dependentes + servicosAdicionaisMovel1 + servicosAdicionaisMovel2;
        document.getElementById("receitaTotalMovel").value = "R$ " + receitaMovel.toFixed(2);

        // Atualiza o valor do campo oculto para envio no formulário
        document.getElementById('receitaTotalMovelHidden').value = receitaMovel.toFixed(2);

        // Soma total
        let receitaTotal = receitaFixa + receitaMovel;
        document.getElementById("receitaTotalPedido").value = "R$ " + receitaTotal.toFixed(2);
        document.getElementById('receitaTotalPedidoHidden').value = receitaTotal.toFixed(2);
    }

    // Chama a função inicialmente para configurar as opções corretamente
    calcularTotal();

    // Evento para recalcular o total após alteração em qualquer select
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function () {
            calcularTotal();
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function atualizarPlanoMovel() {
        const planoBaseSelect = document.getElementById('planoBase');
        const planoMovelSelect = document.getElementById('planoMovel');
        const planoMovelNomeInput = document.getElementById('planoMovelNome');

        const planoBaseOption = planoBaseSelect.options[planoBaseSelect.selectedIndex];
        const planoBaseNome = planoBaseOption.getAttribute('data-nome') || '';

        const vivoTotal = 'VIVO TOTAL';
        const vivoTotalProMovel = 'VIVO TOTAL PRO MOVEL';

        // Se contiver "VIVO TOTAL" no Plano Base
        if (planoBaseNome.toUpperCase().includes(vivoTotal)) {
            for (let option of planoMovelSelect.options) {
                const nomeMovel = option.getAttribute('data-nome') || '';
                if (nomeMovel.toUpperCase() === vivoTotalProMovel) {
                    option.selected = true; // Seleciona "VIVO TOTAL PRO MOVEL"
                    planoMovelSelect.disabled = true; // Desabilita o select
                    planoMovelNomeInput.value = nomeMovel; // Atualiza o input hidden
                    break;
                }
            }
        } else {
            // Caso contrário, habilita o select e limpa seleção
            planoMovelSelect.disabled = false;
            planoMovelSelect.value = ''; 
            planoMovelNomeInput.value = ''; // Limpa o valor do input hidden

            // Desabilita opções contendo "VIVO TOTAL"
            for (let option of planoMovelSelect.options) {
                const nomeMovel = option.getAttribute('data-nome') || '';
                if (nomeMovel.toUpperCase().includes(vivoTotal)) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            }
        }
    }

    function atualizarPlanoMovelNome() {
        const planoMovelSelect = document.getElementById('planoMovel');
        const planoMovelNomeInput = document.getElementById('planoMovelNome');

        const planoMovelOption = planoMovelSelect.options[planoMovelSelect.selectedIndex];
        const planoMovelNome = planoMovelOption ? planoMovelOption.getAttribute('data-nome') || '' : '';
        planoMovelNomeInput.value = planoMovelNome; // Atualiza o input hidden com o nome
    }

    // Reexecuta a lógica ao carregar a página para garantir a consistência
    atualizarPlanoMovel();

    // Eventos para recalcular e atualizar valores ao alterar selects
    const planoBaseSelect = document.getElementById('planoBase');
    const planoMovelSelect = document.getElementById('planoMovel');

    planoBaseSelect.addEventListener('change', () => {
        atualizarPlanoMovel();
        atualizarPlanoMovelNome();
    });

    planoMovelSelect.addEventListener('change', atualizarPlanoMovelNome);
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Seleciona os selects pelos IDs
        const selects = [
            document.getElementById("servicosAdicionais1"),
            document.getElementById("servicosAdicionais2"),
            document.getElementById("servicosAdicionais3")
        ];

        // Seleciona os inputs hidden pelos IDs
        const hiddenInputs = [
            document.getElementById("servicosAdicionais1Valor"),
            document.getElementById("servicosAdicionais2Valor"),
            document.getElementById("servicosAdicionais3Valor")
        ];

        // Função que atualiza as opções dos selects
        function atualizarOpcoes() {
            // Cria um array com os valores selecionados (ignorando valores vazios)
            const selecionados = selects.map((select, index) => {
                // Pega o valor do select ou do input hidden
                return select.value || hiddenInputs[index].value;
            }).filter(value => value !== "");

            // Atualiza cada select
            selects.forEach((select, index) => {
                const valorAtual = select.value;

                // Reabilita todas as opções inicialmente
                Array.from(select.options).forEach(option => {
                    option.disabled = false; // Reabilita todas as opções
                });

                // Desabilita as opções que já foram selecionadas nos outros selects ou nos inputs hidden
                selecionados.forEach(selecionado => {
                    if (selecionado !== valorAtual) {
                        Array.from(select.options).forEach(option => {
                            if (option.value === selecionado) {
                                option.disabled = true; // Desabilita a opção
                            }
                        });
                    }
                });
            });
        }

        // Adiciona um evento "change" a cada select
        selects.forEach((select, index) => {
            select.addEventListener("change", function () {
                // Atualiza o valor no input hidden correspondente
                hiddenInputs[index].value = select.value;

                // Atualiza as opções disponíveis
                atualizarOpcoes(); // Chama a função para atualizar as opções
            });
        });

        // Chama a função inicialmente para configurar as opções corretamente
        atualizarOpcoes();
    });


</script>

</html>