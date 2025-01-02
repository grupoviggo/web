<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FAZENDO LOGIN</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="./css/loginnexus.css">
</head>
<body>
<div id="loading-overlay">
        <div id="loading-container">
            <div id="loading-circle"></div>
            <p id="loading-text">aguarde...</p>
        </div>
    </div>

    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
        // Exibir a tela de carregamento
        document.getElementById('loading-overlay').style.display = 'flex';

        // Simular um atraso de 2,5 segundos
        setTimeout(function() {
            // Redirecionar para menu.php após o atraso
            window.location.href = 'menu';
        }, 500);
    </script>
</body>
</html>
