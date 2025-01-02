// Script para ocultar o trecho específico do código-fonte ao inspecionar a página
document.addEventListener("DOMContentLoaded", function() {
    // Se o usuário tentar visualizar o código-fonte da página
    document.addEventListener("keydown", function(event) {
        // Se as teclas "Ctrl + U" ou "Cmd + Option + U" forem pressionadas
        if ((event.ctrlKey || event.metaKey) && event.key === "u") {
            // Oculta o trecho do código-fonte
            document.querySelector('.iframe-container').style.display = 'none';
            // Mostra uma mensagem de alerta ao usuário
            alert("Não é possível visualizar o código-fonte desta página.");
            // Evita que o comportamento padrão seja executado
            event.preventDefault();
        }
    });
});

