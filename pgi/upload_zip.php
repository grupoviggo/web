<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao_admpgi.php'; // Conexão com o banco de dados

$message = "";
$error = false;

if (isset($_FILES['fileUpload'])) {
    $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
    $fileExtension = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);

    if ($fileExtension == 'zip') {
        $zip = new ZipArchive();
        if ($zip->open($fileTmpPath)) {
            $csvFileContent = null;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileName = $zip->getNameIndex($i);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                if ($fileExtension === 'csv') {
                    $csvFileContent = $zip->getFromIndex($i);
                    break;
                }
            }

            $zip->close();

            if ($csvFileContent) {
                $rows = explode("\n", $csvFileContent);
                $header = str_getcsv(array_shift($rows));
                $expectedHeader = ['CODIGO_PRODUTO', 'DESCRICAO_PRODUTO', 'QUANTIDADE', 'PRECO_UNITARIO', 'DATA_VENDA'];

                if ($header === $expectedHeader) {
                    foreach ($rows as $lineNumber => $row) {
                        if (trim($row) === "") {
                            continue;
                        }

                        $data = str_getcsv($row);
                        $codigoProduto = $data[0];
                        $descricaoProduto = strtoupper($data[1]);
                        $quantidade = (int)$data[2];
                        $precoUnitario = (float)$data[3];
                        $dataVenda = $data[4];

                        $stmt = $conn->prepare("INSERT INTO dados_venda (CODIGO_PRODUTO, DESCRICAO_PRODUTO, QUANTIDADE, PRECO_UNITARIO, DATA_VENDA) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssids", $codigoProduto, $descricaoProduto, $quantidade, $precoUnitario, $dataVenda);

                        if (!$stmt->execute()) {
                            $message = "Erro ao inserir dados na linha " . ($lineNumber + 2) . ": " . $stmt->error;
                            $error = true;
                            break;
                        }
                    }

                    if (!$error) {
                        $message = "Arquivo .csv processado e dados inseridos com sucesso!";
                    }
                } else {
                    $message = "Erro: O cabeçalho do arquivo .csv está incorreto.";
                    $error = true;
                }
            } else {
                $message = "Erro: Nenhum arquivo .csv encontrado no arquivo .zip.";
                $error = true;
            }
        } else {
            $message = "Erro ao abrir o arquivo .zip.";
            $error = true;
        }
    } else {
        $message = "Formato de arquivo não suportado. Apenas arquivos .zip contendo .csv são permitidos.";
        $error = true;
    }
} else {
    $message = "Nenhum arquivo foi enviado.";
    $error = true;
}
?>

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

    <!-- Modal do Bootstrap -->
    <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header <?php echo $error ? 'bg-danger' : 'bg-success'; ?>">
                    <h5 class="modal-title text-white" id="resultModalLabel">
                        <?php echo $error ? 'Erro' : 'Sucesso'; ?>
                    </h5>
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
        $(document).ready(function () {
            // Exibe o modal automaticamente
            $('#resultModal').modal('show');

            // Redireciona automaticamente após 5 segundos
            setTimeout(function () {
                window.location.href = "importar_zip.php";
            }, 5000);

            // Ou redireciona ao clicar no botão "Fechar"
            $('#closeButton').click(function () {
                window.location.href = "importar_zip.php";
            });
        });
    </script>

</body>

</html>
