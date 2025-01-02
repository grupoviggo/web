<?php
// Configurações de conexão com o banco de dados
$servidor = "200.147.61.78"; // Endereço do servidor de banco de dados
$usuario = "viggoadm2"; // Usuário do banco de dados
$senha = "Viggo2024@"; // Senha do banco de dados
$banco = "nexus"; // Nome do banco de dados

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);
$conn->set_charset("utf8mb4");

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>
