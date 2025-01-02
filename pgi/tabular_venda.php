<?php
session_start(); // Inicia a sessão
header('Content-Type: application/json'); // Configura o tipo de resposta como JSON

// Conexão com o banco de dados
require_once 'conexao_admpgi.php';

// Verificar se a conexão foi realizada
if (!isset($conn)) {
    echo json_encode(["status" => "error", "message" => "Falha na conexão com o banco de dados."]);
    exit();
}

// Verificar se o método é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os dados foram recebidos corretamente
    $codigo_venda = $_POST['codigo_venda'] ?? null;
    $status_venda = $_POST['status_venda'] ?? null;

    // Verifica se a sessão está ativa e se o nome do usuário (backoffice) foi passado
    if (!isset($_SESSION['nome'])) {
        echo json_encode(["status" => "error", "message" => "Usuário não autenticado."]);
        exit();
    }
    $backoffice = $_SESSION['nome'] ?? null; // Pega o nome do usuário logado


    // Verificar se todos os dados obrigatórios foram recebidos
    if (!$codigo_venda || !$status_venda || !$backoffice) {
        echo json_encode([
            "status" => "error", 
            "message" => "Dados incompletos. Verifique se todos os campos foram preenchidos."
        ]);
        exit();
    }

    try {
        // Atualiza o status e o nome do usuário (backoffice) na tabela vendas
        $sql = "UPDATE vendas SET status_venda = ?, backoffice = ? WHERE codigo_venda = ?";
        $stmt = $conn->prepare($sql);

        // Usa bind_param para evitar erros de parâmetros
        $stmt->bind_param("sss", $status_venda, $backoffice, $codigo_venda); // 'sss' indica que são strings

        // Executa a consulta
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Status atualizado com sucesso."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao atualizar o status."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Erro inesperado: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método inválido."]);
}
?>
