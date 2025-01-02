document.addEventListener('DOMContentLoaded', function () {
        // Seleciona o(s) input(s) onde a máscara será aplicada
        const dateInputs = document.querySelectorAll('.date-mask');

        dateInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove qualquer caractere que não seja número
                if (value.length > 8) value = value.slice(0, 8); // Limita o tamanho a 8 caracteres (DDMMYYYY)

                // Aplica a máscara conforme os números são digitados
                if (value.length >= 5) {
                    value = value.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3'); // Formato DD/MM/AAAA
                } else if (value.length >= 3) {
                    value = value.replace(/(\d{2})(\d{2})/, '$1/$2'); // Formato DD/MM
                }

                e.target.value = value;
            });

            input.addEventListener('blur', function () {
                // Validação básica ao perder o foco
                const value = e.target.value;
                const datePattern = /^\d{2}\/\d{2}\/\d{4}$/; // Regex para validar o formato DD/MM/AAAA
                if (value && !datePattern.test(value)) {
                    alert('Por favor, insira uma data válida no formato DD/MM/AAAA.');
                    e.target.focus();
                }
            });
        });
    });
