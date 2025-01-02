<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>USUÁRIO CADASTRADO</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/styleCadastro.css">
</head>
<body>
<?php
$login = $_POST["usuario"];
$senha = "mudar123";
$nivel = $_POST["nivel"];
$titulo = $_POST["titulo"];
$linque = $_POST["linque"];
$username = $_POST["username"];

// Hash da senha usando password_hash
$senhaHashed = password_hash($senha, PASSWORD_DEFAULT);

$connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$connect) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Verifica se o login já existe
$verifica = mysqli_query($connect, "SELECT * FROM usuarios WHERE usuario = '$login'") or die("Erro ao selecionar");

if (mysqli_num_rows($verifica) > 0) {
    echo '
        <div class="modal" tabindex="-1" role="dialog" id="loginExistenteModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Erro de Cadastro</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Esse login já existe. Por favor, escolha outro.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="caduser.php" class="btn btn-secondary">Fechar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function(){
                $("#loginExistenteModal").modal();
            });
        </script>';
    exit();
}

// Insere o novo usuário no banco com a senha hash
$query = "INSERT INTO usuarios (usuario, senha, nivel, titulo, linque, username) VALUES ('$login', '$senhaHashed', '$nivel', '$titulo', '$linque', '$username')";
$insert = mysqli_query($connect, $query);

if ($insert) {
    echo '
        <div class="modal" tabindex="-1" role="dialog" id="cadastroSucessoModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastro Bem-sucedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Usuário cadastrado com sucesso!</p>
                    </div>
                    <div class="modal-footer">
                        <a href="caduser.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function(){
                $("#cadastroSucessoModal").modal();
            });
        </script>';
} else {
    echo '
        <div class="modal" tabindex="-1" role="dialog" id="cadastroErroModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Erro de Cadastro</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Não foi possível cadastrar esse usuário. Tente novamente mais tarde.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="caduser.php" class="btn btn-secondary">Fechar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function(){
                $("#cadastroErroModal").modal();
            });
        </script>';
}

// Fechar a conexão
mysqli_close($connect);
?>
</body>
</html>
