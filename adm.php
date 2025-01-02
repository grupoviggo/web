<?php
session_start();

// Verifica se o usuário já está logado, se sim, redireciona para caduser.php
if (isset($_SESSION['username'])) {
    header("Location: caduser");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LOGIN BD NEXUS</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="./css/loginnexusadm.css">
</head>
<body>
<div class="login-container">
    <img src="./img/nexusloginbd.png" alt="NEXUS VIGGO">
    
    <form method="POST" action="loginbdnexus">
        <div class="form-group">
            <input type="text" id="username" name="username" class="form-control" placeholder="usuário@grupoviggo.com.br" required>
        </div>
        <div class="form-group">
            <input type="password" id="senha" name="senha" class="form-control" placeholder="***********" required>
        </div>
        <button type="submit" id="login" name="login" class="btn-login">ENTRAR</button>
    </form>

    <div class="footer">
        <img src="./img/poweredbi.png" class="footer-img" alt="powered by power bi">
    </div>
</div>

</body>
</html>