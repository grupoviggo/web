<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    // Redireciona para a página adm.php se o usuário não estiver logado
    header("Location: admpgi.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/painelusuarios.css">

</head>
<body>
        <!-- Barra de navegação Bootstrap -->
        <nav class="navbar navbar-expand-lg navbar-dark">
        <!-- Adiciona a imagem ao lado esquerdo -->
        <a class="navbar-brand" href="painel">
            <img src="../img/pgiloginbd.png" width="auto" height="25px" alt="">
        </a>
                <!-- Linha vertical pontilhada -->
                <div class="vertical-divider"></div>
        <!-- Nome do usuário e tempo logado -->
        <div class="navbar-text text-light ml-3 small"> <!-- Adiciona a classe small para diminuir o texto -->
            <spam class="label-text"><strong>SGU - SISTEMA DE GESTÃO AO BANCO DE DADOS</strong></span>
        </div>
        <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-danger btn-sm btn-sair" type="submit" onclick="window.location.href='sairadmpgi'; return false;">SAIR</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>    
<div class="container mt-4">
    <h2 class="text-center mb-4">cadastrar novo usuário</h2>
    <hr>
    <form method="POST" action="cadastro_usuario_pgi">
        <div class="form-group">
            <label for="email">E-MAIL:</label>
            <input type="text" id="email" class="form-control" name="email" placeholder="usuario@grupoviggo.com.br" maxlength="150" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase()">
        </div>

        <div class="form-group">
            <label for="nome">NOME:</label>
            <input type="text" id="nome" class="form-control" name="nome" placeholder="NOME COMPLETO" maxlength="50" style="text-transform: uppercase;" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').toUpperCase()">
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
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" class="form-control" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
        </div>

        <div class="form-group">
            <label for="cpf">DATA DE NASCIMENTO:</label>
            <input type="text" id="dt_nascimento" class="form-control" name="dt_nascimento" placeholder="DD/MM/AAAA" maxlength="10" style="width: 100%;" required>
        </div>

        <div class="form-group">
            <label for="cargo">CARGO:</label>
            <select id="cargo" class="form-control" name="cargo" onchange="atualizarDepartamento()">
                <option value="GERENTE">GERENTE</option>
                <option value="COORDENADOR">COORDENADOR</option>
                <option value="SUPERVISOR">SUPERVISOR</option>
                <option value="VENDEDOR">VENDEDOR</option>
                <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
            </select>
        </div>

        <div class="form-group">
            <label for="departamento">DEPARTAMENTO:</label>
            <select id="departamento" class="form-control" name="departamento">
                <option value="COMERCIAL">COMERCIAL</option>
            </select>
        </div>

        <div class="btn-group">
            <button type="submit" id="cadastrar" class="btn btn-warning">CADASTRAR</button>
            <button type="button" id="users" name="users" class="btn btn-info" onclick="window.location.href='usuarios_pgi'; return false;">VER USUÁRIOS</button>
        </div>
    </form>
</div>
<br>
  <!-- Adicionando jQuery (certifique-se de incluir isso antes do seu script) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <script>
    $(document).ready(function() {
    // Máscara para o campo CPF
    $('#cpf').mask('000.000.000-00');
    // Máscara para o campo Data de Nascimento
    $('#dt_nascimento').mask('00/00/0000');
});

  </script>
<script>
    function atualizarDepartamento() {
        var cargo = document.getElementById("cargo").value;
        var departamento = document.getElementById("departamento");
        departamento.innerHTML = "";

        if (cargo === "GERENTE" || cargo === "COORDENADOR" || cargo === "SUPERVISOR" || cargo === "VENDEDOR") {
            var opcao = document.createElement("option");
            opcao.value = "COMERCIAL";
            opcao.text = "COMERCIAL";
            departamento.appendChild(opcao);
        } else if (cargo === "ADMINISTRATIVO") {
            var opcoes = [
                { value: "COMERCIAL", text: "COMERCIAL" },
                { value: "BACKOFFICE", text: "BACKOFFICE" },
                { value: "PLANEJAMENTO/T.I", text: "PLANEJAMENTO OU T.I" },
                { value: "FINANCEIRO", text: "FINANCEIRO" },
                { value: "RH OU DP", text: "RH OU DP" }
            ];
            opcoes.forEach(function (opcao) {
                var elemento = document.createElement("option");
                elemento.value = opcao.value;
                elemento.text = opcao.text;
                departamento.appendChild(elemento);
            });
        }
    }
</script>
</body>
</html>