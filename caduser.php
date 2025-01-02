<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    // Redireciona para a página adm.php se o usuário não estiver logado
    header("Location: adm.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CADASTRAR USUÁRIO</title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css/painelusuarios.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="painel">
            <img src="../img/nexusloginbd.png" width="auto" height="25px" alt="">
        </a>
        <div class="vertical-divider"></div>
        <div class="navbar-text text-light ml-3 small">
            <strong>SGU - SISTEMA DE GESTÃO AO BANCO DE DADOS</strong>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='sairadm'; return false;">VOLTAR</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav> 
<div class="container mt-4">
    <h2 class="text-center mb-4">cadastrar novo usuário</h2>
    <hr>
    <form method="POST" action="cadastrouser">
        <div class="form-group">
            <label for="email">E-MAIL:</label>
            <input type="text" id="usuario" class="form-control" name="usuario" placeholder="usuario@grupoviggo.com.br" maxlength="150" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase()" required>
        </div>

        <div class="form-group">
            <label for="nivel">NÍVEL ACESSO:</label>
            <select id="nivel" class="form-control" name="nivel">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cpf">TÍTULO DASH:</label>
            <input type="text" id="titulo" class="form-control" name="titulo" placeholder="PAP - LESTE" maxlength="80" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
        </div>

        <div class="form-group">
            <label for="cpf">LINK DASH:</label>
            <input type="text" id="linque" class="form-control" name="linque" placeholder="pap-leste.php" maxlength="80" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase()">
        </div>

        <div class="form-group">
            <label for="nome">NOME:</label>
            <input type="text" id="username" class="form-control" name="username" placeholder="NOME SOBRENOME" maxlength="150" style="text-transform: uppercase;" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').toUpperCase()">
        </div>
        <br>
        <div class="btn-group">
            <button type="submit" id="cadastrar" class="btn btn-warning">CADASTRAR</button>
            <button type="button" id="users" name="users" class="btn btn-info" onclick="window.location.href='usuarios'; return false;">VER USUÁRIOS</button>
        </div>
    </form>
</div>
<br>
</body>
</html>