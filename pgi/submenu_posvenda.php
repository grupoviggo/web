<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_venda']) && isset($_POST['status_venda'])) {
    // Sanitizando as entradas
    $codigo_venda = htmlspecialchars($_POST['codigo_venda'], ENT_QUOTES, 'UTF-8');
    $status_venda = htmlspecialchars($_POST['status_venda'], ENT_QUOTES, 'UTF-8');

    if (!empty($codigo_venda) && !empty($status_venda)) {
        // Início do submenu
        echo "<div>";

        // Gerar submenu com base no status da venda
        switch ($status_venda) {
            case 'CANCELADO':
                echo "<ul class='sub_pos'>
                    <li><button onclick='showObservationBox(\"COMERCIAL\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>Comercial</button></li>
                    <li><button onclick='showObservationBox(\"TÉCNICO\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>Técnico</button></li>
                    <li><button onclick='showObservationBox(\"OPERACIONAL\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>Operacional</button></li>
                </ul>";
                break;

            case 'ATIVO':
                echo "<ul class='sub_pos'>
                    <li><button onclick='showObservationBox(\"ATIVO\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>Adicionar Observação</button></li>
                </ul>";
                break;

                case 'PENDENTE':
                    echo "<ul class='sub_pos'>
                        <li><button onclick='showObservationBox(\"INSTALAÇÃO\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>Instalação</button></li>
                        <li><button onclick='showObservationBox(\"AGENDAMENTO\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>Agendamento</button></li>
                        <li><button onclick='showObservationBox(\"-\", \"" . htmlspecialchars($codigo_venda, ENT_QUOTES) . "\", this)'>-</button></li>
                    </ul>";
                    break;

            default:
                echo "<p>Nenhuma opção disponível para o status: " . htmlspecialchars($status_venda, ENT_QUOTES) . "</p>";
                break;
        }

        // Fechando o container
        echo "</div>";
    } else {
        echo "<div class='sub_pos-container'><p>Dados inválidos.</p></div>";
    }
} else {
    echo "<div class='sub_pos-container'><p>Requisição inválida.</p></div>";
}
?>
