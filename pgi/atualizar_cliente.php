<?php
// Conexão com o banco de dados
require 'conexao_admpgi.php';

// Verifica se o botão "Atualizar Status de Faturas" foi clicado e se clientes foram selecionados
if (isset($_POST['clientes'])) {
    $clientesSelecionados = $_POST['clientes'];

    // Loop pelos IDs dos clientes selecionados para atualizar as faturas
    foreach ($clientesSelecionados as $id) {
        // Construindo a consulta SQL para cada cliente
        $updateFields = [];
        for ($i = 1; $i <= 4; $i++) {
            $campoFatura = "{$i}_FATURA";
            $valorFatura = isset($_POST["fatura{$i}_{$id}"]) ? $_POST["fatura{$i}_{$id}"] : 'Não Pago';
            $updateFields[] = "$campoFatura = '$valorFatura'";
        }
        
        $updateQuery = "UPDATE dados_tfp SET " . implode(", ", $updateFields) . " WHERE ID = $id";
        
        // Executa a atualização e verifica se ocorreu com sucesso
        if ($conn->query($updateQuery) === TRUE) {
            echo "Faturas do cliente ID $id atualizadas com sucesso.<br>";
        } else {
            echo "Erro ao atualizar o cliente ID $id: " . $conn->error . "<br>";
        }
    }
} else {
    echo "Nenhum cliente selecionado para atualização.";
}

// Fecha a conexão com o banco de dados
$conn->close();
?>