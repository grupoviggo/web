<?php
include 'conexao_admpgi.php';

if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
    $query = "SELECT PRODUTO_NOME FROM produtos WHERE TIPO = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Erro na preparação da consulta: ' . $conn->error);
    }

    $stmt->bind_param('s', $tipo);
    if (!$stmt->execute()) {
        die('Erro na execução da consulta: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $produtos = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($produtos);
} else {
    echo json_encode(['error' => 'Parâmetro tipo não fornecido.']);
}
?>
