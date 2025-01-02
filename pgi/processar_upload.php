<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processamento de Upload</title>
    <!-- Inclua o CSS do Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

require 'conexao_admpgi.php'; // Conexão com o banco de dados
require 'SimpleXLSX.php'; // Carregar a biblioteca SimpleXLSX

$message = "";
$error = false;

if (isset($_FILES['fileUpload'])) {
    $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
    $fileExtension = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);

    if ($fileExtension == 'xlsx') {
        if ($xlsx = SimpleXLSX::parse($fileTmpPath)) {
            foreach ($xlsx->rows() as $index => $row) {
                if ($index === 0) continue;

                $vendaId = $row[0];
                $ordemDeServico = $row[1];
                $dataInstalacao = substr($row[2], 0, 10);
                $documento = $row[3];
                $nomeRazaoSocial = $row[4];
                $valorGerencial = $row[5];
                $dataFatura = $row[6];

                $stmt = $conn->prepare("INSERT INTO dados_tfp (VENDA_ID, VENDA_ORDEM_DE_SERVICO, POSVENDA_DATA_INSTALACAO, CLIENTE_DOCUMENTO, CLIENTE_NOME_RAZAO_SOCIAL, PRODUTO_VALOR_GERENCIAL, VENDA_DATA_FATURA) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $vendaId, $ordemDeServico, $dataInstalacao, $documento, $nomeRazaoSocial, $valorGerencial, $dataFatura);

                if (!$stmt->execute()) {
                    $message .= "Erro ao inserir dados: " . $stmt->error . "<br>";
                    $error = true;
                    break;
                }
            }
            if (!$error) {
                $message = "Arquivo .xlsx processado e dados inseridos com sucesso!";
            }
        } else {
            $message = "Erro ao processar o arquivo: " . SimpleXLSX::parseError();
            $error = true;
        }
    } else {
        $message = "Formato de arquivo não suportado. Apenas arquivos .xlsx são permitidos.";
        $error = true;
    }
} else {
    $message = "Nenhum arquivo foi enviado.";
    $error = true;
}
?>

<!-- Modal do Bootstrap -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header <?php echo $error ? 'bg-danger' : 'bg-success'; ?>">
                <h5 class="modal-title text-white" id="resultModalLabel"><?php echo $error ? 'Erro' : 'Sucesso'; ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $message; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="closeButton">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Inclua o JS do Bootstrap e jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
// Exibe o modal automaticamente
$(document).ready(function() {
    $('#resultModal').modal('show');
    
    // Redireciona ao clicar em "Fechar"
    $('#closeButton').click(function() {
        window.location.href = "carga-database-tfp.php";
    });
});
</script>

</body>
</html>
