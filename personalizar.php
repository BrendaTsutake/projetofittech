<?php
// Inicia a sessão
session_start();

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não estiver logado, expulsa ele de volta para o login
    header("Location: login.html");
    exit;
}

$nome_usuario = htmlspecialchars($_SESSION['nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar plano</title>
</head>

<style>
    @font-face {
        font-family: "Louis George Cafe";
        src: url(fontes/louis_george_cafe/Louis\ George\ Cafe\ Light.ttf) format("truetype");
    }

    @font-face {
    font-family: "mousse";
    src:
    url("fontes/mousse/Mousse-Regular.otf") format("otf");
    }

        
    body {
        font-family: 'Louis George Cafe', Arial, sans-serif;
        font-weight: 500; 
        font-size: 22px;
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

    .personalizar p{
        text-align: center;
        font-size: 50px;
        margin-top: 30px;
        margin-bottom: 0;
        color: #86A754;
        font-family: 'mousse', Arial, sans-serif;
    }

    .explicando p {
        margin-top: 0;
        text-align: center;
        font-size: 22px;
    }

    .botao {
        margin-top: 20px;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .botao button {
        background-color: #9EC662;
        color: #FFF9EA;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 25px;
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
        width: 500px;
        padding: 15px;
        box-sizing: border-box;
        border: 1px solid #9EC662;
        border-radius: 10px;
        text-align: center;
        font-size: 20px;
        font-family: 'Louis George Cafe', Arial, sans-serif;
        color: #333;
        background-color: transparent;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        box-shadow: 0 3px 3px #F8694D40;
        }

    .option-btn.selecionado {
        background-color: #9EC662;
        color: #FFF9EA;
        border-color: #9EC662;
    }
    </style>

<body>

    <div class="logo">
        <img src="img/Group 70.png" alt="Logo FitTech"> 
    </div>

    <div class="personalizar">
        <p>Personalizar Plano</p>
    </div>

    <div class="explicando">
        <p>Personalizar seu plano através das suas informações.
            Encontre uma forma de manter a vida saudável.</p>
    </div>

    <form id="metasForm" action="processa_personalizar.php" method="POST">

        <div class="opcoes">
            <button type="button" class="option-btn" data-value="iniciar-vida-saudavel">Iniciar a vida saudável</button>
            <button type="button" class="option-btn" data-value="manter-vida-saudavel">Manter a vida saudável</button>
            <button type="button" class="option-btn" data-value="ganho-massa-muscular">Ganho de massa muscular</button>
            <button type="button" class="option-btn" data-value="perder-peso">Perder peso</button>
            <button type="button" class="option-btn" data-value="melhoria-qualidade-sono">Melhoria da qualidade de sono</button>
            <button type="button" class="option-btn" data-value="prevencao-doencas">Prevenção de doenças</button>
            <button type="button" class="option-btn" data-value="aumento-autoestima">Aumento da autoestima</button>
            <button type="button" class="option-btn" data-value="outros">Outros</button>
        </div>

        <input type="hidden" name="metas_selecionadas" id="metas_selecionadas_hidden">

        <div class="botao">
            <button type="submit" id="avancarBtn">Avançar</button>
        </div>

    </form> </body>

<script>
    // Seu script de selecionar/deselecionar
    const todosOsBotoes = document.querySelectorAll('.option-btn');
    todosOsBotoes.forEach(function(botao) {
        botao.addEventListener('click', function() {
            botao.classList.toggle('selecionado');
        });
    });

    // O JavaScript que salva os dados
    document.getElementById('metasForm').addEventListener('submit', function(event) {
        
        // Encontra os botões selecionados
        const botoesSelecionados = document.querySelectorAll('.option-btn.selecionado');
        const valoresSelecionados = [];
        
        // Pega o "data-value" de cada um
        botoesSelecionados.forEach(function(botao) {
            valoresSelecionados.push(botao.dataset.value);
        });

        // Verifica se pelo menos um foi selecionado
        if (valoresSelecionados.length > 0) {
            
            // Se sim, junta os valores
            const valoresString = valoresSelecionados.join(',');
            
            // Coloca essa string no campo invisível
            document.getElementById('metas_selecionadas_hidden').value = valoresString;
            
            // 6. Permite o envio do formulário


        } else {
            // 7. Se não selecionou nada, impede o envio e avisa
            event.preventDefault(); 
            alert('Por favor, selecione ao menos uma opção.');
        }
    });
</script>
</html>