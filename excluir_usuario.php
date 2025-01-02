<?php
// Configuração da conexão com o banco de dados
$hostname = '200.147.61.78';
$username = 'viggoadm2';
$password = 'Viggo2024@';
$database = 'nexus';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
}
session_start();


// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: adm");
    exit();
}

// Valida a existência do ID na URL
if (!isset($_GET['id'])) {
    echo "Erro: ID do usuário não foi fornecido.";
    exit();
}

$id = $_GET['id'];

// Verifica se o ID é válido antes de buscar os dados
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Erro: Usuário não encontrado.";
    exit();
}

// Processo de exclusão quando o botão de confirmação é clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_exclusao'])) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Redireciona após exclusão bem-sucedida
        header("Location: usuarios.php?excluido=true");
        exit();
    } else {
        echo "Erro ao excluir usuário.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Usuário</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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
<div class="container mt-4">
    <br>
    <h2>excluir cadastro do usuário</h2>
    <br>
    <hr>
    <p>Tem certeza de que deseja excluir o usuário abaixo ?
    <br>
    <p><strong class="label-destaque">E-MAIL:</strong> <strong><?php echo htmlspecialchars($user['usuario']); ?></strong></p>
    <br>
    <hr>
    <!-- Botão para abrir o modal -->
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmModal">Confirmar Exclusão</button>
    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja excluir o usuário <strong><?php echo htmlspecialchars($user['usuario']); ?></strong>?
            </div>
            <div class="modal-footer">
                <form method="post">
                    <button type="submit" name="confirmar_exclusao" class="btn btn-danger">Confirmar</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
