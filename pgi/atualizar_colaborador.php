<?php
// Conexão com o banco de dados
$connect = mysqli_connect("200.147.61.78", "viggoadm2", "Viggo2024@", "nexus");

if (!$connect) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Verifica se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para buscar os dados do usuário específico
    $query = "SELECT * FROM dados_colaborador WHERE id = $id";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "<p class='alert alert-danger'>Usuário não encontrado.</p>";
        exit;
    }
} else {
    echo "<p class='alert alert-danger'>ID do usuário não foi especificado.</p>";
    exit;
}

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $NOME = mysqli_real_escape_string($connect, $_POST['NOME']);
    $CPF = mysqli_real_escape_string($connect, $_POST['CPF']);
    $EMAIL = mysqli_real_escape_string($connect, $_POST['email']);
    $DT_NASCIMENTO = mysqli_real_escape_string($connect, $_POST['DATA_NASCIMENTO']);
    $DT_ADMISSAO = mysqli_real_escape_string($connect, $_POST['DATA_ADMISSAO']);
    $TELEFONE = mysqli_real_escape_string($connect, $_POST['telefone']);
    $CARGO = mysqli_real_escape_string($connect, $_POST['cargo']);
    $DEPARTAMENTO = mysqli_real_escape_string($connect, $_POST['departamento']);
    $NIVEL = mysqli_real_escape_string($connect, $_POST['nivel']);
    $CONSULTOR_BASE_NOME = mysqli_real_escape_string($connect, $_POST['CONSULTOR_BASE_NOME']); // Adicionando o campo CONSULTOR_BASE_NOME

    // Atualiza os dados do usuário no banco de dados
    $update_query = "
            UPDATE dados_colaborador
            SET NOME = '$NOME', CPF = '$CPF', DT_NASCIMENTO='$DT_NASCIMENTO',DT_ADMISSAO='$DT_ADMISSAO',
            cargo = '$CARGO', CONSULTOR_BASE_NOME='$CONSULTOR_BASE_NOME', departamento='$DEPARTAMENTO', email='$EMAIL',
            nivel = '$NIVEL' WHERE id = $id
        ";

    if (mysqli_query($connect, $update_query)) {
        echo "<p class='alert alert-success'>Usuário atualizado com sucesso!</p>";
        echo "<script>
                    setTimeout(function() {
                        window.location.href = 'colaboradores_rh.php';
                    }, 1500); // Redireciona após 2 segundos
                  </script>";
    } else {
        echo "<p class='alert alert-danger'>Erro ao atualizar usuário: " . mysqli_error($connect) . "</p>";
    }
}

// Fecha a conexão com o banco de dados
mysqli_close($connect);
?>