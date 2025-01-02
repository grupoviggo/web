<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index");
    exit();
}

// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];

// Verifica se o nível de acesso é diferente de 0 e 2, se sim, redireciona para o index
if ($nivel_acesso != 0 && $nivel_acesso != 2) {
    header("Location: index");
    exit();
}

// Se o botão de sair foi clicado
if (isset($_POST['sairnexus'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: index");
    exit();
}

// URL original
$urlOriginal = "https://app.powerbi.com/view?r=eyJrIjoiN2U2ODhjMzUtYzIxYy00YzM0LTk2NTEtZTM0MTc1ODMzZjQyIiwidCI6ImU0YjUyYWNhLTQzNmItNDhmMC05NGY1LWFhM2U2ZmIzZDVjYiJ9&Embed=false&filterPaneEnabled=false&navContentPaneEnabled=false&transparent=0";

// Redirecionamento
header("Location: " . $urlOriginal);
exit();
?>

