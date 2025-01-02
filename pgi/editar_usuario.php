<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['username'])) {
    header("Location: admpgi");
    exit();
}

// Se o botão de sair foi clicado
if (isset($_POST['sairadmpgi'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: admpgi");
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
        <!-- Barra de navegação Bootstrap -->
        <nav class="navbar navbar-expand-lg navbar-dark">
        <!-- Adiciona a imagem ao lado esquerdo -->
        <a class="navbar-brand" href="painel">
            <img src="../img/pgiloginbd.png" width="auto" height="25px" alt="">
        </a>
                        <!-- Linha vertical pontilhada -->
                        <div class="vertical-divider"></div>
        <!-- Nome do usuário e tempo logado -->
        <div class="navbar-text text-light ml-3 small"> <!-- Adiciona a classe small para diminuir o texto -->
            <spam class="label-text"><strong>SGU - SISTEMA DE GESTÃO AO BANCO DE DADOS</strong></span>
        </div>
        <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='usuarios_pgi'; return false;">VOLTAR</button>
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
      $query = "SELECT * FROM usuarios_pgi WHERE id = $id";
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
      $email = mysqli_real_escape_string($connect, $_POST['email']);
      $nome = mysqli_real_escape_string($connect, $_POST['nome']);
      $cpf = mysqli_real_escape_string($connect, $_POST['cpf']);
      $cargo = mysqli_real_escape_string($connect, $_POST['cargo']);
      $departamento = mysqli_real_escape_string($connect, $_POST['departamento']);
      $nivel = mysqli_real_escape_string($connect, $_POST['nivel']);

      // Atualiza os dados do usuário no banco de dados
      $update_query = "
          UPDATE usuarios_pgi
          SET email = '$email', nome = '$nome', cpf = '$cpf', cargo = '$cargo', departamento = '$departamento', nivel = '$nivel'
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
      <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" maxlength="150" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase()" required>
    </div>
    <div class="form-group">
      <label for="nome">Nome:</label>
      <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" maxlength="50" style="text-transform: uppercase;" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').toUpperCase()" required>
    </div>
    <div class="form-group">
      <label for="cpf">CPF:</label>
      <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo htmlspecialchars($user['cpf']); ?>" maxlength="14" oninput="mascaraCPF(this)" required>
    </div>
    <!-- Campo Cargo com Select -->
    <div class="form-group">
        <label for="cargo">CARGO:</label>
        <select id="cargo" class="form-control" name="cargo" onchange="atualizarDepartamento()">
            <option value="GERENTE" <?php echo $user['cargo'] == 'GERENTE' ? 'selected' : ''; ?>>GERENTE</option>
            <option value="COORDENADOR" <?php echo $user['cargo'] == 'COORDENADOR' ? 'selected' : ''; ?>>COORDENADOR</option>
            <option value="SUPERVISOR" <?php echo $user['cargo'] == 'SUPERVISOR' ? 'selected' : ''; ?>>SUPERVISOR</option>
            <option value="VENDEDOR" <?php echo $user['cargo'] == 'VENDEDOR' ? 'selected' : ''; ?>>VENDEDOR</option>
            <option value="ADMINISTRATIVO" <?php echo $user['cargo'] == 'ADMINISTRATIVO' ? 'selected' : ''; ?>>ADMINISTRATIVO</option>
        </select>
    </div>

    <!-- Campo Departamento com Select Dinâmico -->
    <div class="form-group">
        <label for="departamento">DEPARTAMENTO:</label>
        <select id="departamento" class="form-control" name="departamento">
            <!-- Opções serão preenchidas pelo JavaScript -->
        </select>
    </div>

    <div class="form-group">
        <label for="nivel">Nível:</label>
        <input type="number" class="form-control" id="nivel" name="nivel" value="<?php echo htmlspecialchars($user['nivel']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    <a href="usuarios_pgi" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
    <script>
    function atualizarDepartamento() {
        var cargo = document.getElementById("cargo").value;
        var departamento = document.getElementById("departamento");
        departamento.innerHTML = "";

        if (cargo === "GERENTE" || cargo === "COORDENADOR" || cargo === "SUPERVISOR" || cargo === "VENDEDOR") {
            var opcao = document.createElement("option");
            opcao.value = "COMERCIAL";
            opcao.text = "COMERCIAL";
            departamento.appendChild(opcao);
        } else if (cargo === "ADMINISTRATIVO") {
            var opcoes = [
                { value: "COMERCIAL", text: "COMERCIAL" },
                { value: "BACKOFFICE", text: "BACKOFFICE" },
                { value: "PLANEJAMENTO/T.I", text: "PLANEJAMENTO OU T.I" },
                { value: "FINANCEIRO", text: "FINANCEIRO" },
                { value: "RH OU DP", text: "RH OU DP" }
            ];
            opcoes.forEach(function (opcao) {
                var elemento = document.createElement("option");
                elemento.value = opcao.value;
                elemento.text = opcao.text;
                departamento.appendChild(elemento);
            });
        }
    }

    // Função para carregar o departamento inicial com base no valor atual do usuário
    function carregarDepartamentoInicial() {
        atualizarDepartamento(); // Chama a função para preencher as opções
        var departamentoAtual = "<?php echo $user['departamento']; ?>";
        var departamentoSelect = document.getElementById("departamento");

        for (var i = 0; i < departamentoSelect.options.length; i++) {
            if (departamentoSelect.options[i].value === departamentoAtual) {
                departamentoSelect.options[i].selected = true;
                break;
            }
        }
    }

    // Executa a função ao carregar a página
    window.onload = carregarDepartamentoInicial;
</script>
    <script src="../js/mascara_cpf.js"></script>
</body>
</html>
