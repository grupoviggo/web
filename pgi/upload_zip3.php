<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao_admpgi.php'; // Conexão com o banco de dados

$message = "";
$error = false;

// Função para colocar 0 à esquerda no CEP (caso tenha menos de 8 caracteres)
function formatCep($cep)
{
    return str_pad($cep, 8, "0", STR_PAD_LEFT);
}



// Função para determinar o tipo de dados
function getDataType($value)
{
    if (is_numeric($value)) {
        return 'd'; // double
    }
    return 's'; // string
}

// Função para formatar o valor da venda
function formatValorVenda($valor)
{
    // Remove qualquer caractere que não seja número ou ponto
    $valor = preg_replace('/[^\d.]/', '', $valor);

    // Garante que o valor tenha duas casas decimais
    return number_format((float) $valor, 2, '.', '');
}

// Função para substituir acentos por caracteres não acentuados
function replaceAccent($str)
{
    $accents = ['á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û', 'ä', 'ë', 'ï', 'ö', 'ü', 'ç'];
    $noAccents = ['á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û', 'ä', 'ë', 'ï', 'ö', 'ü', 'ç'];

    // Substitui todos os caracteres acentuados
    return str_replace($accents, $noAccents, $str);
}

// Função para garantir que o CSV seja lido corretamente como UTF-8
function readCsvFile($fileContent)
{
    // Primeiro, tenta detectar e forçar a conversão para UTF-8
    $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'auto');  // Converte para UTF-8 (detectando a codificação original)

    // Verifica se a conversão foi bem-sucedida, se necessário, faz uma segunda conversão
    if (!mb_check_encoding($fileContent, 'UTF-8')) {
        $fileContent = utf8_encode($fileContent);  // Tentativa de outra conversão se o arquivo não for UTF-8
    }

    return $fileContent;
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
                // Garantir que o CSV foi lido com a codificação correta
                $csvFileContent = readCsvFile($csvFileContent);

                // Aqui, estamos lendo o arquivo CSV já convertido para UTF-8
                $rows = explode("\n", $csvFileContent);
                $header = str_getcsv(array_shift($rows), ';'); // Delimitador de coluna alterado para ponto e vírgula

                error_log("Cabeçalho CSV: " . print_r($header, true));

                // Mapeamento das colunas do CSV para o banco de dados
                $csvToDbMap = [
                    'Código da Venda' => 'codigo_venda',
                    'PDV' => 'pdv',
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

                    $data = str_getcsv($row, ';'); // Delimitador de coluna alterado para ponto e vírgula
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
                                $dataAssoc['Tipo cliente (CPF / CNPJ)'] = 'PF';
                                break;
                            case 'Pessoa Jurídica':
                                $dataAssoc['Tipo cliente (CPF / CNPJ)'] = 'PJ';
                                break;
                        }
                    }

                    // **CONVERSÃO DO CAMPO 'STATUS VENDA'**
                    if (isset($dataAssoc['Status da Venda'])) {
                        switch ($dataAssoc['Status da Venda']) {
                            case 'Finalizada':
                                $dataAssoc['Status da Venda'] = 'FINALIZADA';
                                break;
                            case 'Pendente BKO':
                                $dataAssoc['Status da Venda'] = 'P.BKO';
                                break;
                            case 'Pendente Vendedor':
                                $dataAssoc['Status da Venda'] = 'P.VENDEDOR';
                                break;
                            case 'Pendente Biometria':
                                $dataAssoc['Status da Venda'] = 'P.BIOMETRIA';
                                break;
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

                    // Configuração da conexão para usar utf8mb4
                    $conn->set_charset("utf8mb4"); // Define a collation para a conexão como utf8mb4

                    // Preparação e execução da inserção no banco
                    $query = "INSERT INTO vendas (" . implode(", ", array_values($csvToDbMap)) . ") VALUES (" . implode(", ", array_fill(0, count($csvToDbMap), '?')) . ")";
                    $stmt = $conn->prepare($query);

                    // Criação dos tipos de dados e valores para bind
                    $types = '';
                    $values = [];
                    foreach ($dataAssoc as $column => $value) {
                        $types .= getDataType($value);
                        $values[] = $value;
                    }

                    // Verifica se a declaração foi preparada com sucesso
                    if ($stmt) {
                        $stmt->bind_param($types, ...$values);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        // Log de erro caso a preparação da query falhe
                        error_log("Erro ao preparar a query: " . $conn->error);
                    }

                }

                if (empty($invalidRows)) {
                    $message = "Arquivo CSV importado com sucesso!";
                } else {
                    $message = "O arquivo foi importado com alguns erros. Verifique as linhas: " . implode(", ", $invalidRows);
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

// Exibindo a mensagem
if ($error) {
    echo "<p style='color: red;'>$message</p>";
} else {
    echo "<p style='color: green;'>$message</p>";
}
?>