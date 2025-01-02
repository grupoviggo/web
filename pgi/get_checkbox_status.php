<?php
// Conectar ao banco de dados
require 'conexao_admpgi.php';

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para obter os status de todos os clientes, incluindo o campo BKO (quem marcou/desmarcou)
$query = "SELECT ID, is_selected, BKO FROM dados_tfp";
$result = $conn->query($query);

if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Armazenar os resultados em um array
$checkboxStatus = [];
while ($row = $result->fetch_assoc()) {
    $checkboxStatus[] = $row;
}

// Retornar os resultados como JSON
header('Content-Type: application/json');
echo json_encode($checkboxStatus);

$conn->close();
?>
