// Função para identificar a página atual e adicionar a classe 'active' no menu correto
function setActiveMenu() {
    // Obtém o caminho da página atual
    const currentPage = window.location.pathname.split("/").pop();

    // Seleciona todos os itens de menu (considerando que o <i> será o alvo para 'active')
    const menuItems = document.querySelectorAll('.sidebar .menu-item, .sidebar i.page-link');

    // Itera sobre os itens de menu
    menuItems.forEach(item => {
        const link = item.querySelector('a') || item; // Caso seja um <i> sem <a>, usa o <i> diretamente
        
        // Verifica se o item possui um link com 'href'
        if (link && link.getAttribute('href')) {
            const linkHref = link.getAttribute('href').split("/").pop();

            // Se o caminho da página atual corresponder ao link do menu, adiciona 'active'
            if (currentPage === linkHref) {
                item.classList.add('active'); // Adiciona 'active' ao <i> ou item de menu
            } else {
                item.classList.remove('active'); // Remove 'active' caso contrário
            }
        }
    });
}

// Chama a função logo após o carregamento da página
document.addEventListener('DOMContentLoaded', () => {
    setActiveMenu(); // Ajusta a classe 'active' no menu
    toggleSidebar(); // Garante que o menu inicia recolhido (não expandido)
});

// Garantir que o menu lateral comece recolhido
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggle-icon');
    sidebar.classList.remove('expanded'); // Garante que o menu começa recolhido

    const menuTexts = sidebar.querySelectorAll('.menu-item .menu-text');
    menuTexts.forEach(menuText => {
        menuText.style.display = sidebar.classList.contains('expanded') ? 'flex' : 'none';
    });

    toggleIcon.classList.remove('fi-br-angle-left'); // Remova a classe inicial se necessário
    toggleIcon.classList.add('fi-br-bars-sort'); // Ícone padrão (menu recolhido)
}

// Função de toggle para a visibilidade do submenu
function showSubmenu(element) {
    const submenu = element.querySelector(".submenu-card");
    submenu.style.display = "block";
    setTimeout(() => submenu.style.opacity = "1", 10);
    element.style.height = "200px";
    adjustSidebarHeight();
}

function hideSubmenu(element) {
    const submenu = element.querySelector(".submenu-card");
    submenu.style.opacity = "0";
    setTimeout(() => submenu.style.display = "none", 300);
    element.style.height = "120px";
    adjustSidebarHeight();
}

// Ajusta a altura do menu lateral com base no conteúdo
function adjustSidebarHeight() {
    const sidebar = document.querySelector('.sidebar');
    let totalHeight = 800;
    const cards = sidebar.querySelectorAll('.card');
    cards.forEach(card => totalHeight += card.offsetHeight);
    sidebar.style.height = `${totalHeight}px`;
}

// Função de toggle para o menu de logout
function toggleLogoutMenu() {
    const submenu = document.getElementById('submenu-logout');
    submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
}

// Fechar o submenu quando clicar fora
document.addEventListener('click', function (event) {
    const userProfile = document.querySelector('.user-profile');
    const submenu = document.getElementById('submenu-logout');
    if (!userProfile.contains(event.target)) {
        submenu.style.display = 'none';
    }
});

// Atualiza a hora e data dinamicamente
function atualizarDataHora() {
    const agora = new Date();
    const data = agora.toLocaleDateString('pt-BR');
    const hora = agora.toLocaleTimeString('pt-BR');
    const dateTimeElement = document.getElementById('datetime');
    if (dateTimeElement) dateTimeElement.textContent = `${data} ${hora}`;
}
setInterval(atualizarDataHora, 1000);

// Função para carregar conteúdo via AJAX
$(document).ready(function () {
    function loadPage(page) {
        $('#content').fadeOut(300, function () {
            $.ajax({
                url: page,
                success: function (data) {
                    const newContent = $(data).find('#content').html();
                    $('#content').html(newContent).fadeIn(300);
                    history.pushState({ page: page }, '', page);
                },
                error: function () {
                    $('#content').html('<p>Erro ao carregar a página!</p>').fadeIn(300);
                }
            });
        });
    }

    $('a.page-link').click(function (e) {
        e.preventDefault();
        const page = $(this).attr('href');
        loadPage(page);
    });

    $(window).on('popstate', function () {
        loadPage(location.pathname);
    });
});
