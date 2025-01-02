<?php
// Conexão com o banco
$host = '200.147.61.78';
$dbname = 'nexus';
$user = 'viggoadm2';
$pass = 'Viggo2024@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query para pegar os dados
    $sql = "SELECT COORDENADOR_NOME, SUPERVISOR_NOME, CONSULTOR_NOME FROM Colaborador_hierarquia";
    $stmt = $pdo->query($sql);

    // Estrutura hierárquica
    $hierarquia = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $coordenador = $row['COORDENADOR_NOME'];
        $supervisor = $row['SUPERVISOR_NOME'];
        $consultor = $row['CONSULTOR_NOME'];

        if (!isset($hierarquia[$coordenador])) {
            $hierarquia[$coordenador] = [];
        }
        if (!isset($hierarquia[$coordenador][$supervisor])) {
            $hierarquia[$coordenador][$supervisor] = [];
        }
        $hierarquia[$coordenador][$supervisor][] = $consultor;
    }

    // Retorna JSON
    header('Content-Type: application/json');
    echo json_encode($hierarquia);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
