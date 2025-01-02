<?php
include 'conexao_admpgi.php';

if (isset($_GET['produtos'])) {
    $produtos = explode(',', $_GET['produtos']);
    $placeholders = rtrim(str_repeat('?,', count($produtos)), ',');
    $query = "SELECT SUM(PRODUTO_VALOR_GERENCIAL) AS total FROM produtos WHERE PRODUTO_NOME IN ($placeholders)";
    
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Erro na preparação da consulta: ' . $conn->$error);
    }

    $stmt->bind_param(str_repeat('s', count($produtos)), ...$produtos);
    if (!$stmt->execute()) {
        die('Erro na execução da consulta: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['total' => $row['total'] ?? 0]);
} else {
    echo json_encode(['error' => 'Parâmetro produtos não fornecido.']);
}
?>
