<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Função para obter a senha alterada do usuário pelo ID
function obterSenhaAlteradaDoUsuarioPorId($usuario_id) {
    $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    if (!$connect) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    $query = "SELECT senha_alterada FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && $dados = mysqli_fetch_assoc($result)) {
        return $dados['senha_alterada'];
    } else {
        return null;
    }

    // Fechar a conexão
    mysqli_stmt_close($stmt);
    mysqli_close($connect);
}

// Verifica se a senha foi alterada pelo ID do usuário
$usuario_id = $_SESSION['ID']; // Assumindo que o ID do usuário está armazenado na sessão
$senha_alterada = obterSenhaAlteradaDoUsuarioPorId($usuario_id);

// Verifica se a senha foi alterada
if ($senha_alterada == 0) {
    header("Location: alterar_senha.php");
    exit();
}

// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Se o botão de sair foi clicado
if (isset($_POST['sairnexus'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS (VIGGO)</title>
    <link rel="stylesheet" href="./css/menu.css">
    <style>
        /* Estilo para divs desativadas */
        .disabled {
            pointer-events: none; /* torna a div não clicável */
            opacity: 0.5; /* reduz a opacidade para tornar a div mais escura */
        }
    </style>
</head>
<body>
<img src="./img/Logo.svg" alt="Logo" class="logo">
<form method="POST" action="sairnexus" id="FormSair">
    <button class="Btn" type="submit">
        <div class="sign">
            <svg viewBox="0 0 512 512">
              <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
            </svg> 
        </div>  
          <div class="text">Desconectar</div>    
    </button>
</form>
<img src="./img/NexusLogobi.png" alt="LogoNexus" class="Nexus">
<br><br>
<h4>Portal de Inteligência Grupo Viggo</h4>
<hr class="styled-hr">
<div class="container">
    <div class="half-container">
        <a href="diretoria.php" id="dir_a">
            <div class="card" id="dir">
                <div class="title">DIRETORIA</div>
                <img src="./img/chart-diretoria.png" alt="Diretoria" width="100" height="100" class="image-dir">
            </div>
        </a>
        <a href="comercial.php" id="com_a">
            <div class="card" id="com">
                <div class="title">COMERCIAL</div>
                <img src="./img/chart-comercial.png" alt="COMERCIAL" width="100" height="100" class="image-dir2">
            </div>
        </a>
    </div>

    <div class="half-container">
        <a href="#" id="rh_a">
            <div class="card" id="rh">
                <div class="title">RH</div>
                <img src="./img/chart-rh.png" alt="RH" width="100" height="100" class="image-dir3">
            </div>
        </a>
        <a href="#" id="bko_a">
            <div class="card" id="bko">
                <div class="title">BKO</div>
                <img src="./img/chart-bko.png" alt="BKO" width="100" height="100" class="image-dir4">
            </div>
        </a>
    </div>
</div>

<script>
   // Função para desativar o clique nas divs com base no nível de acesso do usuário
function desativarCliqueDivs() {
    // Obtém o nível de acesso do usuário
    var nivel_acesso = <?php echo $nivel_acesso; ?>;

    // Função para desativar clique e trocar a imagem de uma lista de divs
    function desativarEAlterarImagem(divIDs, imagePath) {
        divIDs.forEach(function(id) {
            var div = document.getElementById(id);
            if (div) {
                div.classList.add('disabled');
                var img = div.querySelector('img');
                if (img) {
                    img.src = imagePath;
                }
            }

            // Altera o atributo href para "menu"
            var link = document.getElementById(id + '_a');
            if (link) {
                link.setAttribute('href', 'menu');
            }
        });
    }

    // Verifica se o nível de acesso é igual a 1
    if (nivel_acesso === 1) {
        // Desativa clique e altera imagem para 'trancar.png' nas divs '#dir', '#rh', '#bko'
        desativarEAlterarImagem(['dir', 'rh', 'bko'], './img/trancar.png');
    }

    // Verifica se o nível de acesso é igual a 2
    if (nivel_acesso === 2) {
        // Desativa clique e altera imagem para 'trancar.png' nas divs '#dir', '#rh', '#com'
        desativarEAlterarImagem(['dir', 'rh', 'com'], './img/trancar.png');
    }
}

// Chama a função ao carregar a página
window.onload = desativarCliqueDivs;

</script>




</body>
</html>
