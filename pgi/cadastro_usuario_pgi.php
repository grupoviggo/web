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
$login = $_POST["email"];
$senha = "mudar123";
$nivel = $_POST["nivel"];
$nome = $_POST["nome"];
$cpf = $_POST["cpf"];
$dt_nascimento = $_POST["dt_nascimento"];
$cargo = $_POST["cargo"];
$departamento = $_POST["departamento"];

// Hash da senha usando password_hash
$senhaHashed = password_hash($senha, PASSWORD_DEFAULT);

$connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$connect) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Verifica se o login já existe
$verifica = mysqli_query($connect, "SELECT * FROM usuarios_pgi WHERE email = '$login'") or die("Erro ao selecionar");

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
                        <a href="caduser_pgi.php" class="btn btn-secondary">Fechar</a>
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
$query = "INSERT INTO usuarios_pgi (email, nome, cpf, DT_NASCIMENTO, cargo, departamento, senha, nivel) VALUES ('$login', '$nome', '$cpf', '$dt_nascimento', '$cargo', '$departamento', '$senhaHashed', '$nivel')";
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
                        <a href="usuarios_pgi.php" class="btn btn-primary">OK</a>
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
                        <a href="caduser_pgi.php" class="btn btn-secondary">Fechar</a>
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
