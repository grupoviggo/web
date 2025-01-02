<?php
session_start();

if (isset($_SESSION['username'])) {
    // Se já estiver logado, redireciona para o menu
    header("Location: caduser_pgi.php");
    exit();
}

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $senha = $_POST["senha"];

    // Conectar ao banco de dados
    $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    if (!$connect) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    // Consulta atualizada para usar a tabela 'administracao'
    $query = "SELECT id, username, senha FROM administracao WHERE username = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && $dados = mysqli_fetch_assoc($result)) {
        // Use password_verify para verificar a senha
        if (password_verify($senha, $dados['senha'])) {
            // Armazena os dados do usuário na sessão
            $_SESSION['username'] = $username;
            $_SESSION['ID'] = $dados['id'];

            // Se o login for bem-sucedido, redireciona para a página caduser.php
            header("Location: caduser_pgi.php");
            exit();
        } else {
            echo "<script language='javascript' type='text/javascript'>
                  alert('Usuário e/ou senha incorretos');window.location.href='admpgi.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script language='javascript' type='text/javascript'>
              alert('Usuário e/ou senha incorretos');window.location.href='admpgi.php';
              </script>";
        exit();
    }
    // Fecha a conexão
    mysqli_stmt_close($stmt);
    mysqli_close($connect);
}
?>
