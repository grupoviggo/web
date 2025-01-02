function loadSubMenu(button, codigoVenda, statusVenda) {
    // Fechar submenus anteriores
    document.querySelectorAll('.sub_pos-container').forEach(submenu => submenu.remove());

    // Identificar o contêiner do submenu
    const containerId = `sub_pos-container-${codigoVenda}`;
    let submenuContainer = document.createElement('div');
    submenuContainer.id = containerId;
    submenuContainer.className = 'sub_pos-container';

    // Adicionar o submenu ao body
    document.body.appendChild(submenuContainer);

    // Posicionar o submenu ao lado do botão
    const buttonRect = button.getBoundingClientRect();
    submenuContainer.style.position = 'absolute';
    submenuContainer.style.top = `${buttonRect.top + window.scrollY}px`;
    submenuContainer.style.left = `${buttonRect.right + 1}px`;

    // Fazer a requisição POST para carregar o submenu
    fetch('submenu_posvenda', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `codigo_venda=${encodeURIComponent(codigoVenda)}&status_venda=${encodeURIComponent(statusVenda)}`
    })
    .then(response => response.text())
    .then(data => {
        submenuContainer.innerHTML = data;

        // Adicionar evento para fechar o submenu ao clicar fora
        document.addEventListener('click', handleClickOutside);
    })
    .catch(error => {
        submenuContainer.innerHTML = '<p>Erro ao carregar submenu.</p>';
        console.error('Erro:', error);
    });
}

function showObservationBox(option, codigoVenda, button) {
    // Fechar caixas de observação anteriores
    document.querySelectorAll('.observation-box').forEach(box => box.remove());

    // Criar e posicionar a caixa de observação
    const observationBox = document.createElement('div');
    observationBox.className = 'observation-box';
    observationBox.innerHTML = `
        <textarea placeholder="Observação..." required></textarea>
        <button onclick="submitObservation('${option}', '${codigoVenda}')">OK</button>
        <div class="error-message" style="color: red; display: none;">Este campo é obrigatório!</div>
    `;

    // Posicionar ao lado do botão do subitem
    const buttonRect = button.getBoundingClientRect();
    observationBox.style.position = 'absolute';
    observationBox.style.top = `${buttonRect.top + window.scrollY}px`;
    observationBox.style.left = `${buttonRect.right + 10}px`;

    document.body.appendChild(observationBox);

    // Criar um estilo para a textarea (a borda vermelha será mostrada apenas quando o campo for inválido)
    const textarea = observationBox.querySelector('textarea');
    textarea.addEventListener('input', () => {
        // Remover a borda vermelha e a mensagem de erro quando o usuário começar a digitar
        if (textarea.value.trim() !== "") {
            textarea.style.border = '';
            observationBox.querySelector('.error-message').style.display = 'none';
        }
    });
}

function submitObservation(option, codigoVenda) {
    const observationTextarea = document.querySelector('.observation-box textarea');
    const observationMessage = document.querySelector('.observation-box .error-message');

    // Verificar se a textarea está vazia
    if (observationTextarea.value.trim() === "") {
        // Mostrar a borda vermelha e a mensagem de erro
        observationTextarea.style.border = '2px solid red';
        observationMessage.style.display = 'block';
        return; // Impede a execução da função se o campo estiver vazio
    }

    // Se o campo não estiver vazio, prosseguir com o envio da observação
    const observation = observationTextarea.value;

    // Criar o modal dinamicamente
    const modalId = `observationModal-${codigoVenda}`;
    const modalHTML = `
    <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="${modalId}Label" style="color: black;">VENDA TABULADA COM SUCESSO!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Código da Venda:</strong> ${codigoVenda}</p>
                    <p><strong>Opção:</strong> ${option}</p>
                    <p><strong>Observação:</strong> ${observation}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Finalizar</button>
                </div>
            </div>
        </div>
    </div>
    `;

    // Adicionar o modal ao body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Mostrar o modal
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();

    // Remover o modal após o fechamento
    const modalElement = document.getElementById(modalId);
    modalElement.addEventListener('hidden.bs.modal', () => {
        modalElement.remove();
    });
}

// Fechar o submenu ao clicar fora
function handleClickOutside(event) {
    const submenus = document.querySelectorAll('.sub_pos-container, .observation-box');
    let isClickInside = false;

    submenus.forEach(menu => {
        if (menu.contains(event.target)) {
            isClickInside = true;
        }
    });

    if (!isClickInside) {
        // Remover todos os submenus e caixas de observação
        submenus.forEach(menu => menu.remove());
        document.removeEventListener('click', handleClickOutside);
    }
}
