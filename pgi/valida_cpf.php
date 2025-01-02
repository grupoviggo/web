<?php
// Conexão com o banco de dados
include 'conexao_admpgi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = $_POST['cpf'] ?? '';
    $email = $_POST['email'] ?? '';

    // Verifica no banco de dados se o CPF existe
    $stmt = $conn->prepare("SELECT cpf, email FROM usuarios_pgi WHERE cpf = ?");
    $stmt->bind_param("s", $cpf); // Usando o CPF como uma string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao executar a consulta']);
        exit;
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifica se o e-mail corresponde ao CPF
        if ($user['email'] === $email) {
            echo json_encode(['status' => 'found']);
        } else {
            echo json_encode(['status' => 'email_mismatch']);
        }
    } else {
        echo json_encode(['status' => 'not_found']);
    }

    $stmt->close();
    $conn->close();
}

?>
