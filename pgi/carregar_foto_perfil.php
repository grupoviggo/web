<?php
// Função para obter a foto de perfil do usuário pelo ID
function obterFotoPerfilDoUsuarioPorId($usuario_id)
{
    $conn = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    if (!$conn) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    $query = "SELECT foto_perfil FROM usuarios_pgi WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && $dados = mysqli_fetch_assoc($result)) {
        return $dados['foto_perfil'];
    } else {
        return "../img/default.png"; // Caminho de uma imagem padrão, caso o usuário não tenha uma foto
    }

    // Fechar a conexão
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
