<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <title>Registro</title>
</head>
<body>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregar o autoload do PHPMailer (ajuste o caminho se necessário)
require './libs/PHPMailer-master/src/PHPMailer.php';
require './libs/PHPMailer-master/src/SMTP.php';
require './libs/PHPMailer-master/src/Exception.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = htmlspecialchars($_POST['nome']);
    $dataNascimento = htmlspecialchars($_POST['dataNascimento']);
    $cpf = htmlspecialchars($_POST['cpfRegister']);

    // Formatar a data para o formato dd/mm/yyyy
    $dataNascimentoFormatada = DateTime::createFromFormat('Y-m-d', $dataNascimento)->format('d/m/Y');


    if (!empty($nome) && !empty($dataNascimento) && !empty($cpf)) {
        // Configurações do e-mail
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtps.uhserver.com';  // Ajuste para o servidor SMTP do seu provedor (UOL Host ou outro)
            $mail->SMTPAuth = true;
            $mail->Username = 'naoresponda@viggonexus.online'; // Seu e-mail de login
            $mail->Password = 'Viggo@2025*';          // A senha do seu e-mail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;  // Ou 465, dependendo do servidor SMTP

            // Configuração do charset para evitar problemas de codificação
            $mail->CharSet = 'UTF-8';

            // Destinatários
            $mail->setFrom('naoresponda@viggonexus.online', 'Nexus PGI');
            $mail->addAddress('pablo.pontes@grupoviggo.com.br');  // Endereço de destino

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'NEXUS PGI - ERRO AO LOGAR';
            $mail->Body    = "

            <!DOCTYPE html>
            <html lang='pt-BR'>
            <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #FFFFFF; /* Cor de fundo ajustada */
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        padding: 20px;
                        background-color: #ffffff;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }
                    h3{
                    color: #ffffff;
                    }
                    .header {
                        background-color: #007BFF;
                        color: #ffffff;
                        padding: 10px;
                        text-align: center;
                        border-radius: 8px;
                    }
                    .content {
                        padding: 20px;
                        background-color: #EDF3FB;
                    }
                    .footer {
                        text-align: center;
                        padding: 10px;
                        font-size: 14px;
                        background-color: #EDF3FB;
                        color: #888888;
                    }
                    h2 {
                        color: #333;
                    }
                    p {
                        font-size: 16px;
                        color: #555;
                    }
                </style>
            </head>
            <body>
            <br>
                <div class='container'>
                <img src='https://viggonexus.online/img/nexuspgi_light.png' style='width: 65%;'>
                <br>
                <p>
                    <div class='header'>
                        <h3 style='color: #FFF;'>CONSULTOR COM PROBLEMA NO LOGIN</h3>
                    </div>
                    <div class='content'>
                        <p><strong>Nome:</strong> {$nome}</p>
                        <p><strong>Data de Nascimento:</strong> {$dataNascimentoFormatada}</p>
                        <p><strong>CPF:</strong> {$cpf}</p>
                        <br>
                        <p>Favor verificar os dados do colaborador acima, verifique se há bloqueios temporários, inativação e caso necessário reset a senha do usuário</p>
                    </div>
                    <div class='footer'>
                        <p style='color: red; font-weight: bold;'>Este é um e-mail automático, favor não responder!</p>
                    </div>
                </div>
            </body>
            </html>";


            // Envia o e-mail
            $mail->send();

            // Exibe o modal de sucesso
            $modalId = "successModal";
            $modalTitle = "Registro enviado com sucesso!";
            $modalBody = "Os dados foram enviados para o RH. Em breve entrarão em contato. Obrigado!";
        } catch (Exception $e) {
            $modalId = "errorModal";
            $modalTitle = "Erro ao enviar registro";
            $modalBody = "Ocorreu um erro ao enviar o registro. Tente novamente após 1 minuto!";
        }
    } else {
        $modalId = "errorModal";
        $modalTitle = "Campos incompletos";
        $modalBody = "Por favor, preencha todos os campos do formulário.";
    }
} else {
    $modalId = "errorModal";
    $modalTitle = "Método de envio inválido";
    $modalBody = "Use o formulário para enviar os dados.";
}
?>

<!-- Modal -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel"><?= $modalTitle ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= $modalBody ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Exibe o modal automaticamente
    var myModal = new bootstrap.Modal(document.getElementById('<?= $modalId ?>'));
    myModal.show();

    // Redireciona para login.php após o fechamento do modal
    var modalElement = document.getElementById('<?= $modalId ?>');
    modalElement.addEventListener('hidden.bs.modal', function () {
        window.location.href = 'login.php';
    });
</script>
</body>
</html>
