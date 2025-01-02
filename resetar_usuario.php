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

// Verifica se o ID do usuário foi enviado e o exibe no modal de confirmação
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $userId = intval($_POST['id']);
    $userUsuario = $_POST['usuario'];
} else {
    echo "<script>alert('ID do usuário não foi fornecido.'); window.location.href = 'usuarios.php';</script>";
    exit;
}

// Variável para controlar a mensagem de sucesso
$mensagem = "";

if (isset($_POST['confirm_reset']) && $_POST['confirm_reset'] == 'yes') {
    $novaSenha = 'mudar123';
    $senhaHashed = password_hash($novaSenha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET senha = ?, senha_alterada = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $senhaHashed, $userId);

    if ($stmt->execute()) {
        $mensagem = "Senha resetada com sucesso!";
    } else {
        $mensagem = "Erro ao resetar a senha.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Reset de Senha</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #013565">
<div class="modal show" tabindex="-1" style="display: block; background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Reset de Senha</h5>
            </div>
            <div class="modal-body">
                <?php if (empty($mensagem)): ?>
                    <p>Deseja realmente resetar a senha do usuário: <strong><?php echo htmlspecialchars($userUsuario); ?></strong>?</p>
                <?php else: ?>
                    <p class="text-success"><?php echo htmlspecialchars($mensagem); ?></p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?php if (empty($mensagem)): ?>
                    <form method="POST" action="resetar_usuario">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($userId); ?>">
                        <input type="hidden" name="confirm_reset" value="yes">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='usuarios.php'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">SIM</button>
                    </form>
                <?php else: ?>
                    <button type="button" class="btn btn-success" onclick="window.location.href='usuarios.php'">OK</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Se a mensagem de sucesso estiver presente, fecha o modal após 2 segundos
    <?php if (!empty($mensagem)): ?>
        setTimeout(function() {
            window.location.href = 'usuarios.php';
        }, 2000);
    <?php endif; ?>
</script>
</body>
</html>