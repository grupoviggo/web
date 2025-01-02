<?php
// Inclui a conexão com o banco
include '../pgi/conexao_admpgi.php';

// Consulta para obter a última data e hora de inserção
$query = "SELECT MAX(data_insercao) AS ultima_data FROM Ranking_campanha";
$results = $conn->query($query);

// Query para buscar os dados
$sql = "SELECT BASE, NOME, SUPERVISOR, BRUTA, ATIVO, VIVO_TOTAL, CANCELAMENTO, B2B, CATEGORIA, CUPONS, BONUS FROM Ranking_campanha";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acelera VIGGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            if ($results && $row = $results->fetch_assoc()) {
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
            <!-- Campo de busca -->
            <div class="input-group">
                <input type="text" id="search-input" class="form-control" placeholder="Pesquisar vendedor ou Base">
                <button class="btn btn-primary" type="button">Buscar</button>
            </div>
        </div>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>BASE</th>
                    <th>SUPERVISOR</th>
                    <th>VENDEDOR</th>
                    <th>BRUTA</th>
                    <th>INSTALADA</th>
                    <th>CANC. TOTAL</th>
                    <th>VT %</th>
                    <th>B2B</th>
                    <th>CATEGORIA</th>
                    <th>CUPONS</th>
                    <th>BÔNUS</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                // Verifica se há resultados
                if ($result->num_rows > 0) {
                    // Exibe os dados na tabela
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['BASE']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['SUPERVISOR']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['NOME']) . "</td>";
                        echo "<td>R$ " . number_format($row['BRUTA'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['ATIVO'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['CANCELAMENTO'] == 100 ? '100%' : number_format($row['CANCELAMENTO']* 100, 1, ',', '') . '%') . "</td>";
                        echo "<td>" . htmlspecialchars($row['VIVO_TOTAL'] == 1 ? '100%': number_format($row['VIVO_TOTAL'] * 100, 1, ',', '') . '%') . "</td>";

                        echo "<td>" . htmlspecialchars($row['B2B']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CATEGORIA']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CUPONS']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['BONUS']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Nenhum dado encontrado</td></tr>";
                }

                // Fecha a conexão
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
        // Verifica se a página foi carregada por recarregamento
        if (performance.navigation.type === 1) {
            // Redireciona apenas no recarregamento
            window.location.href = "aceleraviggo.php";
        }
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
</body>

</html>