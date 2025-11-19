<?php
// Inicia a sessão
session_start();

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequência de exercício físico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" href="img/logo-img.png" type="image/png">
    <style>
    @font-face {
        font-family: "Louis George Cafe";
        src: url(fontes/louis_george_cafe/Louis\ George\ Cafe\ Light.ttf) format("truetype");
    }
    @font-face {
        font-family: "mousse";
        src: url("fontes/mousse/Mousse-Regular.otf") format("otf");
    }
    body {
        font-family: 'Louis George Cafe', Arial, sans-serif;
        font-weight: 500; 
        font-size: 20px;
        background-color: #FFF9EA;
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .logo img {
        display: block;
        margin: 0 auto;
        margin-top: 0;
        width: 250px;
        margin-left: auto;
        margin-right: auto;
    }
    .titulo p {
        text-align: center;
        font-size: 50px;
        margin-top: 50px;
        font-family: 'Louis George Cafe', Arial, sans-serif;
    }
    .textinho p {
        margin-top: 0;
        text-align: center;
        font-size: 22px;
        margin-bottom: 60px;
    }
    .opcoes {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
    }
    .botao {
        margin-top: 20px;
        width: 100%;
        display: flex;
        justify-content: center;
        gap: 24px; 
    }
    .botao button {
        background-color: #F8694D;
        color: #FFF9EA;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 22px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.16);
        margin-top: 30px;
        font-family: 'Louis George Cafe', Arial, sans-serif;
    }
    .opcoes {
        align-items: center;
        display: flex;
        flex-direction: column;
        gap: 15px; 
        margin-top: 30px;
    }

    .option-btn {
        width: 650px;
        padding: 15px;
        box-sizing: border-box;
        border: 1px solid #9EC662;
        color: #333;
        background-color: transparent;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        box-shadow: 0 3px 3px #F8694D40;
        border-radius: 10px;
        text-align: center;
        font-size: 20px;
        font-family: 'Louis George Cafe', Arial, sans-serif;
    }

    .option-btn.selecionado {
        background-color: #9EC662;
        color: #FFF9EA;
        border-color: #9EC662;
    }

    .option-btn h5 {
        font-weight: bold;
        color: #FFF9EA; /* Garante que o H5 mude de cor também */
    }
    /* O H5 do botão não selecionado precisa ser escuro */
    .option-btn:not(.selecionado) h5 {
        color: #333;
    }

    </style>
</head>
<body>

    <div class="logo">
        <img src="img/logo.png" alt="Logo"> </div>

    <div class="titulo">
        <strong><p>Qual é sua frequência de exercício físico?</p></strong>
    </div>

    <div class="textinho">
        <p>Referem a quantidade e intensidade de movimentos ou exercícios que uma pessoa realiza em sua rotina diária.</p>
    </div>

    <form id="exercicioForm" method="POST" action="processa_exercicio.php">

        <div class="opcoes">
            <button type="button" class="option-btn" data-value="Sedentário"><h5>Sedentário</h5><p>Pessoa que realiza pouca ou nenhuma atividade física no dia a dia.</p></button>
            <button type="button" class="option-btn" data-value="Levemente ativo"><h5>Levemente ativo</h5><p>Pessoa que realiza atividades físicas leves como caminhadas ocasionais.</p></button>
            <button type="button" class="option-btn" data-value="Ativo"><h5>Ativo</h5><p>Pessoa que pratica atividades físicas regulares de intensidade moderada</p></button>
            <button type="button" class="option-btn" data-value="Muito ativo"><h5>Muito ativo</h5><p>Pessoa que realiza atividades físicas intensas com frequência como treinos.</p></button>
        </div>

        <input type="hidden" name="frequencia" id="frequencia_hidden">

        <div class="botao">
            <button type="button" onclick="window.location.href='restricoes.php'">Voltar</button>
            <button type="submit">Avançar</button>
        </div>

    </form> <script>
        const form = document.getElementById('exercicioForm');
        const botoesOpcoes = document.querySelectorAll('.option-btn');
        const campoHidden = document.getElementById('frequencia_hidden');

        let valorSelecionado = "";

        botoesOpcoes.forEach(function(botao) {
            botao.addEventListener('click', function() {
                // Pega o valor do botão clicado
                valorSelecionado = botao.dataset.value;
                
                // Remove a classe 'selecionado' de todos os botões
                botoesOpcoes.forEach(b => b.classList.remove('selecionado'));
                
                // Adiciona a classe 'selecionado' apenas no botão clicado
                botao.classList.add('selecionado');
            });
        });

        // Antes de enviar o formulário
        form.addEventListener('submit', function(event) {
            if (valorSelecionado) {
                // Se um valor foi selecionado, coloca no campo invisível
                campoHidden.value = valorSelecionado;
            } else {
                // Se nada foi selecionado, impede o envio e avisa
                event.preventDefault();
                alert('Por favor, selecione uma opção.');
            }
        });
    </script>
</body>
</html>