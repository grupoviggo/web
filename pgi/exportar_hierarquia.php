<?php
// Incluir a conexão com o banco de dados
require 'conexao_admpgi.php';

try {
    // Definir o nome do arquivo para download
    $filename = 'hierarquia_atual.csv';

    // Enviar cabeçalhos para download
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Abrir o output em modo de escrita
    $output = fopen('php://output', 'w');

    if ($output === false) {
        throw new Exception('Não foi possível abrir o output para escrita.');
    }

    // Definir os títulos das colunas na ordem especificada
    $headers = [
        'CONSULTOR_ID', 'CONSULTOR_NOME', 'CONSULTOR_TIPO', 'CONSULTOR_CPF',
        'CONSULTOR_BASE_NOME', 'CONSULTOR_BASE_GRUPO', 'CONSULTOR_SETOR_TIPO',
        'SUPERVISOR_NOME', 'SUPERVISOR_CPF', 'COORDENADOR_NOME', 'COORDENADOR_CPF',
        'GERENTE_BASE_NOME', 'GERENTE_BASE_CPF', 'GERENTE_TERRITORIO_NOME',
        'GERENTE_TERRITORIO_CPF', 'DIRETOR_NOME', 'DIRETOR_CPF',
        'CONSULTOR_BACKOFFICE', 'CONSULTOR_CENTRO_CUSTO', 'CONSULTOR_STATUS'
    ];

    // Escrever os títulos no arquivo CSV
    fputcsv($output, $headers);

    // Consultar os dados do banco
    $query = "SELECT CONSULTOR_ID, CONSULTOR_NOME, CONSULTOR_TIPO, CONSULTOR_CPF, CONSULTOR_BASE_NOME, CONSULTOR_BASE_GRUPO, 
              CONSULTOR_SETOR_TIPO, SUPERVISOR_NOME, SUPERVISOR_CPF, COORDENADOR_NOME, COORDENADOR_CPF, 
              GERENTE_BASE_NOME, GERENTE_BASE_CPF, GERENTE_TERRITORIO_NOME, GERENTE_TERRITORIO_CPF, 
              DIRETOR_NOME, DIRETOR_CPF, CONSULTOR_BACKOFFICE, CONSULTOR_CENTRO_CUSTO, CONSULTOR_STATUS
              FROM Colaborador_hierarquia";
    $result = mysqli_query($conn, $query);

    if ($result === false) {
        throw new Exception('Erro na consulta ao banco de dados: ' . mysqli_error($conn));
    }

    // Preencher o arquivo CSV com os dados
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }

    // Fechar o output
    fclose($output);
    exit;
} catch (Exception $e) {
    echo 'Erro ao gerar o CSV: ' . $e->getMessage();
}
?>
