<?php
// Inclui a conexão com o banco
include '../pgi/conexao_admpgi.php';

// Verifica se foi selecionado um gerente no filtro
$gerenteSelecionado = isset($_POST['gerente']) ? $_POST['gerente'] : ''; // Recebe o valor do filtro de gerente, via POST

// Consulta para obter a última data e hora de inserção
$queryData = "SELECT MAX(data_insercao) AS ultima_data FROM Ranking_campanha";
$resultsData = $conn->query($queryData);

// Consulta para buscar os dados com base no filtro de gerente
$sqlDados = "SELECT GERENTE_T, MAX(BASE) AS BASE, GERENTE_B, MAX(COORDENADOR) AS COORDENADOR, 
                    SUM(BRUTA) AS TOTAL_BRUTA, SUM(ATIVO) AS TOTAL_ATIVO, SUM(CANCELADO) AS TOTAL_CANCELADO, 
                    SUM(RECEITA_VT) AS TOTAL_RVT, SUM(B2B) AS TOTAL_B2B, SUM(CANCELAMENTO) AS TOTAL_CANCELAMENTO, 
                    COUNT(CASE WHEN CUPONS > 0 THEN NOME END) AS VENDEDORES_COM_CUPONS 
            FROM Ranking_campanha";

// Se um gerente foi selecionado, adicionar a cláusula WHERE
if ($gerenteSelecionado != '') {
    $sqlDados .= " WHERE GERENTE_T = '" . $conn->real_escape_string($gerenteSelecionado) . "'";
}

$sqlDados .= " GROUP BY COORDENADOR"; // Continua o agrupamento por coordenador
$resultDados = $conn->query($sqlDados);

// Consulta para buscar os gerentes únicos
$sqlGerentes = "SELECT DISTINCT GERENTE_T FROM Ranking_campanha";
$resultGerentes = $conn->query($sqlGerentes);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acelera VIGGO - Gestão</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="campanha.css">
</head>

<body>
    <header>
        <img src="Acelera Viggo - Logo.png" alt="Logo Acelera Viggo">
    </header>
    <hr>
    <div class="content_primario">
        <div class="header-container">
            <!-- Texto de atualização -->
            <?php
            // Verifica se a consulta retornou um resultado válido
            if ($resultsData && $row = $resultsData->fetch_assoc()) {
                // Converte a data para o formato desejado
                $ultimaData = new DateTime($row['ultima_data']);
                // Subtrai uma hora
                $ultimaData->modify('-1 hour');
                $dataFormatada = $ultimaData->format('d/m'); // Formato: dia/mês
                $horaFormatada = $ultimaData->format('H:i'); // Formato: hora:minuto
            } else {
                // Caso não haja resultados, utiliza um valor padrão
                $dataFormatada = 'N/A';
                $horaFormatada = 'N/A';
            }

            // Exibe a mensagem
            echo '<p class="updated-info">Atualizado em: ' . htmlspecialchars($dataFormatada) . ' - ' . htmlspecialchars($horaFormatada) . 'h</p>';
            ?>
<!-- Campo de busca com altura menor -->
<!-- Exibindo os dados na tabela -->
<form method="POST">
<div class="input-group mb-3">
    <select id="gerente-select" name="gerente" class="form-select form-select" onchange="this.form.submit()">
        <option value="">Todos os Gerentes</option>
        <?php
        if ($resultGerentes->num_rows > 0) {
            while ($row = $resultGerentes->fetch_assoc()) {
                echo "<option value=\"" . htmlspecialchars($row['GERENTE_T']) . "\"" . ($gerenteSelecionado == $row['GERENTE_T'] ? ' selected' : '') . ">" . htmlspecialchars($row['GERENTE_T']) . "</option>";
            }
        }
        ?>
    </select>
    <span class="input-group-text filter-btn">
        <i class="fa-solid fa-filter"></i>
    </span>
</div>

</form>
</div>
</div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>OPERAÇÃO</th>
                    <th>GERENTE.T</th>
                    <th>GERENTE BASE</th>
                    <th>COORDENADOR</th>
                    <th>BRUTA</th>
                    <th>INSTALADA</th>
                    <th>CANC.TOTAL</th>
                    <th>VT %</th>
                    <th>B2B</th>
                    <th>C.ELEGÍVEIS</th>
                </tr>
            </thead>
            <tbody id="table-body">
    <?php
    // Inicializa variáveis para somas
    $totalBruta = 0;
    $totalAtivo = 0;
    $totalCancelado = 0;
    $totalRVT = 0;
    $totalB2B = 0;
    $totalConsultores = 0;

    if ($resultDados->num_rows > 0) {
        while ($row = $resultDados->fetch_assoc()) {
            // Acumula os totais, agora considerando apenas os dados filtrados
            $totalBruta += $row['TOTAL_BRUTA'];
            $totalAtivo += $row['TOTAL_ATIVO'];
            $totalCancelado += $row['TOTAL_CANCELADO'];
            $totalRVT += $row['TOTAL_RVT'];
            $totalB2B += $row['TOTAL_B2B'];
            $totalConsultores += $row['VENDEDORES_COM_CUPONS'];

            // Calcular o percentual de cancelamento por linha
            $percentualCancelamento = ($row['TOTAL_BRUTA'] > 0) ? ($row['TOTAL_CANCELADO'] / $row['TOTAL_BRUTA']) * 100 : 0;
            $percentualVT = ($row['TOTAL_BRUTA'] > 0) ? ($row['TOTAL_RVT'] / $row['TOTAL_BRUTA']) * 100 : 0;

            // Exibe as linhas normais da tabela
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['BASE']) . "</td>";
            echo "<td>" . htmlspecialchars($row['GERENTE_T']) . "</td>";
            echo "<td>" . htmlspecialchars($row['GERENTE_B']) . "</td>";
            echo "<td>" . htmlspecialchars($row['COORDENADOR']) . "</td>";
            echo "<td>R$ " . number_format($row['TOTAL_BRUTA'], 2, ',', '.') . "</td>";
            echo "<td>R$ " . number_format($row['TOTAL_ATIVO'], 2, ',', '.') . "</td>";
            echo "<td>" . htmlspecialchars(number_format($percentualCancelamento, 1, ',', '') . '%') . "</td>";
            echo "<td>" . htmlspecialchars(number_format($percentualVT, 1, ',', '') . '%') . "</td>";
            echo "<td>" . htmlspecialchars($row['TOTAL_B2B']) . "</td>";
            echo "<td>" . htmlspecialchars($row['VENDEDORES_COM_CUPONS']) . "</td>";
            echo "</tr>";
        }

        // Calcular o percentual total de cancelamento e Vivo Total
        $percentualTotalCancelamento = ($totalBruta > 0) ? ($totalCancelado / $totalBruta) * 100 : 0;
        $percentualTotalVT = ($totalBruta > 0) ? ($totalRVT / $totalBruta) * 100 : 0;

        // Adiciona a linha de soma no final da tabela com ID ou classe para identificação
        echo "<tr id='total-row' class='totals-row'>";
        echo "<td colspan='4' style='text-align: right;'>TOTAL GERAL:</td>";
        echo "<td class='totals-row'>R$ " . number_format($totalBruta, 2, ',', '.') . "</td>";
        echo "<td class='totals-row'>R$ " . number_format($totalAtivo, 2, ',', '.') . "</td>";
        echo "<td class='totals-row'>" . htmlspecialchars(number_format($percentualTotalCancelamento, 1, ',', '') . '%') . "</td>";
        echo "<td class='totals-row'>" . htmlspecialchars(number_format($percentualTotalVT, 1, ',', '') . '%') . "</td>";
        echo "<td class='totals-row'>" . htmlspecialchars($totalB2B) . "</td>";
        echo "<td class='totals-row'>" . htmlspecialchars($totalConsultores) . "</td>";
        echo "</tr>";
    } else {
        echo "<tr><td colspan='10'>Nenhum dado encontrado</td></tr>";
    }
    $conn->close();
    ?>
</tbody>

    </table>
    </div>
    <p class="atencao">
    *Atenção: Os dados do relatório são ilustrativos, serão revisados no fechamento de campanha.<br>
    *Os Cupons Bônus de Cancelamento Comercial e Vendas aos Domingos serão Calculados no fechamento de Campanha.
</p>
    <img class="footer-img" src="logo.png" alt="Imagem de rodapé">
    <script>
const selectElement = document.getElementById('gerente-select');
const tableBody = document.getElementById('table-body');
const totalRow = document.getElementById('total-row'); // Linha de totais

// Filtra automaticamente ao selecionar no <select>
selectElement.addEventListener('change', () => {
    const selectedGerente = selectElement.value.trim();
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
        const gerenteColumn = row.cells[1]?.textContent.trim() || ''; // Coluna do Gerente

        if (selectedGerente === '' || gerenteColumn === selectedGerente) {
            row.style.display = ''; // Exibe a linha
        } else {
            row.style.display = 'none'; // Oculta a linha
        }
    });

    // A linha de totais deve permanecer visível
    totalRow.style.display = ''; // Garante que a linha de totais continue visível
});


</script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-input');
            const tableBody = document.getElementById('table-body');

            // Função para filtrar a tabela conforme a busca
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');

                // Percorre todas as linhas da tabela e exibe apenas as que correspondem à busca
                Array.from(rows).forEach(row => {
                    const cells = row.getElementsByTagName('td');
                    let match = false;

                    // Verifica se alguma célula da linha corresponde à busca
                    for (let cell of cells) {
                        if (cell.textContent.toLowerCase().includes(query)) {
                            match = true;
                            break;
                        }
                    }

                    row.style.display = match ? '' : 'none';
                });
            });
        });
    </script>
    <script>
        // Impede o clique com o botão direito do mouse
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            alert('A visualização do código fonte foi bloqueada.');
        });

        // Impede o uso de teclas de atalho como Ctrl+U e Ctrl+Shift+I
        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 'i' || e.key === 'I' || e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                alert('A visualização do código fonte foi bloqueada.');
            }
        });

    </script>
  <script>
    window.onbeforeunload = null; // Desativa a confirmação de saída
  </script>  
</body>
</html>