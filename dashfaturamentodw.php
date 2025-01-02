<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index");
    exit();
}

// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Verifica se o nível de acesso é diferente de 0, se sim, redireciona para o index
if ($nivel_acesso != 0) {
    header("Location: index");
    exit();
}

// Se o botão de sair foi clicado
if (isset($_POST['sairnexus'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: index");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>DASHBOARD - FATURAMENTO</title>
    <!-- Adicione os links para o Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="./js/ocultarCodigoFonte.js"></script>
    <script src="./js/scripts_principais_dash_fat.js"></script>
    <link rel="stylesheet" href="./css/dash_dir_fat.css">
</head>
<body oncontextmenu="return false;">
    <!-- Barra de navegação Bootstrap -->
    <div class="navbar navbar-expand-lg navbar-dark">
        <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                    <form class="form-inline my-2 my-lg-0">
                        <img src="./img/voltar.png" class="voltaricon" alt="voltar" title="voltar a lista">
                        <img src="./img/home.png" class="homeicon" alt="home" title="menu principal">
                        <img src="./img/pontos.png" class="pontosicon">
                        <img src="./img/setamenu.png" class="seta-menu" alt="Seta Menu" title="full screen">
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <div class="menu navbar-expand-lg navbar-dark">
        <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                    <form class="form-inline my-2 my-lg-0">
                        <img src="./img/voltar2.png" class="voltaricon2" alt="voltar">
                        <img src="./img/home2.png" class="homeicon2" alt="home">
                        <img src="./img/pontos2.png" class="pontosicon2">
                        <img src="./img/setamenu2.png" class="seta-menu2" alt="Seta Menu">
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <div class="bar-direita" style="margin-right: 6px;"></div>
    <div class="iframe-container" style="margin-left: -6px;">
        <iframe id="powerBIframe" title="DASHBOARD FATURAMENTO" width="1510" style="overflow: hidden;" src="sec_dash_fatdw" frameborder="0" scrolling="NO" allowFullScreen="true" allowtransparency="true"></iframe>
        </div>
    <!-- Adicione o link para o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Para desativar o menu de contexto do botão direito do mouse
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
        });
    </script>
</body>
</html>
