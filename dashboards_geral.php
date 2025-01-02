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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARDS - REGIONAL II - GERAL</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/listagem.css">
</head>
<body style="height: auto;">

<!-- Barra de navegação Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <!-- Adiciona a imagem ao lado esquerdo -->
    <a class="navbar-brand" href="menu">
        <img src="./img/nexuslogin.png"  width="auto" height="25px" alt="">
    </a>
    <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                <form class="form-inline my-2 my-lg-0">
                    <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="history.back(); return false;">VOLTAR</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
<hr class="hr-white">
<div class="container mt-5">
    <h3 class="text-center mb-4">DASHBOARDS COMERCIAL</h3>
    <!-- Lista ordenada em formato de tabela -->
    <br/>
    <table class="table">
    <tbody>
<?php if ($nivel_acesso != 1): ?>
<tr>
    <td>
        <a href="pap-leste">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Pap Leste</span>
            </div>
        </a>
    </td>
</tr> 
<tr>
    <td>
        <a href="pap-nordeste">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Pap Nordeste</span>
            </div>
        </a>
    </td>
</tr>   
<tr>
    <td>
        <a href="pap-noroeste">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Pap Noroeste</span>
            </div>
        </a>
    </td>
</tr>
<tr>
    <td>
        <a href="pap-norte-oeste">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Pap Norte e Oeste</span>
            </div>
        </a>
    </td>
</tr>
<tr>
    <td>
        <a href="pap-sudeste">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Pap Sudeste</span>
            </div>
        </a>
    </td>
</tr>
<tr>
    <td>
        <a href="dashleonardogestao">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - VIGGO I & VIGGO II</span>
            </div>
        </a>
    </td>
</tr>  
<tr>
    <td>
        <a href="viggo-sorocaba">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Sorocaba</span>
            </div>
        </a>
    </td>
</tr>
<tr>
    <td>
        <a href="viggo-campinas">
            <div class="item">
                <img src="./img/grafic.png" alt="Comercial" width="18" height="18">
                <span>Dashboard - Campinas</span>
            </div>
        </a>
    </td>
</tr>
<?php endif; ?>

    </tbody>
</table>

</div>

<!-- Adicione o link para o Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>