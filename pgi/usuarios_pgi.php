<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['username'])) {
    header("Location: admpgi");
    exit();
}

// Se o botão de sair foi clicado
if (isset($_POST['sairadmpgi'])) {
    session_unset();
    session_destroy();
    header("Location: admpgi");
    exit();
}

// Conexão com o banco de dados
$connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$connect) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define a variável de busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Consulta para buscar os usuários com base na busca por CPF ou Nome
$query = "SELECT id, email, nome, cpf, cargo, departamento, nivel FROM usuarios_pgi";
if (!empty($busca)) {
    $query .= " WHERE cpf LIKE '%" . mysqli_real_escape_string($connect, $busca) . "%' 
                OR nome LIKE '%" . mysqli_real_escape_string($connect, $busca) . "%'";
}
$result = mysqli_query($connect, $query);

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
            <img src="../img/pgiloginbd.png" width="auto" height="25px" alt="">
        </a>
        <div class="vertical-divider"></div>
        <div class="navbar-text text-light ml-3 small">
            <strong>SGU - SISTEMA DE GESTÃO AO BANCO DE DADOS</strong>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='caduser_pgi'; return false;">VOLTAR</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
<div class="container mt-5">
    <h2>
        Lista de Usuários Cadastrados
        <form method="GET" class="form-inline d-inline-block float-right">
            <input type="text" name="busca" class="form-control mr-2" placeholder="Buscar por CPF ou Nome" value="<?php echo htmlspecialchars($busca); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <button type="button" class="btn btn-secondary ml-2" onclick="window.location.href='usuarios_pgi.php'">Limpar Busca</button>
        </form>
    </h2>
    <br>
    <table class="table table-bordered">
        <thead class="head-light">
            <tr>
                <th>Email</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Cargo</th>
                <th>Departamento</th>
                <th>Nível</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($temResultados) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td class='no-right-border'>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td class='no-left-border'>" . htmlspecialchars($row['nome']) . "</td>";
                    echo "<td class='no-left-border-middle'>" . htmlspecialchars($row['cpf']) . "</td>";
                    echo "<td class='no-left-border-middle'>" . htmlspecialchars($row['cargo']) . "</td>";
                    echo "<td class='no-left-border-middle'>" . htmlspecialchars($row['departamento']) . "</td>";
                    echo "<td class='no-left-border'>" . htmlspecialchars($row['nivel']) . "</td>";
                    echo "<td class='action-buttons'>
                            <a href='editar_usuario.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Editar</a>
                            <a href='excluir_usuario.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Excluir</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>Nenhum usuário encontrado.</td></tr>";
            }
            // Fecha a conexão com o banco de dados
            mysqli_close($connect);
            ?>
        </tbody>
    </table>
</div>

<!-- Modal de Dados Não Encontrados -->
<div class="modal fade" id="noDataModal" tabindex="-1" aria-labelledby="noDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noDataModalLabel">Atenção</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                DADOS NÃO ENCONTRADO.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    // Exibe o modal de dados não encontrados se não houver resultados na busca
    <?php if (!$temResultados && !empty($busca)): ?>
        $(document).ready(function() {
            $('#noDataModal').modal('show');
        });
    <?php endif; ?>
</script>

</body>
</html>
