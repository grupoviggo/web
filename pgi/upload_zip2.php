<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao_admpgi.php'; // Conexão com o banco de dados

$message = "";
$error = false;
$rowsInserted = 0; // Contador de linhas inseridas com sucesso

// Funções auxiliares (mantêm as funções já definidas no código anterior)

function formatCep($cep)
{
    return str_pad(preg_replace('/\D/', '', $cep), 8, "0", STR_PAD_LEFT);
}

function getDataType($value)
{
    if (is_numeric($value)) {
        return 'd'; // double
    }
    return 's'; // string
}

function formatValorVenda($valor)
{
    $valor = preg_replace('/[^0-9.]/', '', $valor);
    return number_format((float) $valor, 2, '.', '');
}

function replaceAccent($str)
{
    $accents = [
        'á',
        'é',
        'í',
        'ó',
        'ú',
        'à',
        'è',
        'ì',
        'ò',
        'ù',
        'ã',
        'õ',
        'â',
        'ê',
        'î',
        'ô',
        'û',
        'ä',
        'ë',
        'ï',
        'ö',
        'ü',
        'ç',
        'Á',
        'É',
        'Í',
        'Ó',
        'Ú',
        'À',
        'È',
        'Ì',
        'Ò',
        'Ù',
        'Ã',
        'Õ',
        'Â',
        'Ê',
        'Î',
        'Ô',
        'Û',
        'Ä',
        'Ë',
        'Ï',
        'Ö',
        'Ü',
        'Ç'
    ];
    $noAccents = [
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'o',
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'e',
        'i',
        'o',
        'u',
        'c',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'O',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'E',
        'I',
        'O',
        'U',
        'C'
    ];
    return str_replace($accents, $noAccents, $str);
}

function readCsvFile($fileContent)
{
    return mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1');
}

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
                $csvFileContent = readCsvFile($csvFileContent);

                $rows = explode("\n", $csvFileContent);
                $header = str_getcsv(array_shift($rows), ';');

                error_log("Cabeçalho CSV: " . print_r($header, true));

                // Mapeamento ajustado para refletir o cabeçalho real do CSV
                $csvToDbMap = [
                    'Código da Venda' => 'codigo_venda',
                    'PDV - Adabás' => 'pdv',
                    'Data Input da Venda' => 'data_input',
                    'Horário' => 'horario_venda',
                    'Tipo cliente (CPF / CNPJ)' => 'tipo_cliente',
                    'Endereço do Cliente' => 'endereco_cliente',
                    'Número' => 'numero',
                    'Bairro' => 'bairro',
                    'Complemento' => 'complemento',
                    'Cidade do Cliente' => 'cidade_cliente',
                    'UF do Cliente' => 'uf_cliente',
                    'CEP do Cliente' => 'cep_cliente',
                    'Tipo de Serviço' => 'tipo_servico',
                    'Serviço' => 'servico',
                    'Plano' => 'plano',
                    'CPF do Vendedor' => 'cpf_vendedor',
                    'RE do Vendedor' => 're_vendedor',
                    'Nome do Vendedor' => 'nome_vendedor',
                    'Observações da Venda' => 'observacoes',
                    'Status da Venda' => 'status_venda',
                    'Status do Serviço' => 'status_servico',
                    'Fatura - Email' => 'fatura_email',
                    'Fatura - Dia Pagamento' => 'fatura_dia_pagamento',
                    'Valor da Venda' => 'valor_venda',
                    'Serviços Adicionais' => 'servicos_adicionais',
                ];

                $invalidRows = [];

                foreach ($rows as $lineNumber => $row) {
                    if (trim($row) === "") {
                        continue;
                    }
                
                    $data = str_getcsv($row, ';');
                    $dataAssoc = [];
                
                    // Preenche apenas as colunas necessárias do mapeamento
                    foreach ($csvToDbMap as $csvColumn => $dbColumn) {
                        $index = array_search($csvColumn, $header);
                        $dataAssoc[$csvColumn] = $index !== false ? $data[$index] : null;
                    }
                
                    // **CONVERSÃO DO CAMPO 'TIPO CLIENTE'**
                    if (isset($dataAssoc['Tipo cliente (CPF / CNPJ)'])) {
                        switch ($dataAssoc['Tipo cliente (CPF / CNPJ)']) {
                            case 'Pessoa Física':
                            case 'Pessoa Fisica':
                                $dataAssoc['Tipo cliente (CPF / CNPJ)'] = 'B2C';
                                break;
                            case 'Pessoa Jurídica':
                            case 'Pessoa Juridica':
                                $dataAssoc['Tipo cliente (CPF / CNPJ)'] = 'B2B';
                                break;
                        }
                    }
                
                    // **CONVERSÃO DO CAMPO 'STATUS VENDA'**
                    if (isset($dataAssoc['Status da Venda'])) {
                        switch ($dataAssoc['Status da Venda']) {
                            case 'Aguardando':
                                $dataAssoc['Status da Venda'] = 'EM FILA';
                                break;
                            case 'AGUARDANDO': // Somente os registros com este valor serão inseridos
                                break;
                            default:
                                // Pula para a próxima linha caso o status não seja AGUARDANDO
                                continue 2;
                        }
                    }
                
                    // Verifica e formata o CEP e o CPF
                    if (isset($dataAssoc['CEP do Cliente'])) {
                        $dataAssoc['CEP do Cliente'] = formatCep($dataAssoc['CEP do Cliente']);
                    }
                
                    // Permite que o dia da fatura seja zerado
                    if (isset($dataAssoc['Fatura - Dia Pagamento']) && $dataAssoc['Fatura - Dia Pagamento'] === '') {
                        $dataAssoc['Fatura - Dia Pagamento'] = null; // ou 0, dependendo da sua preferência
                    }
                
                    // Verifica se o Código da Venda é válido
                    if (empty($dataAssoc['Código da Venda']) || strtolower($dataAssoc['Código da Venda']) === 'nenhum') {
                        $invalidRows[] = $lineNumber + 2; // Linha original no CSV
                        error_log("Linha inválida: " . ($lineNumber + 2) . " - Código da Venda: " . $dataAssoc['Código da Venda']);
                        continue;
                    }
                
                    // Formata o valor da venda
                    if (isset($dataAssoc['Valor da Venda'])) {
                        $dataAssoc['Valor da Venda'] = formatValorVenda($dataAssoc['Valor da Venda']);
                    }
                
                    // Substitui acentos nos valores
                    foreach ($dataAssoc as $key => $value) {
                        if (is_string($value)) {
                            $dataAssoc[$key] = replaceAccent($value);
                        }
                    }
                
                    $conn->set_charset("utf8mb4");
                
                    $query = "INSERT INTO vendas (" . implode(", ", array_values($csvToDbMap)) . ") VALUES (" . implode(", ", array_fill(0, count($csvToDbMap), '?')) . ")";
                    $stmt = $conn->prepare($query);
                
                    $types = '';
                    $values = [];
                    foreach ($dataAssoc as $column => $value) {
                        $types .= getDataType($value);
                        $values[] = $value;
                    }
                
                    if ($stmt) {
                        $stmt->bind_param($types, ...$values);
                        if ($stmt->execute()) {
                            $rowsInserted++; // Incrementa o contador de linhas inseridas
                        }
                        $stmt->close();
                    } else {
                        error_log("Erro ao preparar a query: " . $conn->error);
                    }
                }
                

                if (empty($invalidRows)) {
                    $message = "Arquivo CSV importado com sucesso! Total de linhas inseridas: $rowsInserted.";
                } else {
                    $message = "O arquivo foi importado com alguns erros. Total de linhas inseridas: $rowsInserted. Verifique as linhas: " . implode(", ", $invalidRows);
                    $error = true;
                }
            } else {
                $message = "Erro ao ler o conteúdo do arquivo CSV.";
                $error = true;
            }
        } else {
            $message = "Erro ao abrir o arquivo ZIP.";
            $error = true;
        }
    } else {
        $message = "Por favor, envie um arquivo .zip contendo um CSV.";
        $error = true;
    }
} else {
    $message = "Nenhum arquivo foi enviado.";
    $error = true;
}

$modalType = $error ? 'danger' : 'success';
$redirectUrl = 'importar_zip';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Importação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-<?= $modalType; ?> text-white">
                <h5 class="modal-title" id="resultModalLabel">
                    <?= $error ? 'Erro na Importação' : 'Importação Bem-Sucedida'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= htmlspecialchars($message); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
        resultModal.show();

        // Redireciona após 5 segundos
        setTimeout(function () {
            window.location.href = '<?= $redirectUrl; ?>';
        }, 5000);
    });
</script>

</body>
</html>