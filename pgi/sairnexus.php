<?php
session_start();

// Encerra a sessão
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAINDO...</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style>
        @font-face {
    font-family: 'Typo Hoop'; /* Nome que você vai usar no CSS */
    src: url('../fonts/TypoHoop_Bold.otf') format('opentype');
    font-weight: bold;
    font-style: bold;
}

        body {
            background-color: #EDF3FB; 
        }

        #loading-overlay {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #EDF3FB;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 9999;
            color: #EDF3FB;
            flex-direction: column;
        }
        
        #loading-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 350px; /* Maior largura */
            height: 350px; /* Maior altura */
        }
        
        #loading-circle {
            border: 20px solid #EDF3FB;
            border-top: 20px solid #44abb6;
            border-radius: 50%;
            width: 350px; /* Tamanho maior do círculo */
            height: 350px;
            animation: spin 1.5s linear infinite;
            /* Adicionando o efeito de borda "sumindo" */
            box-shadow: 0 0 5px 5px rgba(68, 171, 182, 0.41);
            background: transparent;
            position: absolute;
            mask-image: radial-gradient(circle, rgba(0, 0, 0, 1) 90%, rgba(0, 0, 0, 0) 100%);
            -webkit-mask-image: radial-gradient(circle, rgba(0, 0, 0, 1) 90%, rgba(0, 0, 0, 0) 100%);
        }

        #loading-container img {
            position: absolute;
            width: 60%; /* Maior imagem */
            height: auto;
            animation: zoomInOut 2s infinite ease-in-out;
        }

        #loading-text {
            margin-top: 20px; /* Espaço abaixo do círculo */
            font-size: 18px;
            color: #4C4A4D;
            font-family: 'Typo Hoop', Arial, sans-serif; /* Fallbacks para segurança */
            font-weight: bold;
        }

        #loading-dots {
            display: inline-block;
            font-size: 18px;
            letter-spacing: 2px;
            color: #4C4A4D;
            font-family: 'Segoe UI', Arial, sans-serif; /* Fallbacks para segurança */
            font-weight: bold;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes zoomInOut {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</head>
<body>
    <div id="loading-overlay">
        <div id="loading-container">
            <div id="loading-circle"></div>
            <img src="../img/nexus_logo_centro.png" alt="nexus">
        </div>
        <div id="loading-text">
            saindo com segurança, aguarde<span id="loading-dots"></span>
        </div>
    </div>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        // Exibir a tela de carregamento
        document.getElementById('loading-overlay').style.display = 'flex';

        // Simular um atraso de 2,5 segundos para redirecionar
        setTimeout(function() {
            window.location.href = 'login';
        }, 4000);

        // Função para animar os pontos
        const dots = document.getElementById("loading-dots");
    let dotCount = 0;

    setInterval(() => {
        dotCount = (dotCount + 1) % 4; // Ciclo entre 0, 1, 2, 3
        dots.textContent = '.'.repeat(dotCount); // Adiciona os pontos
    }, 400); // Altere o tempo conforme necessário
    </script>
</body>
</html>