<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processamento de Upload</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

require 'conexao_admpgi.php'; // Conexão com o banco de dados
require 'SimpleXLSX.php';

$message = "";
$error = false;

// Truncate a tabela antes de inserir novos dados
$conn->query("TRUNCATE TABLE Colaborador_hierarquia");

if (isset($_FILES['fileUploadHierarquia'])) {
    $fileTmpPath = $_FILES['fileUploadHierarquia']['tmp_name'];
    $fileExtension = pathinfo($_FILES['fileUploadHierarquia']['name'], PATHINFO_EXTENSION);

    if ($fileExtension == 'xlsx' || $fileExtension == 'csv') {
        if ($xlsx = SimpleXLSX::parse($fileTmpPath)) {
            foreach ($xlsx->rows() as $index => $row) {
                if ($index === 0) continue; // Ignorar cabeçalho

                // Mapeamento das colunas
                $CONSULTOR_NOME = strtoupper($row[0]);
                $CONSULTOR_TIPO = strtoupper($row[1]);
                $CONSULTOR_CPF = $row[2];
                $CONSULTOR_BASE_NOME = strtoupper($row[3]);
                $CONSULTOR_BASE_GRUPO = strtoupper($row[4]);
                $CONSULTOR_SETOR_TIPO = strtoupper($row[5]);
                $SUPERVISOR_NOME = strtoupper($row[6]);
                $SUPERVISOR_CPF = $row[7];
                $COORDENADOR_NOME = strtoupper($row[8]);
                $COORDENADOR_CPF = $row[9];
                $GERENTE_BASE_NOME = strtoupper($row[10]);
                $GERENTE_BASE_CPF = $row[11];
                $GERENTE_TERRITORIO_NOME = strtoupper($row[12]);
                $GERENTE_TERRITORIO_CPF = $row[13];
                $DIRETOR_NOME = strtoupper($row[14]);
                $DIRETOR_CPF = $row[15];
                $CONSULTOR_BACKOFFICE = strtoupper($row[16]);
                $CONSULTOR_CENTRO_CUSTO = $row[17];

                // Adicione um log para verificar o valor do CPF
                error_log("CPF da linha " . ($index + 1) . ": " . $CONSULTOR_CPF);

                // Verifica e formata CPF com pontos e traço
                if (preg_match('/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', $CONSULTOR_CPF)) {
                    // CPF está no formato correto
                } else {
                    $message .= "Erro: CPF inválido na linha " . ($index + 1) . ". Deve estar no formato XXX.XXX.XXX-XX.<br>";
                    $error = true;
                    break;
                }

                // Ajuste a instrução SQL para os novos campos
                $stmt = $conn->prepare("INSERT INTO Colaborador_hierarquia (CONSULTOR_NOME, CONSULTOR_TIPO, CONSULTOR_CPF, CONSULTOR_BASE_NOME, CONSULTOR_BASE_GRUPO, CONSULTOR_SETOR_TIPO, SUPERVISOR_NOME, SUPERVISOR_CPF, COORDENADOR_NOME, COORDENADOR_CPF, GERENTE_BASE_NOME, GERENTE_BASE_CPF, GERENTE_TERRITORIO_NOME, GERENTE_TERRITORIO_CPF, DIRETOR_NOME, DIRETOR_CPF, CONSULTOR_BACKOFFICE, CONSULTOR_CENTRO_CUSTO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssssssssss", $CONSULTOR_NOME, $CONSULTOR_TIPO, $CONSULTOR_CPF, $CONSULTOR_BASE_NOME, $CONSULTOR_BASE_GRUPO, $CONSULTOR_SETOR_TIPO, $SUPERVISOR_NOME, $SUPERVISOR_CPF, $COORDENADOR_NOME, $COORDENADOR_CPF, $GERENTE_BASE_NOME, $GERENTE_BASE_CPF, $GERENTE_TERRITORIO_NOME, $GERENTE_TERRITORIO_CPF, $DIRETOR_NOME, $DIRETOR_CPF, $CONSULTOR_BACKOFFICE, $CONSULTOR_CENTRO_CUSTO);

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
        $message = "Formato de arquivo não suportado. Apenas arquivos .xlsx ou .csv são permitidos.";
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#resultModal').modal('show');

        $('#closeButton').click(function() {
            window.location.href = "Painel_hierarquia.php";
        });
    });
</script>

</body>
</html>
