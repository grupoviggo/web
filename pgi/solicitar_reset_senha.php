<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Obtenha o ID do usuário da sessão
$user_id = $_SESSION['ID'];

// Consulta para buscar os dados do usuário logado
$sql = "SELECT nome, cpf, email FROM usuarios_pgi WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Verifica se os dados do usuário foram encontrados
if ($userData) {
    $nome = $userData['nome'];
    $cpf = $userData['cpf'];
    $email_usuario = $userData['email'];

    // Mensagem de alerta no e-mail
    $mensagem = "
        <html>
        <body>
            <p><strong>! ATENÇÃO !</strong> - O usuário abaixo fez uma solicitação de reset de senha, por favor aprove em até 24h.</p>
            <p><strong>Nome:</strong> $nome</p>
            <p><strong>CPF:</strong> $cpf</p>
            <p><strong>E-mail:</strong> $email_usuario</p>
            <p>
                <a href='https://viggonexus.online/pgi/aprovar.php?user_id=$user_id' style='padding: 10px 20px; background-color: green; color: white; text-decoration: none; font-weight: bold;'>APROVAR</a>
                &nbsp;&nbsp;
                <a href='https://viggonexus.online/pgi/negacao.php?user_id=$user_id' style='padding: 10px 20px; background-color: red; color: white; text-decoration: none; font-weight: bold;'>NEGAR</a>
            </p>
        </body>
        </html>
    ";

    // Incluir os arquivos do PHPMailer
    require './libs/PHPMailer-master/src/PHPMailer.php';
    require './libs/PHPMailer-master/src/SMTP.php';
    require './libs/PHPMailer-master/src/Exception.php';

    // Criar uma instância do PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
// Configurações do servidor SMTP
$mail->isSMTP();
$mail->Host = 'smtps.uhserver.com';  // Servidor SMTP da UOL
$mail->SMTPAuth = true;
$mail->Username = 'naoresponda@viggonexus.online';  // Seu e-mail UOL
$mail->Password = 'Viggo@2025*';  // Sua senha UOL
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;  // Usar SMTPS para a criptografia SSL/TLS
$mail->Port = 465;  // Porta correta para SMTP com SSL

// Configuração do remetente e destinatário
$mail->setFrom('no-reply@grupoviggo.com.br', 'NEXUS');
$mail->addAddress('pablo.pontes@grupoviggo.com.br');  // E-mail do destinatário
$mail->addReplyTo($email_usuario, $nome);  // E-mail de resposta
$mail->isHTML(true);
$mail->Subject = 'NEXUS - TROCA DE SENHA SOLICITADA';
$mail->Body = $mensagem;  // Corpo da mensagem HTML


        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'NEXUS - TROCA DE SENHA SOLICITADA';
        $mail->Body = $mensagem;

        // Enviar o e-mail
        $mail->send();

        // Redireciona se o e-mail for enviado com sucesso
        header("Location: configuracoes.php?reset_sucesso=1");
        exit();
    } catch (Exception $e) {
        // Se o envio falhar, exibe uma mensagem de erro
        echo "<script>alert('Erro ao enviar solicitação de reset de senha.'); window.location.href = 'configuracoes.php';</script>";
    }
} else {
    echo "<script>alert('Erro ao recuperar informações do usuário.'); window.location.href = 'configuracoes.php';</script>";
}

// Fecha a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
