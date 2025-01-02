// Seleciona os elementos do chatbox e botão
const ajudaBtn = document.getElementById("ajuda-btn");
const chatbox = document.getElementById("chatbox");
const chatboxClose = document.getElementById("chatbox-close");
const sendBtn = document.getElementById("send-btn");
const userInput = document.getElementById("user-input");
const chatboxMessages = document.getElementById("chatbox-messages");

// Exibir o chatbox ao clicar no botão "Ajuda"
ajudaBtn.addEventListener("click", () => {
    chatbox.style.display = "block";
});

// Enviar mensagem ao clicar no botão "Enviar"
sendBtn.addEventListener("click", () => {
    sendMessage();
});

// Enviar mensagem ao pressionar "Enter"
userInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
        sendMessage();
    }
});

// Fechar o chatbox ao clicar no botão de fechar
chatboxClose.addEventListener("click", () => {
    chatbox.style.display = "none";
});

// Função para enviar mensagem
function sendMessage() {
    const message = userInput.value.trim(); // Remove espaços em branco
    if (message) {
        // Adicionar a mensagem do usuário no chatbox
        addMessage(message, "user");
        userInput.value = ""; // Limpa o campo de entrada

        // Simular resposta automática da assistente após 1 segundo
        setTimeout(() => {
            addMessage("Ainda estou sendo desenvolvida, em breve poderei te ajudar!, até logo", "nila");
        }, 1000);
    }
}

// Função para adicionar mensagens ao chatbox
function addMessage(text, sender) {
    // Cria o contêiner da mensagem
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("message", sender);

    // Cria o avatar
    const avatarDiv = document.createElement("div");
    avatarDiv.classList.add("avatar");

    if (sender === "nila") {
        avatarDiv.innerHTML = '<img src="../img/avatar.png" alt="Avatar Nila">';
    } else {
        avatarDiv.innerHTML = `<img src="${userAvatar}" style="cursor: border: 3px solid #ffa500;" alt="Avatar do Usuário">`;
    }

    // Cria o texto da mensagem
    const textDiv = document.createElement("div");
    textDiv.classList.add("text");
    textDiv.textContent = text;

    // Adiciona o avatar e o texto ao contêiner da mensagem
    messageDiv.appendChild(avatarDiv);
    messageDiv.appendChild(textDiv);

    // Adiciona a mensagem ao contêiner de mensagens
    chatboxMessages.appendChild(messageDiv);

    // Rolagem automática para exibir a última mensagem
    chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
}
// Garante que o script seja executado após o carregamento completo da página
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("user-input").focus();
});
