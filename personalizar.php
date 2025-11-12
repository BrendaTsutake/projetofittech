<?php
// Inicia a sessão
session_start();

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não estiver logado, expulsa ele de volta para o login
    header("Location: login.html");
    exit;
}

// Se chegou aqui, o usuário está logado!
// Você pode até dar boas-vindas:
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
        <img src="img/logo.png" alt="Logo FitTech">
    </div>

        <div class="personalizar">
            <p>Personalizar Plano</p>
        </div>

        <div class="explicando">
            <p>Personalizar seu plano através das suas informações. 
            Encontre uma forma de manter a vida saudável.</p>
        </div>

        <div class="opcoes">
            <button class="option-btn" data-value="iniciar-vida-saudavel">Iniciar a vida saudável</button>
            <button class="option-btn" data-value="manter-vida-saudavel">Manter a vida saudável</button>
            <button class="option-btn" data-value="ganho-massa-muscular">Ganho de massa muscular</button>
            <button class="option-btn" data-value="perder-peso">Perder peso</button>
            <button class="option-btn" data-value="melhoria-qualidade-sono">Melhoria da qualidade de sono</button>
            <button class="option-btn" data-value="prevencao-doencas">Prevenção de doenças</button>
            <button class="option-btn" data-value="aumento-autoestima">Aumento da autoestima</button>
            <button class="option-btn" data-value="outros">Outros</button>
        </div>

        <div class="botao">
            <button type="button" onclick="window.location.href='restricoes.html'">Avançar</button>
        </div>
    </body>
    
    <script>
        
        const todosOsBotoes = document.querySelectorAll('.option-btn');

        // 2. Adiciona um evento de clique para cada um deles
        todosOsBotoes.forEach(function(botao) {
            botao.addEventListener('click', function() {
                // 3. A mágica acontece aqui:
                // Adiciona a classe 'selecionado' se não tiver, e remove se já tiver.
                botao.classList.toggle('selecionado');
            });
        });

        document.getElementById('avancarBtn').addEventListener('click', function() {
            // Encontra todos os botões que TÊM a classe 'selecionado'
            const botoesSelecionados = document.querySelectorAll('.option-btn.selecionado');
            
            const valoresSelecionados = [];
            
            botoesSelecionados.forEach(function(botao) {
                valoresSelecionados.push(botao.dataset.value);
            });

            if (valoresSelecionados.length > 0) {
                alert('Opções selecionadas: ' + valoresSelecionados.join(', '));
                console.log('Opções selecionadas:', valoresSelecionados);
            } else {
                alert('Por favor, selecione ao menos uma opção.');
            }
        });
    </script>
</html>