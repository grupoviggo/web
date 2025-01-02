function hideNav() {        
    document.querySelector('.navbar').style.display = 'none';
    document.querySelector('.seta-menu').style.display = 'none';
    document.querySelector('.seta-menu2').style.display = 'block';
    document.querySelector('.voltaricon2').style.display = 'block';
    document.querySelector('.voltaricon').style.display = 'none';
    document.querySelector('.homeicon2').style.display = 'block';
    document.querySelector('.homeicon').style.display = 'none';
    document.querySelector('.pontosicon2').style.display = 'block';
    document.querySelector('.pontosicon').style.display = 'none';
    document.querySelector('.menu').style.display = 'block';
    
    const element = document.documentElement;
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.webkitRequestFullscreen) { /* Safari */
        element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) { /* IE11 */
        element.msRequestFullscreen();
    }
}

function showNav() {
    document.querySelector('.navbar').style.display = 'block';
    document.querySelector('.seta-menu2').style.display = 'none';
    document.querySelector('.seta-menu').style.display = 'block';
    document.querySelector('.voltaricon2').style.display = 'none';
    document.querySelector('.voltaricon').style.display = 'block';
    document.querySelector('.homeicon2').style.display = 'none';
    document.querySelector('.homeicon').style.display = 'block';
    document.querySelector('.pontosicon2').style.display = 'none';
    document.querySelector('.pontosicon').style.display = 'block';
    document.querySelector('.menu').style.display = 'none';   
}

window.onload = function() {
    // Adiciona o evento de clique na imagem para ocultar o menu e ativar o modo de tela cheia
    document.querySelector('.seta-menu').addEventListener('click', function() {
        hideNav();
    });

    // Adiciona o evento de clique à imagem com class 'homeicon'
    document.querySelector('.homeicon').addEventListener('click', function() {
    // Redireciona o usuário para a página diretoria.php
    window.location.href = 'menu.php';
    });

    // Adiciona o evento de clique à imagem com class 'homeicon'
    document.querySelector('.voltaricon').addEventListener('click', function() {
    // Redireciona o usuário para a página diretoria.php
    window.location.href = 'diretoria.php';
    });

    // Adiciona o evento de clique na imagem para sair do modo de tela cheia e mostrar a navegação
    document.querySelector('.seta-menu2').addEventListener('click', function() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        showNav();
    });

    // Mantém o menu sempre visível
    showNav();

    // Verifica se é um dispositivo móvel e redireciona se for
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        window.location.href = 'directbi.html';
    }
};


