function mascaraCPF(cpf) {
    // Remove todos os caracteres que não são números
    let valor = cpf.value.replace(/\D/g, '');
    
    // Aplica a máscara de CPF
    if (valor.length <= 11) {
        cpf.value = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }
}
