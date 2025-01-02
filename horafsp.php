<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>DASHBOARD - HORA A HORA</title>
    <!-- Adicione os links para o Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .iframe-container {
            width: 100%;
            height: 100vh;
            z-index: 0;
            position: absolute;

        }

        iframe {
            width: 100%;
            max-width: 2240px;
            height: 100vh;
            border: none;
            margin: 0 auto;
            display: block;
            overflow: hidden;
            touch-action: none; 
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
            background-color: #fff;
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
        .btn-sair {
            color: #fff; 
            font-weight: bold;
            margin-right: 20px; 
        }
    </style>
</head>
<body>
<!-- Barra de navegação Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form class="form-inline my-2 my-lg-0">
                    <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='dashhora'; return false;">VOLTAR</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
    <div class="iframe-container">
    <iframe id="powerBIframe" title="DASH_HORA_FSP" width="1024" height="1060" src="https://app.powerbi.com/view?r=eyJrIjoiYzJhODc4YjEtYmU1NC00M2UwLTk2ZTEtZjQwMGMyMzY2OTE0IiwidCI6ImU0YjUyYWNhLTQzNmItNDhmMC05NGY1LWFhM2U2ZmIzZDVjYiJ9" frameborder="0" allowFullScreen="true"></iframe>
    </div>
    <div class="rodape"></div>
    <!-- Adicione o link para o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
