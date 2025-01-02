<?php
session_start();

if (isset($_SESSION['usuario'])) {
    // Se já estiver logado, redireciona para o menu
    header("Location: menu.php");
    exit();
}

if (isset($_POST["login"])) {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    if (!$connect) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    $query = "SELECT id, usuario, senha, nivel, senha_alterada, linque, titulo, username FROM usuarios WHERE usuario = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && $dados = mysqli_fetch_assoc($result)) {
        // Use password_verify para verificar a senha
        if (password_verify($senha, $dados['senha'])) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['nivel'] = $dados['nivel']; // Armazena o nível de acesso na sessão
            $_SESSION['ID'] = $dados['id']; // Armazena o ID do usuário na sessão
            $_SESSION['linque'] = $dados['linque']; // Armazena o 'linque' na sessão
            $_SESSION['titulo'] = $dados['titulo']; // Armazena o 'titulo' na sessão
            $_SESSION['username'] = $dados['username']; // Armazena o 'Nome do usuário' na sessão
            
            if ($dados['senha_alterada'] == 0) {
                // Redireciona o usuário para a página de alteração de senha
                header("Location: alterar_senha.php");
                exit();
            } else {
                // Senha já foi alterada, redireciona para outra página
                header("Location: entrando.php");
                exit();
            }
        } else {
            echo "<script language='javascript' type='text/javascript'>
                  alert('Usuário e/ou senha incorretos');window.location.href='index.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script language='javascript' type='text/javascript'>
              alert('Usuário e/ou senha incorretos');window.location.href='index.php';
              </script>";
        exit();
    }
    // Fechar a conexão
    mysqli_stmt_close($stmt);
    mysqli_close($connect);
}
?>