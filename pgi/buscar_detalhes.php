<?php
include 'conexao_admpgi.php';

if (isset($_GET['consultor_nome'])) {
    $nomeConsultor = $_GET['consultor_nome'];

    $query = "SELECT SUPERVISOR_NOME, 
                     COORDENADOR_NOME, 
                     GERENTE_BASE_NOME, 
                     GERENTE_TERRITORIO_NOME, 
                     DIRETOR_NOME, 
                     CONSULTOR_BASE_NOME
              FROM Colaborador_hierarquia 
              WHERE CONSULTOR_NOME = ? AND CONSULTOR_STATUS = 'ATIVO'";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nomeConsultor);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        header('Content-Type: application/json');
        $data = $result->fetch_assoc();
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'No data found']);
        }

    }
    exit;
}
?>