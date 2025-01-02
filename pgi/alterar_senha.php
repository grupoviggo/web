<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
  // Se não estiver logado, redireciona para a página de login
  header("Location: login.php");
  exit();
}

// Obtém o usuário logado e seu ID da sessão
$usuario_logado = $_SESSION['cpf'];

// Verifica se o ID do usuário está definido na sessão antes de acessá-lo
$id_usuario = isset($_SESSION['ID']) ? $_SESSION['ID'] : '';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar - Nexus</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../css/login_principal.css">
  <link rel="stylesheet" href="path/to/bootstrap.css">
  <style>
    /* Estilo da mensagem de aviso */
    .warning-message {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin-top: 10px;
    }

    /* Estilo do ícone de aviso */
    .warning-message i {
      margin-right: 8px;
    }

    /* Estilo do texto de aviso */
    .warning-message .senha-info {
      color:rgb(60, 72, 82);
      font-size: 14px;
      font-weight: 500;
    }

    /* Estilo do container de input de senha com o link */
    .input-field-senha {
      position: relative;
    }

    /* Estilo do input de senha */
    .input-field-senha input {
      width: calc(100% - 100px);
      padding-right: 70px;
    }

    /* Estilo do link "Mostrar senha" */
    .toggle-password {
      position: absolute;
      right: 7px;
      top: 51%;
      transform: translateY(-50%);
      font-size: 0.8em;
      color: #007BFF;
      cursor: pointer;
      text-decoration: none;
    }

    .toggle-password:hover {
      color: #076ad4;
    }

    /* Estilo da mensagem abaixo do campo */
    .senha-info {
      color:rgb(69, 79, 87);
      font-size: 0.85em;
      margin-top: -5px;
    }

    /* Estilo do Tooltip */
    .tooltip {
      position: absolute;
      bottom: 100%;
      left: 0;
      background-color: #0d6efd;
      color: #fff;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 14px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      white-space: nowrap;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    /* Classe para exibir o Tooltip */
    .tooltip.show {
      opacity: 1;
      visibility: visible;
    }

    /* Seta do tooltip */
    .tooltip-arrow {
      position: absolute;
      bottom: -5px;
      /* Posiciona a seta abaixo do tooltip */
      left: 20px;
      /* Ajusta a posição horizontal da seta */
      width: 0;
      height: 0;
      border-left: 5px solid transparent;
      border-right: 5px solid transparent;
      border-top: 5px solid #0d6efd;
      /* Cor da seta */
    }
  </style>

</head>

<body>

  <!-- Cabeçalho com logotipo e suporte -->
  <div class="header d-flex justify-content-between align-items-center">
    <!-- Logotipo -->
    <div class="logo">
      <img src="../img/nexuspgi.png" alt="Logotipo">
    </div>
    <!-- Problemas para entrar -->
    <div class="help">
      <span class="me-2" id="msg"> &nbsp;</span>
      <button class="btn btn-primary btn-sm button-ajuda" onclick="window.location.href='logout.php';">Fazer Login</button>
    </div>
  </div>

  <!-- Container principal -->
  <div class="main-container">
    <!-- Container de registro -->
    <div class="card-container">
    <div class="card-front">
    <div class="login-container">
      <!-- Mensagem de primeiro acesso -->
      <h2 class="welcome-message">Primeiro Acesso!</h2>

      <!-- Container de inputs -->
      <div id="form-content">
        <form id="alterarSenhaForm" method="POST" action="nvsenha">
          <!-- Campo de cpf -->
          <div class="input-field">
            <label for="cpf">CPF</label>
            <i id="cpfIcon" class="fa-regular fa-id-card fa-lg" style="color: #7ebed3;"></i>
            <input type="text" id="cpf" class="fadeIn second" name="cpf" disabled maxlength="14"
              value="<?php echo $usuario_logado; ?>">
            <p id="cpfMessage" class="message_cpf"></p>
          </div>
          <!-- Campo de Data de Nascimento -->
          <div class="input-field campo-data" style="position: relative;">
            <label for="nascimento">Data de Nascimento</label>
            <i id="nascimentoIcon" class="fa-solid fa-calendar-days fa-lg" style="color: #7ebed3;"></i>
            <input type="text" id="dt_nascimento" class="fadeIn second" name="dt_nascimento" placeholder="DD/MM/AAAA"
              maxlength="10" style="width: 100%;" required autofocus>
            <!-- Tooltip -->
            <div id="tooltip" class="tooltip">
              Por favor, informe sua data de nascimento para prosseguir!
              <div class="tooltip-arrow"></div>
            </div>
            <p id="nascimentoMessage" class="message"></p>
          </div>

          <!-- Campo de senha -->
          <div class="input-field-senha">
            <label for="senha">Nova Senha</label>
            <i id="lockIcon" class="fa-solid fa-lock fa-lg" style="color: #7ebed3; top: 55%;"></i>
            <input type="password" id="senha" class="fadeIn third" name="senha" maxlength="12"
              placeholder="************" required disabled>
            <a id="togglePasswordLink" class="toggle-password" onclick="togglePasswordVisibility()">Mostrar senha</a>
            <p id="senhaMessage" class="message_senha"></p>
          </div>
      </div>

      <!-- Mensagem de aviso -->
      <div class="warning-message">
        <i class="fa-solid fa-triangle-exclamation" style="color:rgb(255, 223, 107);"></i>
        <span class="senha-info">escolha uma senha de no máximo 12 caracteres</span>
      </div>
      <P></P>
      <!-- Inputs ocultos -->
      <input type="hidden" name="senha_alterada" value="1">
      <input type="hidden" name="ID" value="<?php echo $id_usuario; ?>">

      <!-- Botão de alterar senha -->
      <button type="submit" id="alterarSenhaBtn" class="alter-button" disabled>ALTERAR SENHA</button>

      <!-- Powered by logo abaixo do botão -->
      <br><br>
      <div class="powered-by">
        <img src="../img/poweredbinexus.png" style="width: 180px; height: auto;" alt="powered by Viggo">
      </div>
    </div>
    </form>
    </div>
  </div>
  </div>
  <br><br><br>
  <!-- Rodapé fora do container principal -->
  <div class="page-footer">
    <p>
      <a href="#">Quem somos nós</a>
      <span class="separator">|</span>
      <a href="#">Fale conosco</a>
      <span class="separator">|</span>
      <a href="#">Termos de uso</a>
    </p>
    <br>
    <p style="color: rgb(121, 119, 122); font-size: 0.7em; font-weight: bold;">&copy; 2024 VIGGO - Todos os direitos reservados.
    </p>
  </div>
  <!-- Adicionando jQuery (certifique-se de incluir isso antes do seu script) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const tooltip = document.getElementById("tooltip");
      const input = document.getElementById("dt_nascimento");

      // Exibe o tooltip ao carregar a página
      tooltip.classList.add("show");

      // Remove o tooltip ao começar a digitar
      input.addEventListener("input", function () {
        tooltip.classList.remove("show");
      });
    });

  </script>
  <script>
    $(document).ready(function () {
      // Máscara para o campo CPF
      $('#cpf').mask('000.000.000-00');
      // Máscara para o campo Data de Nascimento
      $('#dt_nascimento').mask('00/00/0000');
    });

  </script>
  <script>
    // Seleciona os elementos do DOM
    const cpfInput = document.getElementById("cpf");
    const cpfMessage = document.getElementById("cpfMessage");
    const nascimentoInput = document.getElementById("dt_nascimento");
    const nascimentoMessage = document.getElementById("nascimentoMessage");
    const senhaInput = document.getElementById("senha");
    const senhaMessage = document.getElementById("senhaMessage");
    const alterarSenhaBtn = document.getElementById("alterarSenhaBtn");

    let nascimentoValido = false;
    let senhaValida = false;


    // Função para desabilitar o campo senha
    function disableSenha() {
      senhaInput.disabled = true;

    }

    // Função para desabilitar o campo senha
    function enableSenha() {
      senhaInput.disabled = false;

    }

    // Função para desabilitar o botão de alteração de senha
    function disableButton() {
      alterarSenhaBtn.disabled = true;

    }

    // Função para habilitar o botão de alteração de senha
    function enableButton() {
      alterarSenhaBtn.disabled = false;
    }

    // Função para atualizar o ícone de CPF
    function updateCpfIcon(isValid) {
      const cpfIcon = document.getElementById("cpfIcon");
      cpfIcon.style.color = isValid ? "#17ba17" : "#7ebed3";
    }

    // Função para atualizar o ícone de data de nascimento
    function updateNascimentoIcon(isValid) {
      const nascimentoIcon = document.getElementById("nascimentoIcon"); // Ícone reutilizado
      nascimentoIcon.style.color = isValid ? "#17ba17" : "#7ebed3";
    }

    // Função para validar o CPF
    async function validateCPF() {
      const cpf = cpfInput.value.trim();

      // Remover classes e mensagens anteriores
      cpfInput.classList.remove("valid", "invalid", "incompatible");
      cpfMessage.classList.remove("visible");

      // Validação básica do CPF (apenas para garantir o formato)
      const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
      if (!cpfRegex.test(cpf)) {
        cpfInput.classList.add("invalid");
        cpfMessage.textContent = "CPF inválido!";
        cpfMessage.classList.add("visible");
        updateCpfIcon(false);
        cpfValido = false;
        return;
      }

      // Validação via servidor
      try {
        const response = await fetch("valida_cpfvalido", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `cpf=${encodeURIComponent(cpf)}`
        });

        const result = await response.json();

        if (result.status === "found") {
          cpfInput.classList.add("valid");
          cpfMessage.textContent = "CPF válido!";
          cpfMessage.classList.add("visible");
          cpfMessage.style.color = "#038b37";
          updateCpfIcon(true);
          cpfValido = true;
          validateNascimento(); // Inicia a validação de nascimento após o CPF ser válido
        } else {
          cpfInput.classList.add("invalid");
          cpfMessage.textContent = "CPF não encontrado!";
          cpfMessage.style.color = "red";
          cpfMessage.classList.add("visible");
          updateCpfIcon(false);
          cpfValido = false;

        }
      } catch (error) {
        console.error("Erro ao validar o CPF:", error);
        cpfMessage.textContent = "Erro ao validar o CPF.";
        cpfMessage.style.color = "red";
        cpfMessage.classList.add("visible");
        updateCpfIcon(false);
        cpfValido = false;

      }
    }

    async function validateNascimento() {
      const cpf = cpfInput.value.trim(); // Obtém o valor do CPF
      const nascimento = nascimentoInput.value.trim(); // Obtém o valor da data de nascimento

      // Remover classes e mensagens anteriores
      nascimentoInput.classList.remove("valid", "invalid");
      nascimentoMessage.classList.remove("visible");
      nascimentoMessage.textContent = ""; // Limpa qualquer mensagem anterior

      // Validação básica da data de nascimento
      const nascimentoRegex = /^\d{2}\/\d{2}\/\d{4}$/;

      // Verificar se a data tem o formato correto
      if (!nascimentoRegex.test(nascimento)) {
        nascimentoInput.classList.add("invalid");
        nascimentoMessage.textContent = "Data de nascimento inválida!";
        nascimentoMessage.classList.add("visible");
        updateNascimentoIcon(false);
        nascimentoValido = false;
        disableButton();
        disableSenha();
        return;
      }

      // Envia os dados para o PHP
      try {
        const response = await fetch('valida_dt_nascimento', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `cpf=${cpf}&dt_nascimento=${nascimento}`
        });

        const result = await response.json();

        console.log('Resposta do servidor:', result); // Verifique a resposta no console

        if (result.status === 'found') {
          nascimentoInput.classList.add("valid");
          nascimentoMessage.textContent = "Data de nascimento válida!";
          nascimentoMessage.classList.add("visible"); // Adiciona estilo de sucesso
          nascimentoMessage.style.color = "#038b37";
          updateNascimentoIcon(true);
          enableSenha();
          nascimentoValido = true;
        } else if (result.status === 'dt_nascimento_mismatch') {
          nascimentoInput.classList.add("incompatible");
          nascimentoMessage.textContent = "Data de nascimento não corresponde ao CPF!";
          nascimentoMessage.classList.add("visible");
          nascimentoMessage.style.color = "#FFA500";
          updateNascimentoIcon(false);
          nascimentoValido = false;
          disableButton();
          disableSenha();
        } else if (result.status === 'not_found') {
          nascimentoInput.classList.add("invalid");
          nascimentoMessage.textContent = "CPF não encontrado!";
          nascimentoMessage.classList.add("visible");
          nascimentoMessage.style.color = "red";
          updateNascimentoIcon(false);
          nascimentoValido = false;
          disableButton();
          disableSenha();
        } else {
          console.error('Erro inesperado:', result);
          nascimentoInput.classList.add("invalid");
          nascimentoMessage.textContent = "Erro ao validar a data de nascimento!";
          nascimentoMessage.classList.add("visible");
          nascimentoMessage.style.color = "red";
          updateNascimentoIcon(false);
          nascimentoValido = false;
          disableButton();
          disableSenha();
        }
      } catch (error) {
        console.error("Erro ao validar a data de nascimento:", error);
        nascimentoInput.classList.add("invalid");
        nascimentoMessage.textContent = "Erro ao validar a data de nascimento!";
        nascimentoMessage.classList.add("visible");
        nascimentoMessage.style.color = "red";
        updateNascimentoIcon(false);
        nascimentoValido = false;
        disableButton();
        disableSenha();
      }
    }


    // Função para validar a senha
    function validatePassword() {
      const senha = senhaInput.value.trim();

      // Remover classes e mensagens anteriores
      senhaInput.classList.remove("valid", "invalid");
      senhaMessage.classList.remove("visible");

      // A validação da senha será feita sempre, caso o campo não esteja vazio
      if (senha === "") {
        senhaValida = false;
        disableButton(); // Desabilita o botão caso a senha esteja vazia
        return;
      }

      // Validação da senha
      if (senha.length < 6) {
        senhaMessage.textContent = "A senha deve ter pelo menos 6 caracteres.";
        senhaMessage.style.color = "red";
        senhaInput.classList.add("invalid");
        senhaMessage.classList.add("visible");
        senhaValida = false;
        disableButton(); // Desabilita o botão caso a senha seja inválida
      } else if (!/[A-Z]/.test(senha)) {
        senhaMessage.textContent = "A senha deve conter pelo menos 1 letra maiúscula.";
        senhaMessage.style.color = "red";
        senhaInput.classList.add("invalid");
        senhaMessage.classList.add("visible");
        senhaValida = false;
        disableButton(); // Desabilita o botão caso a senha não atenda ao critério
      } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(senha)) {
        senhaMessage.textContent = "A senha deve conter pelo menos 1 caractere especial.";
        senhaMessage.style.color = "red";
        senhaInput.classList.add("invalid");
        senhaMessage.classList.add("visible");
        senhaValida = false;
        disableButton(); // Desabilita o botão caso a senha não atenda ao critério
      } else {
        senhaMessage.textContent = "Senha válida.";
        senhaMessage.style.color = "#038b37";
        senhaInput.classList.add("valid");
        senhaMessage.classList.add("visible");
        senhaValida = true;
      }

      // Verifica a validade do formulário após a validação da senha
      checkFormValidity();
    }

    // Função para verificar se o formulário é válido
    function checkFormValidity() {
      if (nascimentoValido && senhaValida) {
        enableButton(); // Habilita o botão se todos os campos forem válidos
      } else {
        disableButton(); // Desabilita o botão se algum campo for inválido
      }
    }

    // Event Listeners
    nascimentoInput.addEventListener("input", validateNascimento);
    senhaInput.addEventListener("input", validatePassword);
  </script>



  <script>
    function togglePasswordVisibility() {
      const passwordField = document.getElementById("senha");
      const togglePasswordLink = document.getElementById("togglePasswordLink");

      // Alterna entre os tipos 'password' e 'text'
      if (passwordField.type === "password") {
        passwordField.type = "text";
        togglePasswordLink.textContent = "Ocultar senha"; // Atualiza o texto do link
      } else {
        passwordField.type = "password";
        togglePasswordLink.textContent = "Mostrar senha"; // Atualiza o texto do link
      }
    }
  </script>
  <script src="../js/mascara_date.js"></script>
</body>

</html>