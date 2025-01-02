<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID da base foi passado via GET
if (!isset($_GET['id_base']) || empty($_GET['id_base'])) {
    echo "ID da base não fornecido.";
    exit();
}

// Sanitiza o ID da base
$id_base = intval($_GET['id_base']);

// Conexão com o banco de dados
$connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$connect) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Atualiza o status da base para 'ATIVO'
$query = "UPDATE bases SET STATUS_BASE = 'ATIVO' WHERE id_base = ?";
$stmt = mysqli_prepare($connect, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_base);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($connect);
        header("Location: Bases.php");
        exit();
    } else {
        echo "Erro ao ativar a base: " . mysqli_error($connect);
    }
} else {
    echo "Erro na preparação da consulta: " . mysqli_error($connect);
}

// Fecha a conexão com o banco de dados
mysqli_close($connect);
?>
