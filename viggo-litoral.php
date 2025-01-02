<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index");
    exit();
}

// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];
$usuario = $_SESSION['usuario'];

// Verifica se o nível de acesso é diferente de 0 ou 1, se sim, redireciona para o menu
if (!in_array($nivel_acesso, [0, 1])) {
    header("Location: menu");
    exit();
}

// Verifica se o nível é 1 e os usuarios abaixo
if ($nivel_acesso == 1 && !in_array($usuario, ['douglas.Barbosa@grupoviggo.com.br', 'robson.Barbosa@grupoviggo.com.br', 'julia.ferreira@grupoviggo.com.br'])) {
    header("Location: menu");
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
<html lang="pt-br">
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>DASHBOARD - VIGGO SÃO VICENTE</title>
    <!-- Adicione os links para o Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/dashs-comercial.css">
</head>
<body>
<!-- Barra de navegação Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                    <button id="toggleFullscreenBtn" class="btn btn-light btn-sm btn-tela" onclick="toggleFullScreen()">TELA CHEIA</button>
                    <button class="btn btn-warning btn-sm btn-voltar"  onclick="window.location.href='dashboards'; return false;">VOLTAR</button>
            </li>
        </ul>
    </div>
</nav>
<div class="bar-direita" style="margin-right: 0px;"></div>
    <div class="iframe-container" id="iframe-container">
        <iframe title="VIGGO SAO VICENTE" width="1510" src="https://app.powerbi.com/view?r=eyJrIjoiMmZjMzAxODEtNjU5ZS00Y2RkLThiYTItNWM5ZTNiMTEyMzE5IiwidCI6ImU0YjUyYWNhLTQzNmItNDhmMC05NGY1LWFhM2U2ZmIzZDVjYiJ9" frameborder="0" allowFullScreen="true"></iframe>
        </div>
    <div class="rodape"></div>
    <!-- Adicione o link para o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleFullScreen() {
        const elem = document.documentElement;
        const btn = document.getElementById("toggleFullscreenBtn");
        const iframeContainer = document.getElementById("iframe-container");
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { /* IE11 */
                elem.msRequestFullscreen();
            }
            iframeContainer.style.marginTop = "80px"; // Adiciona margem superior de 50px
            btn.textContent = "SAIR TELA CHEIA";
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) { /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { /* IE11 */
                document.msExitFullscreen();
            }
            iframeContainer.style.marginTop = "0"; // Remove a margem superior
            btn.textContent = "TELA CHEIA";
        }
        return false; // Evita a recarga da página
    }
    </script>
</body>
</html>
