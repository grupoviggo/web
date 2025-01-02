<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: index.php");
    exit();
}

// Obtém o usuário logado e seu ID da sessão
$usuario_logado = $_SESSION['usuario'];

// Verifica se o ID do usuário está definido na sessão antes de acessá-lo
$id_usuario = isset($_SESSION['ID']) ? $_SESSION['ID'] : '';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css/loginnexus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .password-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }
        .password-container input[type="password"] {
            width: calc(100% - 40px);
            padding-right: 30px;
        }
        .password-container .show-password-icon {
            position: absolute;
            right: 13px;
            cursor: pointer;
            color: #2B486E; /* Cor mais apagada */
        }
    </style>
</head>
<body>
<div class="wrapper fadeInDown">
  <div class="container-fluid d-flex justify-content-center align-items-center">
    <img src="./img/nexuslogin.png" style="width: 400px; height: auto;" class="imgnexuslogo" alt="NEXUS VIGGO" />
  </div>
  <br>
  <div id="formContent">
    <br>

    <!-- Login Form -->
    <form method="POST" action="nvsenha">
      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div style="text-align: left;">

          <h4 style="text-align: center; color: #ffc107;">Primeiro acesso - Alterar Senha</h4>
          <hr>

          <div style="display: flex; flex-direction: column; gap: 10px;">

            <div style="display: flex; gap: 10px; align-items: center;">
              <label for="usuario" style="color: #fff; margin-right: 8px; width: 180px;">&nbsp;USUÁRIO:</label>
              <input type="text" id="usuario" class="fadeIn second" name="usuario" placeholder="usuario@viggo" maxlength="150" style="width: 100%;" disabled value="<?php echo $usuario_logado; ?>">
            </div>

            <div class="password-container">
              <label for="senha" style="color: #fff; margin-right: 8px; width: 180px;">&nbsp;NOVA SENHA:</label>
              <input type="password" id="senha" class="fadeIn third" name="senha" placeholder="**********" maxlength="12">
              <span class="show-password-icon" id="show-password"><i class="fas fa-eye" style="margin-left: -5px;"></i></span>
            </div>
            
            <input type="hidden" name="senha_alterada" value="1">
            <input type="hidden" name="ID" value="<?php echo $id_usuario; ?>">

            <div style="display: flex; justify-content: right; align-items: right;">
              <span style="color: #fff; font-size: 12px;">**Atenção nova senha com no máximo de 12 caracteres**&nbsp;&nbsp;</span>
            </div>
          </div>
        </div>
      </div>
      <br><br>

      <!-- Botão de Consultar -->
      <div style="text-align: center; padding-top: 6px; background-color: #0b1b39;">
        <br>
        <button type="submit" id="cadastrar" class="btn btn-warning">ALTERAR SENHA</button>
        <br><br>
      </div>
    </form>
  </div>
  <script>
    $(document).ready(function(){
      $("#show-password").click(function(){
        var senhaField = $("#senha");
        var type = senhaField.attr("type") === "password" ? "text" : "password";
        senhaField.attr("type", type);
        $(this).find("i").toggleClass("fa-eye fa-eye-slash");
      });
    });
  </script>
</body>
</html>
