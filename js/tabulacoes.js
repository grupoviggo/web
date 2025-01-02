// Referências aos campos
const tabulacao = document.getElementById('tabulacao');
const subStatus = document.getElementById('sub_status');
const descricao = document.getElementById('descricao');

// Definição das opções
const options = {
    AG_RETORNO: {
        sub_status: [
            { value: 'pendente_consultor', text: 'Pendente Consultor' },
            { value: 'pendente_supervisor', text: 'Pendente Supervisor' },
            { value: 'pendente_gerente', text: 'Pendente Gerente' }
        ],
        descricao: {
            pendente_consultor: [
                { value: 'plano_reprovado', text: 'Plano Reprovado' },
                { value: 'sem_contato', text: 'Sem Contato' }
            ],
            pendente_supervisor: [
                { value: 'falta_documentacao', text: 'Falta Documentação' }
            ]
        }
    },
    CANCELADO: [
        { value: 'desistencia', text: 'Desistência' },
        { value: 'plano_incorreto', text: 'Plano Incorreto' }
    ],
    AUDITADA: [
        { value: 'erro_sistemico', text: 'Erro Sistêmico' },
        { value: 'aguardando_documento', text: 'Aguardando Documento' }
    ]
};

// Resetar campos ao fechar o modal
document.getElementById('tabularModal').addEventListener('hidden.bs.modal', () => {
    tabulacao.value = '';
    resetSubStatusDescricao();
});

// Evento principal
tabulacao.addEventListener('change', () => {
    const value = tabulacao.value;
    resetSubStatusDescricao();

    if (value === 'AG_RETORNO') {
        populateOptions(subStatus, options[value].sub_status);
        subStatus.disabled = false;

        subStatus.addEventListener('change', () => {
            const subValue = subStatus.value;
            resetDescricao();
            if (options[value].descricao[subValue]) {
                populateOptions(descricao, options[value].descricao[subValue]);
                descricao.disabled = false;
            }
        });
    } else if (options[value]) {
        populateOptions(descricao, options[value]);
        descricao.disabled = false;
    }
});

// Funções auxiliares
function populateOptions(select, items) {
    select.innerHTML = '<option value="">Selecione</option>';
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.value;
        option.textContent = item.text;
        select.appendChild(option);
    });
}

function resetSubStatusDescricao() {
    subStatus.innerHTML = '<option value="">Selecione</option>';
    descricao.innerHTML = '<option value="">Selecione</option>';
    subStatus.disabled = true;
    descricao.disabled = true;
}

function resetDescricao() {
    descricao.innerHTML = '<option value="">Selecione</option>';
    descricao.disabled = true;
}
