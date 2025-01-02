<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['username'])) {
    header("Location: adm");
    exit();
}

// Se o botão de sair foi clicado
if (isset($_POST['sairadm'])) {
    session_unset();
    session_destroy();
    header("Location: adm");
    exit();
}

// Conexão com o banco de dados
$conn = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$conn) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define a variável de busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Consulta para buscar os usuários com base na busca por CPF ou Nome
$query = "SELECT id, usuario, nivel, linque, titulo FROM usuarios";
if (!empty($busca)) {
    $query .= " WHERE usuario LIKE '%" . mysqli_real_escape_string($conn, $busca) . "%'";
}
$result = mysqli_query($conn, $query);

// Verifica se a busca retornou resultados
$temResultados = mysqli_num_rows($result) > 0;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Usuários</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/painelusuarios.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="painel">
            <img src="../img/nexusloginbd.png" width="auto" height="25px" alt="">
        </a>
        <div class="vertical-divider"></div>
        <div class="navbar-text text-light ml-3 small">
            <strong>SGU - SISTEMA DE GESTÃO AO BANCO DE DADOS</strong>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='caduser'; return false;">VOLTAR</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
<div class="container mt-5">
    <h2>
        Lista de Usuários Cadastrados
        <form method="GET" class="form-inline d-inline-block float-right">
            <input type="text" name="busca" class="form-control mr-2" placeholder="Buscar por e-mail" value="<?php echo htmlspecialchars($busca); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <button type="button" class="btn btn-secondary ml-2" onclick="window.location.href='usuarios.php'">Limpar Busca</button>
        </form>
    </h2>
    <br>
    <table class="table table-bordered">
          <thead class="head-light">
              <tr>
                  <th>Email</th>
                  <th>Nível</th>
                  <th>Lik Dash</th>
                  <th>Título Dash</th>
                  <th>Ações</th>
              </tr>
          </thead>
          <tbody>
              <?php
              if ($temResultados) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td class='no-right-border'>" . htmlspecialchars($row['usuario']) . "</td>";
                    echo "<td class='no-left-border'>" . htmlspecialchars($row['nivel']) . "</td>";
                    echo "<td class='no-left-border-middle'>" . htmlspecialchars($row['linque']) . "</td>";
                    echo "<td class='no-left-border'>" . htmlspecialchars($row['titulo']) . "</td>";
                    echo "<td class='action-buttons'>
                            <a href='editar_usuario.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Editar</a>
                            <form action='resetar_usuario' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <input type='hidden' name='usuario' value='" . $row['usuario'] . "'>
                                <button type='submit' class='btn btn-warning btn-sm'>Resetar Senha</button>
                            </form>
                            <a href='excluir_usuario.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Excluir</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Nenhum usuário encontrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>