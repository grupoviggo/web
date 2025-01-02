// Aguarda o carregamento do DOM
document.addEventListener('DOMContentLoaded', function () {
    const closeButton = document.getElementById('closeModalButton');
    const closeButtonImage = closeButton.querySelector('img');

    // Adiciona o evento de hover para trocar a imagem
    closeButton.addEventListener('mouseenter', () => {
        closeButtonImage.src = '../img/x2.png';
    });

    // Retorna à imagem original quando o mouse sai
    closeButton.addEventListener('mouseleave', () => {
        closeButtonImage.src = '../img/x.png';
    });
});
