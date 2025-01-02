<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_venda = $_POST['codigo_venda'] ?? '';
    $backoffice = $_POST['backoffice'] ?? '';
    $nomeCompleto = $_POST['nomeCompleto'] ?? '';
    $documento = $_POST['documento'] ?? '';
    $planoBaseNome = $_POST['planoBaseNome'] ?? '';

    // Verifique se os campos obrigatórios não estão vazios
    if (!$codigo_venda || !$backoffice || !$nomeCompleto || !$documento || !$planoBaseNome) {
        header("Location: emissao_10.php");
        exit;
    }

    // Conectar ao banco de dados
    include 'conexao_admpgi.php';

    // Consulta de inserção
    $query = "INSERT INTO dados_provisorios (CODIGO_VENDA, BACKOFFICE, NOME_CLIENTE, DOC_CLIENTE, OFERTA)
              VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssss", $codigo_venda, $backoffice, $nomeCompleto, $documento, $planoBaseNome);
        $stmt->execute();
        $stmt->close();
    }

    // Fechar conexão
    $conn->close();

    // Redirecionar para emissao_10.php
    header("Location: emissao_10.php");
    exit;
}
?>
