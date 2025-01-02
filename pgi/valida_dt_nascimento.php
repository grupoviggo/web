<?php
// Conexão com o banco de dados
include 'conexao_admpgi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados enviados via POST
    $cpf = $_POST['cpf'] ?? '';
    $dt_nascimento = $_POST['dt_nascimento'] ?? '';

    // Log para verificar os dados recebidos (apenas para depuração, remova em produção)
    error_log("CPF recebido: $cpf, Data de nascimento recebida: $dt_nascimento");

    // Validação básica
    if (empty($cpf) || empty($dt_nascimento)) {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
        exit;
    }

    // Remove as barras da data e valida formato dd/mm/yyyy
    $dt_nascimento_sem_barras = str_replace('/', '', $dt_nascimento);
    if (strlen($dt_nascimento_sem_barras) !== 8) {
        echo json_encode(['status' => 'error', 'message' => 'Formato de data inválido']);
        exit;
    }

    // Consulta no banco de dados
    $stmt = $conn->prepare("SELECT cpf, DT_NASCIMENTO FROM usuarios_pgi WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se houve erro na execução da consulta
    if ($result === false) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao executar a consulta no banco']);
        exit;
    }

    // Verifica se o CPF foi encontrado
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Recupera a data de nascimento armazenada no banco
        $dt_nascimento_banco = $user['DT_NASCIMENTO'];

        // Log para depuração (apenas para testes, remova em produção)
        error_log("Data recebida: $dt_nascimento, Data no banco: $dt_nascimento_banco");

        // Verifica se a data de nascimento corresponde
        if ($dt_nascimento_banco === $dt_nascimento) {
            echo json_encode(['status' => 'found']);
        } else {
            echo json_encode(['status' => 'dt_nascimento_mismatch']);
        }
    } else {
        echo json_encode(['status' => 'not_found']);
    }

    // Fecha a consulta e a conexão
    $stmt->close();
    $conn->close();
}
?>
