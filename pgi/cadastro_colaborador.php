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

require 'conexao_admpgi.php'; // Conexão geral que acessa todos os bancos

// Verifica a conexão
if (!$conn) {
    die("Erro na conexão com o banco de dados.");
}

// Dados do formulário
$NOME = $_POST["NOME"];
$CPF = $_POST["CPF"];
$DATA_NASCIMENTO = $_POST["DATA_NASCIMENTO"];
$DATA_ADMISSAO = $_POST["DATA_ADMISSAO"];
$EMAIL = $_POST["email"];
$departamento = $_POST["departamento"];
$cargo = $_POST["cargo"];
$BASE = $_POST["CONSULTOR_BASE_NOME"];
$TELEFONE = $_POST["telefone"];
$NIVEL = $_POST["nivel"];
$status = "ATIVO";
$senha = "mudar123";

// Hash da senha
$senhaHashed = password_hash($senha, PASSWORD_DEFAULT);

// Verifica se o CPF já existe no banco de dados do RH
$verifica = mysqli_query($conn, "SELECT * FROM dados_colaborador WHERE CPF = '$CPF'") or die("Erro ao verificar o CPF.");

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
                        <p>Esse colaborador já existe. Por favor, escolha outro.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="painelrh.php" class="btn btn-secondary">Fechar</a>
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

// Insere o novo usuário na tabela do RH
$queryRH = "INSERT INTO dados_colaborador (NOME, CPF, DT_NASCIMENTO, DT_ADMISSAO, cargo, STATUS_COLABORADOR, senha, CONSULTOR_BASE_NOME, departamento, email, nivel, telefone) 
            VALUES ('$NOME', '$CPF', '$DATA_NASCIMENTO', '$DATA_ADMISSAO', '$cargo', '$status', '$senhaHashed', '$BASE', '$departamento', '$EMAIL', '$NIVEL', '$TELEFONE')";
$insertRH = mysqli_query($conn, $queryRH);

if ($insertRH) {
    // Insere os dados na tabela Colaborador_hierarquia
    $queryHierarquia = "INSERT INTO Colaborador_hierarquia (CONSULTOR_NOME, CONSULTOR_CPF, CONSULTOR_TIPO, CONSULTOR_BASE_NOME) 
                        VALUES ('$NOME', '$CPF', '$cargo', '$BASE')";
    $insertHierarquia = mysqli_query($conn, $queryHierarquia);

    if ($insertHierarquia) {
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
                            <p>Colaborador cadastrado com sucesso no RH e na hierarquia!</p>
                        </div>
                        <div class="modal-footer">
                            <a href="painelrh.php" class="btn btn-primary">OK</a>
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
            <div class="modal" tabindex="-1" role="dialog" id="cadastroErroHierarquiaModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Erro de Cadastro</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Colaborador cadastrado no RH, mas houve um erro ao cadastrar na hierarquia.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="painelrh.php" class="btn btn-secondary">Fechar</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                $(document).ready(function(){
                    $("#cadastroErroHierarquiaModal").modal();
                });
            </script>';
    }
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
                        <p>Não foi possível cadastrar esse colaborador no RH. Tente novamente mais tarde.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="painelrh.php" class="btn btn-secondary">Fechar</a>
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

// Fecha a conexão
mysqli_close($conn);

?>

</body>
</html>
