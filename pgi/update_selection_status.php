<?php
// Conectar ao banco de dados
require 'conexao_admpgi.php';

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter os dados enviados via AJAX
$clienteId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$isSelected = isset($_POST['is_selected']) ? intval($_POST['is_selected']) : 0;
$bko = isset($_POST['bko']) ? $_POST['bko'] : '-';  // Nome do usuário logado ou vazio

// Verificar se os dados estão corretos
if ($clienteId > 0) {
    // Atualizar o valor de is_selected e BKO no banco de dados
    $sql = "UPDATE dados_tfp SET is_selected = ?, BKO = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);

    // Verificar se a consulta foi preparada corretamente
    if (!$stmt) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Vincular os parâmetros
    $stmt->bind_param("isi", $isSelected, $bko, $clienteId);

    // Executar a consulta
    if ($stmt->execute()) {
        echo "Atualização bem-sucedida!";
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }

    // Fechar a declaração
    $stmt->close();
} else {
    echo "Dados inválidos.";
}

$conn->close();
?>
