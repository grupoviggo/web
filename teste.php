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
    <style>
        body, html {
            margin: 0;
            padding: 0;
            background-color: #013565;
            overflow: hidden; 
        }

        iframe{
            width: 100%;
            height: 100vh;
        }
        /* Ajuste para a barra de navegação */
        .navbar {
            background-color: #051729d7;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 9999; /* Coloca a barra de navegação sobre o iframe */
        }
        .menu {
            position: fixed;
            width: 100%;
            top: 0;
            display: none;
            z-index: 999; /* Coloca a barra de navegação sobre o iframe */
        }

        .bar-sup-aux {
            top: 0;
            width: 71px;
            height: 38px;
            position: absolute;
            background-color: #152C5A;
            z-index: 99999;
            display: none;
        }
        .bar-esquerda {
            width: 2px;
            height: 100vh;
            position: absolute;
            background-color: #152C5A;
            z-index: 99;
        }
        .bar-direita {
             width: 14px;
             height: 100vh;
             right: 0;
             position: absolute;
             background-color: #013565;
             z-index: 98;
        }
        /* Estilos personalizados para o botão SAIR */
        .btn-voltar {
            color: #013565; 
            font-weight: bold;
            margin-right: 6px; 
        }
        .btn-sair {
            color: #fff; 
            font-weight: bold;
            margin-right: 20px; 
        }
        .seta-menu {
            width: 20px;
            height: auto;
            position: absolute;
            top: 50%;
            right: 5px;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.5;
        }
        .seta-menu2 {
            width: 20px;
            height: auto;
            position: absolute;
            top: 50%;
            right: 5px;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.5;
            display: none;
        }
        .rodape {
            background-color: #013565;
            position: fixed;
            width: 100%;
            height: 73px;
            bottom: 0;
            display: block;
            z-index: 999999;
            
        }
    </style>
</head>
<body>
    <!-- Barra de navegação Bootstrap -->
    <div class="navbar navbar-expand-lg navbar-dark">
        <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='diretoria'; return false;">VOLTAR</button>&nbsp;
                        <button class="btn btn-danger btn-sm btn-sair" type="submit" onclick="window.location.href='menu'; return false;">SAIR</button>
                        <img src="./img/setamenu.png" class="seta-menu" alt="Seta Menu">
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
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" style="display: none;">VOLTAR</button>&nbsp;
                        <button class="btn btn-danger btn-sm btn-sair" type="submit" style="display: none;">SAIR</button>
                        <img src="./img/setamenu2.png" class="seta-menu2" alt="Seta Menu">
                    </form>
                </li>
            </ul>
        </div>
    </div>
   
 
    <iframe title="DASHBOARD FATURAMENTO2" width="1024" height="1060" src="https://app.powerbi.com/view?r=eyJrIjoiOTJjNjdlZTYtYTY0OS00OGMwLTkxODYtOGFiZDRjYTBkZGE2IiwidCI6ImU0YjUyYWNhLTQzNmItNDhmMC05NGY1LWFhM2U2ZmIzZDVjYiJ9" frameborder="0" allowFullScreen="true"></iframe>
        
    
    <!-- Adicione o link para o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function hideNav() {        
            document.querySelector('.navbar').style.display = 'none';
            document.querySelector('.seta-menu').style.display = 'none';
            document.querySelector('.seta-menu2').style.display = 'block';
            document.querySelector('.menu').style.display = 'block';
            
            const element = document.documentElement;
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) { /* Safari */
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { /* IE11 */
                element.msRequestFullscreen();
            }
        }
    
        function showNav() {
            document.querySelector('.navbar').style.display = 'block';
            document.querySelector('.seta-menu2').style.display = 'none';
            document.querySelector('.seta-menu').style.display = 'block';
            document.querySelector('.menu').style.display = 'none';   
        }
    
        window.onload = function() {
            // Adiciona o evento de clique na imagem para ocultar o menu e ativar o modo de tela cheia
            document.querySelector('.seta-menu').addEventListener('click', function() {
                hideNav();
            });
    
            // Adiciona o evento de clique na imagem para sair do modo de tela cheia e mostrar a navegação
            document.querySelector('.seta-menu2').addEventListener('click', function() {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                showNav();
            });
    
            // Mantém o menu sempre visível
            showNav();
    
            // Verifica se é um dispositivo móvel e redireciona se for
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                window.location.href = 'directbi.html';
            }
        };
    </script>   
</body>
</html>
