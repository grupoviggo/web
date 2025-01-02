<?php
// Conexão com o banco de dados
include 'conexao_admpgi.php';

header('Content-Type: application/json'); // Define o cabeçalho da resposta como JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = $_POST['cpf'] ?? '';

    // Valida se o CPF está vazio
    if (empty($cpf)) {
        echo json_encode(['status' => 'invalid', 'message' => 'CPF não pode estar vazio']);
        exit;
    }

    // Validação do formato do CPF
    function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf); // Remove caracteres não numéricos
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    if (!validarCPF($cpf)) {
        echo json_encode(['status' => 'invalid', 'message' => 'CPF inválido']);
        exit;
    }

    // Consulta no banco de dados
    $stmt = $conn->prepare("SELECT cpf FROM usuarios_pgi WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'found', 'message' => 'CPF encontrado']);
    } else {
        echo json_encode(['status' => 'not_found', 'message' => 'CPF não encontrado']);
    }

    $stmt->close();
    $conn->close();
}
?>