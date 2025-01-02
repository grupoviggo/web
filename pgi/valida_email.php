<?php
// Conexão com o banco de dados
include 'conexao_admpgi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // Valida o e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'invalid']);
        exit;
    }

    // Verifica no banco de dados
    $stmt = $conn->prepare("SELECT email FROM usuarios_pgi WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debug: Verifique se a execução chegou até aqui
    if ($result === false) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao executar a consulta']);
        exit;
    }

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'found']);
    } else {
        echo json_encode(['status' => 'not_found']);
    }

    $stmt->close();
    $conn->close();
}
?>
