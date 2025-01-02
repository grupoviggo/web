<?php
include 'conexao_admpgi.php';

function carregarProdutos() {
    global $conn;

    $categorias = ['DADOS', 'VOZ', 'TV', 'ADICIONAL', 'CELULAR'];
    $produtos = [];

    foreach ($categorias as $categoria) {
        $query = "SELECT PRODUTO_NOME, PRODUTO_VALOR_GERENCIAL FROM produtos WHERE TIPO = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $categoria);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos[$categoria] = [];
        while ($row = $result->fetch_assoc()) {
            // Formatar o valor para 2 casas decimais
            $valor_gerencial = number_format($row['PRODUTO_VALOR_GERENCIAL'], 2, '.', '');

            $produtos[$categoria][] = [
                'nome' => htmlspecialchars($row['PRODUTO_NOME']),
                'valor' => $valor_gerencial,  // Utilizar o valor formatado
            ];
        }
    }

    return $produtos;
}
?>
