<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['username'])) {
    header("Location: adm");
    exit();
}

// Se o botão de sair foi clicado
if (isset($_POST['sairadm'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: adm");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário</title>
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
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='usuarios'; return false;">VOLTAR</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
<div class="container mt-4">
  <h2>editar dados do usuário</h2>
  <hr>
  <?php
  // Conexão com o banco de dados
  $connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

  if (!$connect) {
      die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
  }

  // Verifica se o ID foi passado na URL
  if (isset($_GET['id'])) {
      $id = $_GET['id'];

      // Consulta para buscar os dados do usuário específico
      $query = "SELECT * FROM usuarios WHERE id = $id";
      $result = mysqli_query($connect, $query);

      if (mysqli_num_rows($result) == 1) {
          $user = mysqli_fetch_assoc($result);
      } else {
          echo "<p class='alert alert-danger'>Usuário não encontrado.</p>";
          exit;
      }
  } else {
      echo "<p class='alert alert-danger'>ID do usuário não foi especificado.</p>";
      exit;
  }

  // Processa o formulário quando enviado
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $usuario = mysqli_real_escape_string($connect, $_POST['usuario']);
      $linque = mysqli_real_escape_string($connect, $_POST['linque']);
      $titulo = mysqli_real_escape_string($connect, $_POST['titulo']);
      $nivel = mysqli_real_escape_string($connect, $_POST['nivel']);

      // Atualiza os dados do usuário no banco de dados
      $update_query = "
          UPDATE usuarios
          SET usuario = '$usuario',  linque = '$linque', titulo = '$titulo', nivel = '$nivel'
          WHERE id = $id
      ";

      if (mysqli_query($connect, $update_query)) {
          echo "<p class='alert alert-success'>Usuário atualizado com sucesso!</p>";
      } else {
          echo "<p class='alert alert-danger'>Erro ao atualizar usuário: " . mysqli_error($connect) . "</p>";
      }
  }

  // Fecha a conexão com o banco de dados
  mysqli_close($connect);
  ?>

  <!-- Formulário de edição -->
  <form method="post">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($user['usuario']); ?>" maxlength="150" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase()" required>
    </div>
    <div class="form-group">
      <label for="nome">Link Dash:</label>
      <input type="text" class="form-control" id="linque" name="linque" value="<?php echo htmlspecialchars($user['linque']); ?>" maxlength="100" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase()" required>
    </div>
    <div class="form-group">
      <label for="cpf">Título Dash:</label>
      <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($user['titulo']); ?>" maxlength="14" style="text-transform: Uppercase;" oninput="this.value = this.value.toUpperCase()" required>
    </div>
    <div class="form-group">
        <label for="nivel">Nível:</label>
        <input type="number" class="form-control" id="nivel" name="nivel" value="<?php echo htmlspecialchars($user['nivel']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    <a href="usuarios" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
