function consultarCEP() {
    const cepField = document.getElementById('cep');
    const cep = cepField.value.replace(/\D/g, ''); // Remove caracteres não numéricos

    // Permite apenas números no campo
    cepField.value = cep;

    // Verifica se o CEP tem 8 dígitos
    if (cep.length === 8) {
        const url = `https://viacep.com.br/ws/${cep}/json/`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    // Preenche os campos automaticamente com os dados retornados pela API
                    document.getElementById('logradouro').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('uf').value = data.uf;
                }
            })
            .catch(err => {
                // Registra o erro no console
                console.log('Erro ao consultar o CEP:', err);
            });

        // Move o foco para o campo número após digitar 8 dígitos no CEP
        document.getElementById('numero').focus(); // Move o foco para o campo "Número"
    }
}

// Adicionando um evento para garantir que só números sejam digitados
document.getElementById('cep').addEventListener('input', function (e) {
    e.target.value = e.target.value.replace(/\D/g, ''); // Remove caracteres não numéricos

    // Quando o campo CEP tiver 8 caracteres, move o foco para o campo número
    if (e.target.value.length === 8) {
        setTimeout(() => {
            document.getElementById('numero').focus();
        }, 100); // Adiciona um pequeno delay para garantir que o valor seja processado
    }
});
