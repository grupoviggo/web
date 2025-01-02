<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu Hora a Hora</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #013565;
    }
    nav {
        background-color: #051729d7;
        position: fixed;
        width: 100%;
        top: 0;
        }

        .container {
        max-width: 800px;
        margin: 100px auto;
        text-align: center;
        background-color: #042049;
        border-radius: 20px; 
        padding: 20px; 
        box-shadow: 0px 10px 20px rgba(0, 128, 255, 0.4);
}
            /* Estilos personalizados para o botão SAIR */
            .btn-voltar {
            color: #013565; /* Cor do texto branca */
            font-weight: bold; /* Peso da fonte em negrito */
        }

    .imagem {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto 20px;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px;
        border-radius: 20px;
        text-decoration: none;
        color: #ffffff;
        font-weight: bold;
    }

    .black-button {
        background-color: #9027F3;
    }

    .blue-button {
        background-color: #118DFF;
    }

    .orange-button {
        background-color: #E66C37;

    }

    p{
        font-weight: bold;
        color: #c8cbca; 
    }

    a {
        
        text-decoration: none;
        color: #fff;
    }

    a:hover {
        text-decoration: none;
        color: #fff;
       
    }

    a:visited {
        text-decoration: none;
        color: #fff;
       
    }
    hr {
    border: none; 
    height: 1.5px; 
    background-color: #165C81; 
    margin: 20px 0; 
    opacity: 0.2;
}

</style>
</head>
<body>
    <!-- Barra de navegação Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark">
        <!-- Adiciona a imagem ao lado esquerdo -->
        <a class="navbar-brand" href="menu">
        <img src="./img/nexuslogin.png"  width="auto" height="25px" alt="">
    </a>
    <!-- Coloque o conteúdo do formulário dentro da classe 'collapse navbar-collapse' -->
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <!-- Use o 'form-inline' dentro de um 'li' para manter o botão alinhado à direita -->
                <form class="form-inline my-2 my-lg-0">
                    <button class="btn btn-warning btn-sm btn-voltar" type="submit" onclick="window.location.href='comercial'; return false;">VOLTAR</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
    <div class="container">
        <br>
        <img class="imagem" src="./img/LogoHxH.png" alt="logotipo" width="400px" style="filter: invert(100%);">
        <p><img src="./img/relogio.png" height="23px">&nbsp;<time id="hora"></time>&nbsp;&nbsp;<span id="diaSemana"></span>&nbsp;&nbsp;<span id="dataFormatada"></span></p>
        <hr>
        <br><br>
        <div>
            <a href="horaresumo" class="button black-button">RESUMO REGIONAL     </a>
            <a href="horasp" class="button blue-button">SÃO PAULO DDD 11    </a>
            <a href="horafsp" class="button orange-button">SP DDD 13 - 19 & FSP</a>
            <br><br><br>
        </div>
    </div>
    <!-- Adicione o link para o Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
// Função para atualizar a hora, dia da semana e data
function atualizarHora() {
    var agora = new Date();
    var horas = agora.getHours();
    var minutos = agora.getMinutes();
    var dia = agora.getDate();
    var mes = agora.getMonth(); // Lembrando que os meses começam em zero (janeiro é 0)
    var ano = agora.getFullYear();
    var horaString = "";
    var diaString = "";
    var dataFormatada = "";

    // Adicionando um zero à esquerda para minutos menores que 10
    minutos = minutos < 10 ? "0" + minutos : minutos;

    // Verificar se a hora é menor que 12h
    if (horas < 12) {
        horaString = "FECHAMENTO -";
        // Obtendo o dia da semana anterior
        var diasDaSemana = ["DOMINGO", "SEGUNDA-FEIRA", "TERÇA-FEIRA", "QUARTA-FEIRA", "QUINTA-FEIRA", "SEXTA-FEIRA", "SÁBADO"];
        var diaAnterior;
        if (agora.getDay() === 1) {
            diaAnterior = new Date(agora.getTime() - 2 * 24 * 60 * 60 * 1000); // se for segunda, pega a data de 2 dias atrás
        } else {
            diaAnterior = new Date(agora.getTime() - 1 * 24 * 60 * 60 * 1000); // pega a data de 1 dia atrás
        }
        dia = diaAnterior.getDate();
        mes = diaAnterior.getMonth();
        diaString = diasDaSemana[diaAnterior.getDay()];
    } else {
        horaString = horas + "H";
        // Obtendo o dia da semana atual
        var diasDaSemana = ["Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado"];
        var hoje = agora.getDay();
        diaString = diasDaSemana[hoje];
    }

    // Obtendo o nome do mês
    var nomesMeses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    var nomeMes = nomesMeses[mes];

    // Formatando a data no formato desejado
    dataFormatada = dia + "/" + nomeMes.substring(0, 3); // Pegando os 3 primeiros caracteres do nome do mês

    document.getElementById("hora").textContent = horaString;
    document.getElementById("diaSemana").textContent = diaString;
    document.getElementById("dataFormatada").textContent = dataFormatada;
}

// Chamada inicial para exibir os dados ao carregar a página
atualizarHora();

// Atualizar a cada hora
setInterval(atualizarHora, 3600000);

</script>
</body>
</html>