<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    // Verifica se a conexão foi estabelecida com sucesso
    if (!$connect) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    // Recebe os valores dos inputs da página alterar_senha.php
    $novaSenha = isset($_POST["senha"]) ? $_POST["senha"] : '';
    $senhaAlterada = isset($_POST["senha_alterada"]) ? $_POST["senha_alterada"] : 0;
    $idUsuario = isset($_POST["ID"]) ? $_POST["ID"] : '';
    
    // Validações de segurança aqui
    if (empty($novaSenha) || empty($idUsuario)) {
        echo "Dados inválidos.";
        exit();
    }
    

    // Hash da nova senha usando password_hash
    $novaSenhaHashed = password_hash($novaSenha, PASSWORD_DEFAULT);

    // Construa a consulta SQL
    $sql = "UPDATE usuarios_pgi SET senha = '$novaSenhaHashed', senha_alterada = '$senhaAlterada' WHERE ID = '$idUsuario'";

    // Executa a consulta SQL
    if (mysqli_query($connect, $sql)) {
        // Encerra a sessão do usuário
        session_unset();
        session_destroy();
        
        // Fecha a conexão com o banco de dados
        mysqli_close($connect);
        
        // Define uma variável para mostrar a mensagem modal
        $exibirModal = true;
    } else {
        // Exibe mensagem de erro
        echo "Erro ao atualizar a senha: " . mysqli_error($connect);
    }
} else {
    echo "Método de requisição inválido.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SENHA ALTERADA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/login_principal.css">
    <style>
        /* Estilização para a camada escura de fundo */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 100, 0.5); /* Cor preta com 50% de opacidade */
            z-index: 1040; /* Z-index maior que o modal */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        
    </div>
</div>
<!-- Camada escura de fundo -->
<div class="overlay"></div>
<!-- Modal de Senha Alterada -->
<div class="modal fade" id="senhaAlteradaModal" tabindex="-1" aria-labelledby="senhaAlteradaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="senhaAlteradaModalLabel">Senha Alterada com Sucesso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Sua senha foi alterada com sucesso. Por favor, faça o login novamente com sua nova senha.
            </div>
            <div class="modal-footer">
                <a href="login.php" class="btn btn-primary">OK</a>
            </div>
        </div>
    </div>
</div>
<!-- Script para mostrar a mensagem modal -->
<?php if (isset($exibirModal) && $exibirModal): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('senhaAlteradaModal'));
            modal.show();
        });
    </script>
<?php endif; ?>
</body>
</html>
