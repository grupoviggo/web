<?php
// Conexão com o banco de dados
include 'conexao_admpgi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    $cpf = $_POST['cpf'] ?? '';

    // Verifica se a senha ou o CPF foram enviados
    if (empty($senha) || empty($cpf)) {
        echo json_encode(['status' => 'invalid', 'message' => 'CPF ou senha não fornecidos']);
        exit;
    }

    // Verifica no banco de dados
    $stmt = $conn->prepare("SELECT senha FROM usuarios_pgi WHERE cpf = ?"); // Verifica pelo CPF
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashedPassword = $user['senha']; // Senha criptografada no banco de dados

        // Verifica se a senha fornecida corresponde ao hash
        if (password_verify($senha, $hashedPassword)) {
            echo json_encode(['status' => 'valid']);
        } else {
            echo json_encode(['status' => 'invalid', 'message' => 'Senha incorreta']);
        }
    } else {
        echo json_encode(['status' => 'invalid', 'message' => 'CPF não encontrado']);
    }

    $stmt->close();
    $conn->close();
}
?>
