<?php
header("Content-Type: application/json");

// Inclui a conexão com o banco de dados
require 'conexao_admpgi.php';

// Verifica o que está chegando no POST
file_put_contents('log.txt', "Iniciando script\n", FILE_APPEND);
file_put_contents('log.txt', "Dados recebidos: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Recupera os dados enviados via POST
$codigo_venda = $_POST['codigo_venda'] ?? null;
$backoffice = $_POST['backoffice'] ?? null;
$nome_cliente = $_POST['nome_cliente'] ?? null;
$doc_cliente = $_POST['doc_cliente'] ?? null;
$oferta = $_POST['oferta'] ?? null;
$dadosPainelFixa = $_POST['dadosPainelFixa'] ?? null;
$dadosPainelMovel = $_POST['dadosPainelMovel'] ?? null;

// Concatena $dadosPainelFixa e $dadosPainelMovel
$produtos = $dadosPainelFixa . '; ' . $dadosPainelMovel;

// Adiciona logs para verificar o conteúdo das variáveis
file_put_contents('log.txt', "codigo_venda: $codigo_venda, backoffice: $backoffice, nome_cliente: $nome_cliente, doc_cliente: $doc_cliente, oferta: $oferta, produtos: $produtos\n", FILE_APPEND);

// Verifica se todos os campos obrigatórios estão preenchidos
if (!$codigo_venda || !$backoffice || !$nome_cliente || !$doc_cliente || !$oferta || !$produtos) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos.', 'received' => $_POST]);
    exit;
}

// Prepara a query para inserir os dados na tabela
$query = "INSERT INTO dados_provisorios (CODIGO_VENDA, BACKOFICCE, NOME_CLIENTE, DOC_CLIENTE, OFERTA, PRODUTOS) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    // Se a preparação da consulta falhar, registre o erro
    file_put_contents('log.txt', "Erro na preparação da consulta: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Erro na preparação da consulta.', 'received' => $_POST]);
    exit;
}

$stmt->bind_param("ssssss", $codigo_venda, $backoffice, $nome_cliente, $doc_cliente, $oferta, $produtos);

// Executa a query
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();



?>
