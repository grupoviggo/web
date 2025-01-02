function mascaraTel(input) {
    let valor = input.value.replace(/\D/g, ''); // Remove tudo o que não for número

    // Aplica a máscara
    if (valor.length <= 2) {
        input.value = `(${valor}`;
    } else if (valor.length <= 6) {
        input.value = `(${valor.slice(0, 2)}) ${valor.slice(2)}`;
    } else {
        input.value = `(${valor.slice(0, 2)}) ${valor.slice(2, 6)}-${valor.slice(6, 10)}`;
    }
}
