<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Venda</title>
</head>
<body>
    <?php
    // Variáveis definidas na página principal
    $codigo_venda = "12345"; // Exemplo de valor
    $backoffice = "backoffice_user";
    $nomeCompleto = "João da Silva";
    $documento = "123.456.789-00";
    $dadosPainelFixa = "Plano Fixo";
    $dadosPainelMovel = "Plano Móvel";
    ?>

    <!-- Formulário que envia as variáveis via POST -->
    <form action="processa_dados.php" method="POST">
        <input type="hidden" name="codigo_venda" value="<?php echo htmlspecialchars($codigo_venda, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="backoffice" value="<?php echo htmlspecialchars($backoffice, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="nome_cliente" value="<?php echo htmlspecialchars($nomeCompleto, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="doc_cliente" value="<?php echo htmlspecialchars($documento, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="oferta" value="<?php echo htmlspecialchars($dadosPainelFixa, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="dadosPainelFixa" value="<?php echo htmlspecialchars($dadosPainelFixa, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="dadosPainelMovel" value="<?php echo htmlspecialchars($dadosPainelMovel, ENT_QUOTES, 'UTF-8'); ?>">

        <!-- Botão que dispara o envio -->
        <button type="submit">Finalizar</button>
    </form>
</body>
</html>
