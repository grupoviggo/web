<?php
// Função para obter a senha alterada do usuário pelo ID
function obterSenhaAlteradaDoUsuarioPorId($usuario_id)
{
    $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    if (!$connect) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    $query = "SELECT senha_alterada FROM usuarios_pgi WHERE id = ?";
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
?>