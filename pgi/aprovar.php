<?php
// Pega o ID do usuário
$user_id = $_GET['user_id'];

// Conexão com o banco
$conn = new mysqli("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

// Consulta para pegar os dados do usuário
$sql = "SELECT nome, cpf, email FROM usuarios_pgi WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if ($userData) {
    $nome = $userData['nome'];
    $cpf = $userData['cpf'];
    $email_usuario = $userData['email'];

    // Envia o e-mail de aprovação
    $para = "pablo.pontes@grupoviggo.com.br";
    $assunto = "NEXUS - RESET SENHA APROVADO!";
    $mensagem = "
        <html>
        <body>
            <p><strong>Atenção!</strong> O gestor aprovou o reset de senha do usuário abaixo:</p>
            <p><strong>Nome:</strong> $nome</p>
            <p><strong>CPF:</strong> $cpf</p>
            <p><strong>E-mail:</strong> $email_usuario</p>
        </body>
        </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@grupoviggo.com.br\r\n";
    $headers .= "X-Priority: 1 (Highest)\r\n";  // Marcar como alta prioridade
    $headers .= "X-MSMail-Priority: High\r\n";  // Para clientes de e-mail Microsoft
    $headers .= "Importance: High\r\n";  // Marcar como importante para outros clientes de e-mail

    if (mail($para, $assunto, $mensagem, $headers)) {
        echo "Reset de senha aprovado com sucesso.";
    } else {
        echo "Erro ao enviar o e-mail de aprovação.";
    }
}

$stmt->close();
$conn->close();
?>
