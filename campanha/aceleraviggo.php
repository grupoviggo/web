<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acelera Viggo</title>
    <style>
        body {
            margin: 0;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        img {
            width: 450px;
            height: auto;
            animation: runEffect 1s linear 0s 3, zoomEffect 2s ease-in-out 3s forwards;
            animation-fill-mode: forwards;
        }

        @keyframes runEffect {
            0% {
                transform: translateX(900%); /* Começa fora da tela à direita */
            }
            25% {
                transform: translateX(-900%); /* Vai completamente para fora da tela à esquerda */
            }
            50% {
                transform: translateX(900%); /* Volta pela direita */
            }
            75% {
                transform: translateX(-900%); /* Vai para a esquerda novamente */
            }
            100% {
                transform: translateX(0); /* Para no centro da tela */
            }
        }

        @keyframes zoomEffect {
            0% {
                transform: scale(1); /* Começa no tamanho normal */
            }
            25% {
                transform: scale(1.2); /* Aumenta até 1.2x */
            }
            50% {
                transform: scale(1); /* Volta ao tamanho normal */
            }
            75% {
                transform: scale(1.2); /* Aumenta até 1.2x */
            }
            100% {
                transform: scale(1); /* Volta ao tamanho normal */
            }
        }

    </style>
</head>
<body>
    <img src="Acelera Viggo - Logo.png" alt="Logo Acelera Viggo">

    <script>
        setTimeout(() => {
            window.location.href = "acelera.php";
        }, 6000);
    </script>
        <script>
        // Impede o clique com o botão direito do mouse
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    alert('A visualização do código fonte foi bloqueada.');
});

// Impede o uso de teclas de atalho como Ctrl+U e Ctrl+Shift+I
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 'i' || e.key === 'I' || e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        alert('A visualização do código fonte foi bloqueada.');
    }
});

    </script>
</body>
</html>
