<?php
// Inclui a conexão com o banco de dados
require '../pgi/conexao_admpgi.php';

// Captura o valor da busca de forma segura
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query base
$sql = "SELECT BASE, NOME, ATIVO, VIVO_TOTAL, CANCELAMENTO, B2B, CATEGORIA, CUPONS FROM Ranking_campanha";

// Adiciona o filtro de busca, se necessário
if (!empty($search)) {
    $sql .= " WHERE NOME LIKE '%$search%' OR BASE LIKE '%$search%'";
}

// Executa a consulta
$result = $conn->query($sql);

// Verifica se há resultados
if ($result && $result->num_rows > 0) {
    // Retorna os dados como linhas de tabela
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['BASE']) . "</td>";
        echo "<td>" . htmlspecialchars($row['NOME']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ATIVO']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CANCELAMENTO']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VIVO_TOTAL']) . "</td>";
        echo "<td>" . htmlspecialchars($row['B2B']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CATEGORIA']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CUPONS']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>Nenhum dado encontrado</td></tr>";
}

// Fecha a conexão
$conn->close();
?>
