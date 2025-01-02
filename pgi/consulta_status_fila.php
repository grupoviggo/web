<?php
// Conexão com o banco de dados
include('conexao_admpgi.php');

$queryTotal = "SELECT COUNT(*) AS total FROM vendas WHERE status_venda NOT IN ('ATIVO', 'PENDENTE', 'CANCELADO')";
$resultTotal = mysqli_query($conn, $queryTotal);
$totalRow = mysqli_fetch_assoc($resultTotal);
$total = $totalRow['total'] ?? 0; // Total de registros (evitar null)


$queryContagem = "SELECT status_venda, COUNT(*) AS total 
                  FROM vendas 
                  WHERE status_venda NOT IN ('ATIVO', 'PENDENTE', 'CANCELADO') 
                  GROUP BY status_venda";
$resultContagem = mysqli_query($conn, $queryContagem);


// Configurações de cores para os status
$statusConfig = [
    'FINALIZADA' => ['cor' => '#198754'],
    'EM FILA' => ['cor' => '#69838d'],
    'REENTRADA' => ['cor' => '#d2cd88'],
    'AG. RETORNO' => ['cor' => '#F6C6AD'],
    'CANCELADO' => ['cor' => '#c63b3b'],
    'AUDITADA' => ['cor' => '#6f42c1'],
];

// Preparar os dados para exibição
$data = [];

// Adicionar os status encontrados no banco
while ($row = mysqli_fetch_assoc($resultContagem)) {
    $status = $row['status_venda'];
    $quantidade = (int)$row['total'];
    $percentual = ($total > 0) ? ($quantidade / $total) * 100 : 0;

    $data[$status] = [
        'status' => $status,
        'total' => $quantidade,
        'percentual' => $percentual,
        'cor' => $statusConfig[$status]['cor'] ?? '#ddd', // Cor padrão
    ];
}

// Adicionar status sem registros explícitos no banco
foreach ($statusConfig as $status => $config) {
    if (!isset($data[$status])) {
        $data[$status] = [
            'status' => $status,
            'total' => 0,
            'percentual' => 0,
            'cor' => $config['cor'],
        ];
    }
}

// Reindexar o array para evitar chaves personalizadas
$data = array_values($data);
?>
