<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index");
    exit();
}

// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Verifica se o nível de acesso é diferente de 0 ou 2, se sim, redireciona para o index
if ($nivel_acesso != 0 && $nivel_acesso != 2) {
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
<html lang="pt-br">
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>DASHBOARD - BKO RI</title>
    <!-- Adicione os links para o Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #F2F2F2;
        }
        .iframe-container {
            width: 100%;
            height: 100vh;
            z-index: 0;
            position: absolute;
            background-color: #F2F2F2;

        }

        iframe {
            width: 100%;
            max-width: 2240px;
            height: 100vh;
            border: none;
            margin: 0 auto;
            display: block;
            overflow: hidden;
            touch-action: none; /* Desativa o zoom dentro do iframe em dispositivos móveis */
            background-color: #F2F2F2;
        }
        .navbar {
        position: fixed;
        width: 100%;
        z-index: 9999; 
        top: -43px; 
        opacity: 0.1;
        transition: top 0.3s;
        background-color: #fff;
    }

    .navbar:hover {
        background-color: #042049;
        top: 0; 
        opacity: 0.9;
    }

    .navbar-nav .nav-item .form-inline {
        margin-top: 8px;
    }
        .rodape {
            background-color: #F2F2F2;
            position: fixed;
            width: 100%;
            height: 60px;
            bottom: 0;
            display: block;
            z-index: 999999; /* Coloca a barra de navegação sobre o iframe */

        }
        /* Estilos personalizados para o botão SAIR */
        .btn-voltar {
            color: #013565; 
            font-weight: bold;
            margin-right: 6px; 
        }
        .btn-tela {
            color: #013565; 
            font-weight: bold;
            margin-right: 6px; 
            
        }

        .btn-sair {
            color: #fff; 
            font-weight: bold;
            margin-right: 20px; 
        }
        .bar-direita {
            width: 14px;
            height: 100vh;
            right: 0;
            position: absolute;
            background-color: #F2F2F2;
            z-index: 98;
}

    </style>
</head>
<body>
<!-- Barra de navegação Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                    <button id="toggleFullscreenBtn" class="btn btn-light btn-sm btn-tela" onclick="toggleFullScreen()">TELA CHEIA</button>
                    <button class="btn btn-warning btn-sm btn-voltar"  onclick="window.location.href='bko'; return false;">VOLTAR</button>
            </li>
        </ul>
    </div>
</nav>
<div class="bar-direita" style="margin-right: 6px;"></div>
    <div class="iframe-container" style="margin-left: -6px;">
        <iframe id="powerBIframe" title="DASHBOARD BKO I" width="1024" height="1060" src="sec_dash_bko1" frameborder="0" allowFullScreen="true"></iframe>
    </div>
    <div class="rodape"></div>
    <!-- Adicione o link para o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleFullScreen() {
            const elem = document.documentElement;
            const btn = document.getElementById("toggleFullscreenBtn");
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) { /* Safari */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) { /* IE11 */
                    elem.msRequestFullscreen();
                }
                btn.textContent = "SAIR TELA CHEIA";
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) { /* Safari */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) { /* IE11 */
                    document.msExitFullscreen();
                }
                btn.textContent = "TELA CHEIA";
            }
            return false; // Evita a recarga da página
        }
    </script>
</body>
</html>





