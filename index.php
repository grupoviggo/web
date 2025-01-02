<?php
session_start();

// Verifica se o usuário já está logado, se sim, redireciona para menu.php
if (isset($_SESSION['usuario'])) {
    header("Location: menu");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOGIN NEXUS</title>

    <!-- CSS Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="./css/loginnexus.css">

    <!-- JS Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
<!-- <div class="container-fluid d-flex flex-column justify-content-center align-items-center min-vh-100 bg-blue"> -->
<div class="wrapper fadeInDown">
    <div class="container-fluid d-flex justify-content-center align-items-center">
      <a href=""><img src="./img/nexuslogin.png" style="width: 400px; height: auto;" class="imgnexuslogo" alt="NEXUS VIGGO" /></a>
    </div>
      <br>
  <div id="formContent">
    <!-- Tabs Titles -->

    <!-- Login Form -->
    <form method="POST" action="loginnexus">
      <br>
      <!-- <input type="text" id="usuario" class="fadeIn second" name="usuario" placeholder="usuário@grupoviggo.com.br" required>
      <input type="password" id="senha" class="fadeIn third" name="senha" placeholder="***********" required> -->
    <img src="./img/userico.png" class="ico-user" />
    <input type="text" id="usuario" class="fadeIn second" name="usuario" placeholder="usuario@grupoviggo.com.br" style="font-color: #495f7e;" required>

    <img src="./img/senhaico.png" class="ico-senha" />
    <input type="password" id="senha" class="fadeIn third" name="senha" placeholder="lllllllllllll" style="font-family: Wingdings, 'Poppins', sans-serif; font-size: 12px; font-color: #495f7e;" required>

      <br><br>
      <input type="submit" id="login" name="login" class="fadeIn fourth" value="ENTRAR" style="cursor: pointer";>
      <br><br>
    </form>

    <!-- Rodape -->
    <div id="formFooter">
    <img src="./img/poweredbi.png" style="width: 200px; height: auto;" alt="powered by power bi" />
    </div>

  </div>
</div>
<!-- </div> -->
</body>
</html>