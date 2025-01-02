<?php
session_start();
include 'conexao_admpgi.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: admpgi");
    exit();
}

// Valida a existência do ID na URL
if (!isset($_GET['id'])) {
    echo "Erro: ID do usuário não foi fornecido.";
    exit();
}

$id = $_GET['id'];

// Verifica se o ID é válido antes de buscar os dados
$stmt = $conn->prepare("SELECT * FROM usuarios_pgi WHERE id = ?");
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
    $stmt = $conn->prepare("DELETE FROM usuarios_pgi WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Redireciona após exclusão bem-sucedida
        header("Location: usuarios_pgi.php?excluido=true");
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
    <br>
    <h2>excluir cadastro do usuário</h2>
    <br>
    <hr>
    <p>Tem certeza de que deseja excluir o usuário abaixo ?
    <br>
    <p><strong class="label-destaque">NOME:</strong> <strong><?php echo htmlspecialchars($user['nome']); ?></strong></p>
    <p><strong class="label-destaque">CPF:</strong> <strong><?php echo htmlspecialchars($user['cpf']); ?></strong></p>
    <br>
    <hr>
    <!-- Botão para abrir o modal -->
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmModal">Confirmar Exclusão</button>
    <a href="usuarios_pgi.php" class="btn btn-secondary">Cancelar</a>
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
                Tem certeza de que deseja excluir o usuário <strong><?php echo htmlspecialchars($user['nome']); ?></strong>?
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
