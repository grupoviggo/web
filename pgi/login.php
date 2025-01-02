<?php
session_start();

// Verifica se o usuário já está logado, se sim, redireciona para menu.php
if (isset($_SESSION['cpf'])) {
    header("Location: menu");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Nexus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/login_principal.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
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
            <span class="me-2" id="msg">Problemas para entrar? &nbsp;</span>
            <button class="btn btn-primary btn-sm button-ajuda" onclick="toggleCard()">Clique aqui</button>
        </div>
    </div>

<!-- Container principal -->
<div class="main-container">
<!-- Container do formulário com virada -->
<div class="card-container">
        <!-- Frente do card (Formulário principal) -->
        <div class="card-front">
            <form id="loginForm" method="POST" action="loginpgi">
                <div class="login-container">
                    <h2 class="welcome-message">Bem-vindo!</h2>
                    <div id="form-content">
                        <div class="input-field">
                            <label for="cpf">CPF</label>
                            <i id="cpfIcon" class="fa-regular fa-id-card fa-lg" style="color: #7ebed3;"></i>
                            <input type="text" id="cpf" name="cpf" placeholder="Insira seu CPF" oninput="mascaraCPF(this)" required>
                            <p id="cpfMessage" class="message"></p>
                        </div>
                        <div class="input-field-senha">
                            <label for="senha">Senha</label>
                            <i id="lockIcon" class="fa-solid fa-lock fa-lg" style="color: #7ebed3; top: 55%;"></i>
                            <input type="password" id="senha" name="senha" placeholder="Insira a senha" required>
                            <p id="senhaMessage" class="message_senha"></p>
                        </div>
                    </div>
                    <button id="login" name="login" type="submit" class="login-button" disabled>ENTRAR</button>

                    <!-- Linha e registro -->
                    <div class="separador">
                        <hr class="line">
                        <span class="text">OU</span>
                        <hr class="line">
                    </div>
                    <div class="register-link">
                        <span class="tooltip msg-text" data-tooltip="Para primeiro acesso, preencha com seu CPF e senha temporária fornecida pelo RH e clique em entrar">Primeiro Acesso?</span>
                    </div>

                    <!-- Powered by logo abaixo do botão -->
                    <br><br>
                    <div class="powered-by">
                        <img src="../img/poweredbinexus.png" style="width: 180px; height: auto;" alt="powered by Viggo">
                    </div>
                </div>
            </form>
        </div>

        <!-- Verso do card (Segundo formulário) -->
        <div class="card-back">
            <form id="registerForm" method="POST" action="register">
                <div class="login-container">
                    <h2 class="welcome-message">Preencha todos os dados!</h2>
                    <div class="input-field">
                        <label for="nome">Nome Completo</label>
                        <i class="fa-regular fa-user fa-lg" style="color: #7ebed3; top: 68%;"></i>
                        <input type="text" id="nome" name="nome" placeholder="Insira seu nome completo" style="text-transform: uppercase;"  oninput="this.value = this.value.toUpperCase();" required>
                    </div>
                    <div class="input-field">
                        <label for="dataNascimento">Data de Nascimento</label>
                        <i class="fa-regular fa-calendar fa-lg" style="color: #7ebed3; top: 68%;"></i>
                        <input type="date" id="dataNascimento" name="dataNascimento" maxlength="10" required>
                    </div>
                    <div class="input-field">
                        <label for="cpfRegister">CPF</label>
                        <i class="fa-regular fa-id-card fa-lg" style="color: #7ebed3; top: 68%;"></i>
                        <input type="text" id="cpfRegister" name="cpfRegister" placeholder="Insira seu CPF" oninput="mascaraCPF(this)" required>
                    </div>
                    <br>
                    <button id="register" name="register" type="submit" class="alter-button">Enviar</button>

                    <!-- Powered by logo abaixo do botão -->
                    <br><br><br>
                    <div class="powered-by">
                        <img src="../img/poweredbinexus.png" style="width: 180px; height: auto;" alt="powered by Viggo">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
<br>
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
        <p style="color:rgb(121, 119, 122); font-size: 0.7em; font-weight: bold;">&copy; 2025 VIGGO - Todos os direitos reservados</p>
    </div>
    <script src="../js/mascara_cpf.js"></script>
    <script>
function toggleCard() {
    const card = document.querySelector('.card-container');
    const button = document.querySelector('.button-ajuda');
    const msg = document.getElementById('msg'); // Seleciona a div com o ID 'msg'
    const cardFront = document.querySelector('.card-front');
    const cardBack = document.querySelector('.card-back');

    // Aplicar o efeito blur em ambos os cards durante a transição
    cardFront.style.backdropFilter = 'blur(6px)';
    cardFront.style.webkitBackdropFilter = 'blur(6px)';
    cardBack.style.backdropFilter = 'blur(6px)';
    cardBack.style.webkitBackdropFilter = 'blur(6px)';

    // Alternar a classe para o efeito de virada
    card.classList.toggle('flipped');

    // Após a transição (tempo deve coincidir com a duração no CSS)
    setTimeout(() => {
        if (card.classList.contains('flipped')) {
            // Quando o card back é visível
            cardBack.style.backdropFilter = 'none'; // Remove blur do card back
            cardBack.style.webkitBackdropFilter = 'none';
            cardFront.style.backdropFilter = 'blur(6px)'; // Mantém blur no card front
            cardFront.style.webkitBackdropFilter = 'blur(6px)';
        } else {
            // Quando o card front é visível
            cardFront.style.backdropFilter = 'none'; // Remove blur do card front
            cardFront.style.webkitBackdropFilter = 'none';
            cardBack.style.backdropFilter = 'blur(6px)'; // Mantém blur no card back
            cardBack.style.webkitBackdropFilter = 'blur(6px)';
        }
    }, 600); // Tempo da transição em milissegundos

    // Alterar o texto do botão e da mensagem
    if (card.classList.contains('flipped')) {
        button.textContent = 'Fazer Login';
        msg.textContent = ''; // Altera o texto da div para vazio
    } else {
        button.textContent = 'Clique aqui';
        msg.textContent = 'Problemas para entrar? \u00A0'; // Retorna ao texto original
    }
}
</script>



<script>
// Obtém os elementos do DOM
const cpfInput = document.getElementById("cpf");
const senhaInput = document.getElementById("senha");
const loginButton = document.getElementById("login");
const cpfIcon = document.getElementById("cpfIcon");
const lockIcon = document.getElementById("lockIcon");
const cpfMessage = document.getElementById("cpfMessage");
const senhaMessage = document.getElementById("senhaMessage");
const inputField = document.querySelector(".input-field");
const inputFieldSenha = document.querySelector(".input-field-senha");

// Função para habilitar o botão de login
function enableLoginButton() {
    const cpfIsValid = cpfInput.classList.contains("valid");
    const senhaIsValid = senhaInput.classList.contains("valid");

    if (cpfIsValid && senhaIsValid) {
        loginButton.removeAttribute("disabled"); // Habilita o botão de login
    }
}

// Função para desabilitar o botão de login
function disableLoginButton() {
    loginButton.setAttribute("disabled", "true"); // Desabilita o botão de login
}

// Função para atualizar o ícone de CPF
function updateCpfIcon(isValid) {
    if (isValid) {
        cpfIcon.style.color = "#17ba17"; // Verde
    } else {
        cpfIcon.style.color = "#7ebed3"; // Azul padrão
    }
}

// Função para atualizar o ícone de senha
function updateLockIcon(isValid) {
    if (isValid) {
        lockIcon.classList.remove("fa-lock");
        lockIcon.classList.add("fa-lock-open");
        lockIcon.style.color = "#17ba17"; // Verde
    } else {
        lockIcon.classList.remove("fa-lock-open");
        lockIcon.classList.add("fa-lock");
        lockIcon.style.color = "#7ebed3"; // Azul padrão
    }
}

// Função para validação do CPF
cpfInput.addEventListener("input", async () => {
    const cpf = cpfInput.value.trim();

    // Remover mensagens anteriores
    cpfInput.classList.remove("valid", "invalid");
    cpfMessage.classList.remove("visible");
    inputField.classList.remove("with-message");

    // Valida CPF vazio localmente
    if (!cpf) {
        cpfInput.classList.add("invalid");
        cpfMessage.textContent = "CPF não pode estar vazio.";
        cpfMessage.style.color = "red"; // Vermelho
        cpfMessage.classList.add("visible");
        inputField.classList.add("with-message");
        updateCpfIcon(false);
        disableLoginButton();
        return;
    }

    try {
        const response = await fetch("valida_cpfvalido", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `cpf=${encodeURIComponent(cpf)}`,
        });

        if (!response.ok) {
            cpfMessage.textContent = "Erro ao validar o CPF. Tente novamente.";
            cpfMessage.style.color = "red";
            cpfMessage.classList.add("visible");
            return;
        }

        const result = await response.json();
        console.log("Resposta do servidor:", result);

        if (result.status === "found") {
            cpfInput.classList.add("valid");
            cpfMessage.textContent = "CPF válido!";
            cpfMessage.style.color = "#038b37"; // Verde
            cpfMessage.classList.add("visible");
            inputField.classList.add("with-message");
            updateCpfIcon(true);
            enableLoginButton();
        } else if (result.status === "not_found") {
            cpfInput.classList.add("invalid");
            cpfMessage.textContent = "CPF não encontrado!";
            cpfMessage.style.color = "red"; // Vermelho
            cpfMessage.classList.add("visible");
            inputField.classList.add("with-message");
            updateCpfIcon(false);
            disableLoginButton();
        } else {
            cpfInput.classList.add("invalid");
            cpfMessage.textContent = result.message || "Erro ao validar o CPF.";
            cpfMessage.style.color = "red";
            cpfMessage.classList.add("visible");
            inputField.classList.add("with-message");
            updateCpfIcon(false);
            disableLoginButton();
        }
    } catch (error) {
        console.error("Erro ao validar o CPF:", error);
        cpfMessage.textContent = "Erro ao validar o CPF. Tente novamente.";
        cpfMessage.style.color = "red";
        cpfMessage.classList.add("visible");
        updateCpfIcon(false);
        disableLoginButton();
    }
});



// Função para validação da senha
senhaInput.addEventListener("input", async () => {
    const senha = senhaInput.value.trim();
    const cpf = cpfInput.value.trim(); // Obtém o CPF

    // Remover classes e mensagens anteriores
    senhaInput.classList.remove("valid", "invalid");
    senhaMessage.classList.remove("visible");
    inputFieldSenha.classList.remove("with-message");

    if (senha.length < 6) { // Validação simples para a senha
        senhaInput.classList.add("invalid");
        senhaMessage.textContent = "A senha deve ter pelo menos 6 caracteres.";
        senhaMessage.classList.add("visible");
        inputFieldSenha.classList.add("with-message");
        senhaMessage.style.color = "red"; // Vermelho
        updateLockIcon(false); // Atualiza o ícone para bloqueado
        disableLoginButton(); // Desabilita o botão de login
        return;
    }

    // Envia a senha para o servidor para validação
    try {
        const response = await fetch('valida_senha', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `cpf=${encodeURIComponent(cpf)}&senha=${encodeURIComponent(senha)}`
        });

        const data = await response.json();

        if (data.status === 'valid') {
            senhaInput.classList.add("valid");
            senhaMessage.textContent = "Senha válida!";
            senhaMessage.style.color = "#038b37"; // Verde
            senhaMessage.classList.add("visible");
            inputFieldSenha.classList.add("with-message");
            updateLockIcon(true); // Atualiza o ícone para desbloqueado
            enableLoginButton(); // Habilita o botão de login
        } else {
            senhaInput.classList.add("invalid");
            senhaMessage.textContent = data.message || "Senha inválida!";
            senhaMessage.style.color = "red"; // Vermelho
            senhaMessage.classList.add("visible");
            inputFieldSenha.classList.add("with-message");
            updateLockIcon(false); // Atualiza o ícone para bloqueado
            disableLoginButton(); // Desabilita o botão de login
        }
    } catch (error) {
        console.error("Erro ao validar a senha:", error);
        senhaInput.classList.add("invalid");
        senhaMessage.textContent = "Erro ao validar a senha. Tente novamente.";
        senhaMessage.style.color = "red"; // Vermelho
        senhaMessage.classList.add("visible");
        inputFieldSenha.classList.add("with-message");
        updateLockIcon(false); // Atualiza o ícone para bloqueado
        disableLoginButton(); // Desabilita o botão de login
    }
});


// Adiciona o evento de clique no botão de login para iniciar a animação "Carregando"
loginButton.addEventListener("click", (event) => {
    // Impede o envio imediato do formulário
    event.preventDefault();

    // Desabilita o botão para evitar múltiplos cliques
    loginButton.setAttribute("disabled", "true");

        // Muda a cor do botão para #1f90f2 quando ele está desabilitado
        loginButton.style.backgroundColor = "#1f90f2"; // Define a cor do botão

    
    loginButton.textContent = "VERIFICANDO"; // Altera o texto do botão para "Carregando"
    loginButton.style.color = "#ffffff"; // Define a cor do texto para branco

    // Adiciona o efeito dos pontos "..."
    let dots = 0;
    const interval = setInterval(() => {
        dots = (dots + 1) % 4; // Alterna entre 0, 1, 2, e 3 pontos
        loginButton.textContent = "VERIFICANDO" + ".".repeat(dots);
    }, 200); // Atualiza a cada 500ms

    // Simula o envio após 3 segundos (ajuste para enviar antes)
    setTimeout(() => {
        clearInterval(interval); // Para o efeito de animação
        loginButton.textContent = "SUCESSO"; // Mensagem final opcional
        // Agora submete o formulário após o efeito de "Carregando"
        document.getElementById("loginForm").submit(); // Submete o formulário
    }, 2500); // Ajuste o tempo conforme necessário
});

</script>
<script>
    // Função para mover o foco automaticamente para o campo de senha
document.getElementById("cpf").addEventListener("input", function() {
    const cpfValue = this.value.trim();

    // Verifica se o CPF tem 14 caracteres (tamanho padrão do CPF)
    if (cpfValue.length === 14) {
        document.getElementById("senha").focus(); // Move o foco para o campo de senha
    }
});

</script>
</body>
</html>
