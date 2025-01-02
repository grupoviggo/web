<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_venda'])) {
    $codigo_venda = htmlspecialchars($_POST['codigo_venda'], ENT_QUOTES, 'UTF-8');

    if (!empty($codigo_venda)) {
        echo "
            <div>
                <form id='form-auditar' action='emissao_01' method='POST' style='display: none;'>
                    <input type='hidden' name='codigo_venda' value='$codigo_venda'>
                </form>
                <a href='#' onclick=\"document.getElementById('form-auditar').submit(); return false;\" class='submenu-items'>
                    <i class='fa-solid fa-headset'></i> Auditar Venda
                </a>
            </div>
            <hr style='border: none; border-top: 1px solid #79888c; margin: 10px 0;'>

            <a href='#' 
                data-bs-toggle='modal' 
                data-bs-target='#tabularModal' 
                class='submenu-items' 
                data-codigo-venda='$codigo_venda'>
                <i class='fa-solid fa-right-left'></i> Tabular Venda
            </a>

            <form id='form-comunicacao' action='comunicacao_interna.php' method='POST' style='display: none;'>
                <input type='hidden' name='codigo_venda' value='$codigo_venda'>
            </form>
            <a href='#' onclick=\"document.getElementById('form-comunicacao').submit(); return false;\" class='submenu-items'>
                <i class='fa-solid fa-repeat'></i> Comunicação Interna
            </a>
        ";
    } else {
        echo "<div class='error'>Código de venda inválido.</div>";
    }
} else {
    echo "<div class='error'>Acesso inválido. Código de venda deve ser enviado via POST.</div>";
}
?>