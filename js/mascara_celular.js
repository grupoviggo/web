function mascaraCelular(input) {
    let valor = input.value.replace(/\D/g, ''); // Remove tudo o que não for número

    // Aplica a máscara
    if (valor.length <= 2) {
        input.value = `(${valor}`;
    } else if (valor.length <= 6) {
        input.value = `(${valor.slice(0, 2)}) ${valor.slice(2, 3)} ${valor.slice(3)}`;
    } else if (valor.length <= 10) {
        input.value = `(${valor.slice(0, 2)}) ${valor.slice(2, 3)} ${valor.slice(3, 7)}-${valor.slice(7)}`;
    } else {
        input.value = `(${valor.slice(0, 2)}) ${valor.slice(2, 3)} ${valor.slice(3, 7)}-${valor.slice(7, 11)}`;
    }
}
