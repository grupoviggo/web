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
    require 'conexao_admpgi.php';

    // Verifica a conexão com o banco de dados
    if (!$conn) {
        die("Erro na conexão com o banco de dados.");
    }

    // Captura os dados do formulário com os nomes corretos e escapa para evitar SQL Injection
    $NOME = mysqli_real_escape_string($conn, $_POST["CONSULTOR_BASE_NOME"]);

    // Prepara a consulta SQL usando prepared statement
    $query = "INSERT INTO bases (CONSULTOR_BASE_NOME) VALUES (?)";
    $stmt = mysqli_prepare($conn, $query);

    // Verifica se a preparação foi bem-sucedida
    if ($stmt) {
        // Liga os parâmetros e executa a consulta
        mysqli_stmt_bind_param($stmt, "s", $NOME);
        $insert = mysqli_stmt_execute($stmt);

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
                            <p>Base cadastrada com sucesso!</p>
                        </div>
                        <div class="modal-footer">
                            <a href="Cadastro-base_rh.php" class="btn btn-success">Continuar</a>
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
                            <p>Não foi possível cadastrar essa base. Tente novamente mais tarde.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="Cadastro-base_rh.php" class="btn btn-secondary">Fechar</a>
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

        // Fecha a declaração
        mysqli_stmt_close($stmt);
    } else {
        echo "Erro na preparação da consulta SQL.";
    }

    // Fecha a conexão com o banco de dados
    mysqli_close($conn);
    ?>

</body>

</html>