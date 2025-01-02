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
    require 'SimpleXLSX.php'; // Carregar a biblioteca SimpleXLSX
    
    $message = "";
    $error = false;

    if (isset($_FILES['campanhaUpload'])) {
        $fileTmpPath = $_FILES['campanhaUpload']['tmp_name'];
        $fileName = $_FILES['campanhaUpload']['name'];
        $fileSize = $_FILES['campanhaUpload']['size'];
        $fileType = $_FILES['campanhaUpload']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if ($fileExtension === 'xlsx') {
            if ($xlsx = SimpleXLSX::parse($fileTmpPath)) {
                // Truncate na tabela antes de inserir novos dados
                $truncateQuery = "TRUNCATE TABLE Ranking_campanha";
                if (!$conn->query($truncateQuery)) {
                    $message = "Erro ao limpar tabela: " . $stmt->error;
                    $error = true;
                } else {
                    foreach ($xlsx->rows() as $index => $row) {
                        if ($index === 0)
                            continue; // Ignorar cabeçalho
    
                        // Mapeamento das colunas
                        $BASE = strtoupper($row[0]);
                        $NOME = strtoupper($row[1]);
                        $GERENTE_T = strtoupper($row[2]);
                        $GERENTE_B = strtoupper($row[3]);
                        $COORDENADOR = strtoupper($row[4]);
                        $SUPERVISOR = strtoupper($row[5]);
                        $BRUTA = number_format((float) $row[6], 2, '.', '');
                        $ATIVO = number_format((float) $row[7], 2, '.', '');
                        $CANCELADO = number_format((float) $row[8], 2, '.', '');
                        $RECEITA_VT = number_format((float) $row[9], 2, '.', '');
                        $VIVO_TOTAL = number_format((float) $row[10], 3, '.', '');
                        $B2B = intval($row[11]);
                        $CANCELAMENTO = number_format((float) $row[12], 3, '.', '');
                        $CUPONS = intval($row[13]);
                        $BONUS = intval($row[14]);
                        $CATEGORIA = strtoupper($row[15]);

                        $stmt = $conn->prepare("INSERT INTO Ranking_campanha (BASE, NOME, GERENTE_T, GERENTE_B, COORDENADOR, SUPERVISOR, BRUTA, ATIVO, CANCELADO, RECEITA_VT, VIVO_TOTAL, B2B, CANCELAMENTO, CUPONS, BONUS, CATEGORIA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssssdddddissss", $BASE, $NOME, $GERENTE_T, $GERENTE_B, $COORDENADOR, $SUPERVISOR, $BRUTA, $ATIVO, $CANCELADO, $RECEITA_VT, $VIVO_TOTAL, $B2B, $CANCELAMENTO, $CUPONS, $BONUS, $CATEGORIA);

                        if (!$stmt->execute()) {
                            $message .= "Erro ao inserir dados: " . $stmt->error . "<br>";
                            $error = true;
                            break;
                        }
                    }

                    if (!$error) {
                        $message = "Arquivo .xlsx processado, tabela limpa, e dados inseridos com sucesso!";
                    }
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
    <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header <?php echo $error ? 'bg-danger' : 'bg-success'; ?>">
                    <h5 class="modal-title text-white" id="resultModalLabel"><?php echo $error ? 'Erro' : 'Sucesso'; ?>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Exibe o modal automaticamente
        $(document).ready(function () {
            $('#resultModal').modal('show');

            // Redireciona ao clicar em "Fechar"
            $('#closeButton').click(function () {
                window.location.href = "carga-database-campanha.php";
            });
        });
    </script>

</body>

</html>