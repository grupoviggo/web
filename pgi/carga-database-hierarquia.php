<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login");
    exit();
}

// Defina o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel'];
$username_acesso = $_SESSION['nome'];

// Se o botão de sair foi clicado
if (isset($_POST['sairnexus'])) {
    // Encerra a sessão
    session_unset();
    session_destroy();
    header("Location: login");
    exit();
}

// Calcula o tempo de login
if (!isset($_SESSION['tempo_login'])) {
    $_SESSION['tempo_login'] = time(); // Armazena o tempo de login na sessão
}

$tempo_online = time() - $_SESSION['tempo_login']; // Calcula o tempo logado em segundos

// Verifica se o tempo é menor que 1 hora
if ($tempo_online < 3600) {
    $tempo_online_formatado = floor($tempo_online / 60) . " minutos";
} else {
    $horas = floor($tempo_online / 3600);
    $minutos = floor(($tempo_online % 3600) / 60);
    $tempo_online_formatado = sprintf("%02d:%02d horas", $horas, $minutos);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAINEL TFP - BASE DE DADOS</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/painelusuarios.css">
</head>

<body>
    <!-- Barra de navegação Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <!-- Adiciona a imagem ao lado esquerdo -->
        <a class="navbar-brand" href="painel">
            <img src="../img/nexuspgi.png" width="auto" height="25px" alt="">
        </a>
        <!-- Linha vertical pontilhada -->
        <div class="vertical-divider"></div>
        <!-- Nome do usuário e tempo logado -->
        <div class="navbar-text text-light ml-3 small"> <!-- Adiciona a classe small para diminuir o texto -->
            <span class="label-text">usuário: &nbsp;</span><strong><?php echo $_SESSION['nome']; ?></strong>
            <!-- Nome do usuário -->
            <br>
            <span class="label-text">logado: &nbsp;</span><span
                id="tempo-logado"><?php echo $tempo_online_formatado; ?></span> <!-- Tempo logado -->
        </div>
        <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">s
                <li class="nav-item">
                    <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                    <form class="form-inline my-2 my-lg-0">
                        <button class="btn btn-warning btn-sm btn-voltar" type="submit"
                            onclick="window.location.href='paineltfp'; return false;">VOLTAR AO MENU</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
    <br>
    <div class="container mt-4">
    <h3 class="text-center mb-4">FAZER UPLOAD DA PLANILHA DE HIERARQUIA</h3>
    <br>
    <form action="upload_hierarquia" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="fileUploadHierarquia">Selecione o arquivo (.xlsx ou .csv)</label>
            <input type="file" class="form-control-file" id="fileUploadHierarquia" name="fileUploadHierarquia" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>

    <!-- Adicione o link para o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Script para atualizar o tempo logado a cada minuto -->
    <script>
        // Obter o tempo inicial de minutos do PHP
        let minutosLogado = <?php echo floor($tempo_online / 60); ?>;
        // Função para atualizar o tempo logado
        function atualizarTempo() {
            minutosLogado++; // Incrementa 1 minuto
            if (minutosLogado < 60) {
                document.getElementById('tempo-logado').innerText = minutosLogado + " minutos";
            } else {
                let horas = Math.floor(minutosLogado / 60);
                let minutos = minutosLogado % 60;
                document.getElementById('tempo-logado').innerText = `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')} horas`;
            }
        }
        // Atualiza o tempo a cada 1 minuto (60000 milissegundos)
        setInterval(atualizarTempo, 60000);
    </script>
</body>

</html>