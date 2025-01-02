<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hierarquia Dinâmica</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .coordenador { background: #003366; color: white; padding: 10px; margin-top: 20px; font-size: 18px; }
        .supervisor { background: #4CAF50; color: white; padding: 8px; margin-top: 10px; font-size: 16px; }
        .consultor { padding-left: 20px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Hierarquia de Colaboradores</h1>
    <div id="hierarquia"></div>

    <script>
        // Carrega os dados da hierarquia
        fetch('https://viggonexus.online/pgi/hierarquia') // Use HTTPS
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('hierarquia');

        for (const coordenador in data) {
            const coordDiv = document.createElement('div');
            coordDiv.className = 'coordenador';
            coordDiv.textContent = coordenador;

            for (const supervisor in data[coordenador]) {
                const supDiv = document.createElement('div');
                supDiv.className = 'supervisor';
                supDiv.textContent = supervisor;

                data[coordenador][supervisor].forEach(consultor => {
                    const consDiv = document.createElement('div');
                    consDiv.className = 'consultor';
                    consDiv.textContent = consultor;
                    supDiv.appendChild(consDiv);
                });

                coordDiv.appendChild(supDiv);
            }

            container.appendChild(coordDiv);
        }
    })
    .catch(error => console.error('Erro ao carregar hierarquia:', error));

    </script>
</body>
</html>
