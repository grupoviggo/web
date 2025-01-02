<?php
if (isset($_GET['status']) && isset($_GET['codigo_venda'])) {
    $status = htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8');
    $codigo_venda = htmlspecialchars($_GET['codigo_venda'], ENT_QUOTES, 'UTF-8');

    if ($status === 'ATIVO') {
        echo "
            <div>
                <label>Data de Instalação:</label>
                <input type='date'>
                <button>OK</button>
            </div>
        ";
    } elseif ($status === 'PENDENTE') {
        echo "
            <div>
                <a href='#'>INSTALAÇÃO</a>
                <a href='#'>AGENDAMENTO</a>
            </div>
        ";
    } elseif ($status === 'CANCELADO') {
        echo "
            <div>
                <a href='#'>COMERCIAL</a>
                <a href='#'>TÉCNICO</a>
                <a href='#'>OPERACIONAL</a>
            </div>
        ";
    } else {
        echo "<div>Status inválido.</div>";
    }
} else {
    echo "<div>Parâmetros inválidos.</div>";
}
?>
