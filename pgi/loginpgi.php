<?php
session_start();

if (isset($_SESSION['cpf'])) {
    // Se já estiver logado, redireciona para o menu
    header("Location: menu.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST["cpf"];
    $senha = $_POST["senha"];

    $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

    if (!$connect) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    $query = "SELECT id, cpf, senha, nivel, senha_alterada, nome, cargo, departamento FROM usuarios_pgi WHERE cpf = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $cpf);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && $dados = mysqli_fetch_assoc($result)) {
        // Use password_verify para verificar a senha
        if (password_verify($senha, $dados['senha'])) {
            $_SESSION['cpf'] = $cpf;
            $_SESSION['nivel'] = $dados['nivel']; // Armazena o nível de acesso na sessão
            $_SESSION['ID'] = $dados['id']; // Armazena o ID do usuário na sessão
            $_SESSION['nome'] = $dados['nome']; // Armazena o 'Nome' na sessão
            $_SESSION['cargo'] = $dados['cargo']; // Armazena o 'Cargo' na sessão
            $_SESSION['departamento'] = $dados['departamento']; // Armazena o 'Dept' na sessão

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
            // CPF e/ou senha incorretos
            echo "<script language='javascript' type='text/javascript'>
                  alert('CPF e/ou senha incorretos');window.location.href='login.php';
                  </script>";
            exit();
        }
    } else {
        // CPF não encontrado
        echo "<script language='javascript' type='text/javascript'>
              alert('CPF e/ou senha incorretos');window.location.href='login.php';
              </script>";
        exit();
    }

    // Fechar a conexão
    mysqli_stmt_close($stmt);
    mysqli_close($connect);
}
?>
