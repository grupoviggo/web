<?php
require 'conexao_admpgi.php';

session_start();
$user_id = $_SESSION['ID'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    $diretorio = '../uploads/';
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
    $caminhoCompleto = $diretorio . $user_id . '.' . $extensao;

    // Verifica o erro no upload
    if ($_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao fazer upload do arquivo.']);
        exit;
    }

    // Verifica o tipo de arquivo
    $mime = mime_content_type($_FILES['foto_perfil']['tmp_name']);
    $mimes_permitidos = ['image/jpeg', 'image/png'];
    if (!in_array($mime, $mimes_permitidos)) {
        echo json_encode(['status' => 'error', 'message' => 'Apenas arquivos JPG, JPEG e PNG são permitidos.']);
        exit;
    }

    // Move o arquivo e atualiza o banco
    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminhoCompleto)) {
        $sql_update = "UPDATE usuarios_pgi SET foto_perfil = ? WHERE ID = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $caminhoCompleto, $user_id);

        if ($stmt_update->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Foto atualizada com sucesso.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a foto no banco de dados.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao mover o arquivo enviado.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nenhum arquivo foi enviado.']);
}
?>
