<?php
// Inicia a sessão
session_start();

// Verifica se o usuário não está logado
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
    <title>Preenchendo informações</title>
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
    
    .container {
        width: 100%;
        max-width: 600px; 
        text-align: center; 
    }

    .logo img {
        display: block;
        margin: 0 auto;
        margin-top: 0;
        width: 250px;
        margin-left: auto;
        margin-right: auto;
    }

    .form-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .form-section label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 24px;
    }


    input[type="number"],
    input[type="text"],
    select { 
        width: 100%;
        max-width: 350px;
        margin: 8px auto;
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-family: 'Louis George Cafe', sans-serif; 
        font-size: 18px; 
        background-color: white;
    }
        
    input::placeholder {
        font-family: 'Louis George Cafe', sans-serif;
    }

    .gender-options {
        display: flex; 
        justify-content: center;
        gap: 20px; 
        margin-bottom: 20px;
    }

    .checkbox-group {
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .gender-option {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border: solid 2px #F8694D;
        border-radius: 8px;
        width: 200px; 
        height: 200px;
        transition: background-color 0.3s, transform 0.2s, border-color 0.3s;
    }

    .gender-option:hover {
        background-color: #fde8e4;
        transform: scale(1.05);
    }

    /*Estilo para quando o gênero é selecionado */
    .gender-option.selected {
        background-color: #fde8e4;
        border: 4px solid #e05a3f; 
        transform: scale(1.05);
    }

    .gender-option img {
        width: 80px; 
    }

    .checkbox-group label {
        color: #F8694D;
        font-size: 18px;
        font-weight: lighter;
    }
        
    input[type="checkbox"] {
        margin-right: 8px;
        transform: scale(1.2); 
    }

    .botao button {
        background-color: #F8694D;
        color: #FFF9EA;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 19px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.16);
        margin-top: 10px;
        font-family: 'Louis George Cafe', Arial, sans-serif;
        transition: background-color 0.3s;
    }

    .botao button:hover {
        background-color: #e05a3f;
    }

    .titulo{
        font-family: 'mousse';
        color: #F8694D;
        font-weight: lighter;
        font-size: 40px;
    }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <img src="img/logo.png" alt="Logo">
        </div>

        <div class="titulo">
            <h3>Preencha os campos</h3>
        </div>

        <form id="infoForm" method="POST" action="processa_info.php">
            <div class="form-section">
                <label for="altura">Qual a sua altura?</label>
                <input type="number" id="altura" name="altura" min="50" max="250" placeholder="em cm" required>
            </div>

            <div class="form-section">
                <label for="peso">Quanto você pesa?</label>
                <input type="text" id="peso" name="peso" pattern="[0-9]+([,\.][0-9]+)?" placeholder="em kg (ex: 60.5)" required>
            </div>

            <div class="form-section">
                <label for="meta">Qual a sua meta?</label>
                
                <select id="meta" name="objetivo" required>
                    <option value="" disabled selected>Selecione uma opção</option>
                    <option value="Manter peso">Manter peso</option>
                    <option value="Perder peso">Perder peso</option>
                    <option value="Ganhar peso">Ganhar peso</option>
                </select>

            </div>

            <div class="form-section">
                <label for="idade">Qual sua idade?</label>
                <input type="number" id="idade" name="idade" min="10" max="100" required>
            </div>

            <div class="form-section">
                <label>Qual seu gênero?</label>
                <div class="gender-options">
                    <div class="gender-option" data-value="feminino">
                        <img src="img/mulher.png" alt="Feminino">
                    </div>
                    <div class="gender-option" data-value="masculino">
                        <img src="img/homem.png" alt="Masculino">
                    </div>
                </div>
                <input type="hidden" name="genero" id="genero_hidden">
                
                <div class="checkbox-group">
                    <input type="checkbox" id="nao-dizer">
                    <label for="nao-dizer">Prefiro não dizer</label>
                </div>
            </div>

            <div class="botao">
                <button type="submit" class="btn">Começar</button>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('infoForm');
        const genderOptions = document.querySelectorAll('.gender-option');
        const naoDizerCheckbox = document.getElementById('nao-dizer');
        const hiddenInput = document.getElementById('genero_hidden');

        let generoSelecionado = "";

        genderOptions.forEach(option => {
            option.addEventListener('click', () => {
                genderOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                naoDizerCheckbox.checked = false;
                generoSelecionado = option.dataset.value;
            });
        });

        // Adiciona clique no checkbox
        naoDizerCheckbox.addEventListener('click', () => {
            if (naoDizerCheckbox.checked) {
                // Se marcar o checkbox, remove a seleção visual das imagens
                genderOptions.forEach(opt => opt.classList.remove('selected'));
                // Define o valor
                generoSelecionado = "nao-dizer";
            } else {
                // Se desmarcar, limpa o valor
                generoSelecionado = "";
            }
        });

        // Valida antes de enviar
        form.addEventListener('submit', (event) => {
            if (generoSelecionado) {
                hiddenInput.value = generoSelecionado;
            } else {
                event.preventDefault();
                alert('Por favor, selecione uma opção de gênero.');
            }
        });
    </script>
</body>
</html>