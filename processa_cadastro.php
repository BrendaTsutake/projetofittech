<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once "conexao.php"; // (Este deve ser o arquivo MySQLi que corrigimos)

// Variáveis para as mensagens
$mensagem = "";
$tipo_mensagem = "erro"; // Por padrão, qualquer mensagem é um erro

// Verifica se a requisição HTTP foi feita usando o método POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Coleta e Limpeza de Dados ---
    // (Usamos ?? '' para evitar erros caso um campo não venha)
    $nome = $_POST['nome'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $confirmar_email = $_POST['confirmar_email'] ?? ''; // Campo NOVO
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // --- 2. Validação dos Campos ---
    if (
        strlen(trim($nome)) == 0 ||
        strlen(trim($username)) == 0 ||
        strlen(trim($email)) == 0 ||
        strlen(trim($confirmar_email)) == 0 ||
        strlen(trim($senha)) == 0 ||
        strlen(trim($confirmar_senha)) == 0
    ) {
        $mensagem = "Erro: Preencha todos os campos obrigatórios.";
    } 
    // Validação de e-mail (NOVA)
    elseif (trim($email) !== trim($confirmar_email)) {
        $mensagem = "Erro: Os e-mails informados não coincidem.";
    }
    // Validação de senha (Existente)
    elseif (trim($senha) !== trim($confirmar_senha)) {
        $mensagem = "Erro: As senhas informadas não coincidem.";
    } 
    else {
        // --- 3. Processamento do Banco de Dados ---
        // Se todas as validações passaram
        
        // Hash da senha para armazenamento seguro
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Limpa os dados para inserir
        $param_nome = trim($nome);
        $param_username = trim($username);
        $param_email = trim($email);

        // SQL (SEM 'telefone')
        $sql = "INSERT INTO usuario (nome, username, email, senha) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conexao, $sql)) {
            // Vincula os parâmetros (s = string)
            mysqli_stmt_bind_param($stmt, "ssss", $param_nome, $param_username, $param_email, $senha_hash);

            // Tenta executar a consulta
            if (mysqli_stmt_execute($stmt)) {
                // SUCESSO!
                // Redireciona para a página de login (ex: index.html)
                header("Location: index.html"); 
                exit; // Encerra o script após o redirecionamento
            } else {
                // Tratamento de erro para e-mail ou username duplicados
                if (mysqli_stmt_errno($stmt) == 1062) {
                    $mensagem = "Erro: O e-mail ou username já existe.";
                } else {
                    $mensagem = "Erro ao cadastrar o usuário: " . mysqli_stmt_error($stmt);
                }
            }
            // Fecha o statement
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = "Erro ao preparar a consulta: " . mysqli_error($conexao);
        }
    }
} else {
    // Se não for POST, define uma mensagem de erro genérica
    $mensagem = "Erro: O formulário não foi enviado corretamente.";
}

// --- 4. Exibição de Mensagens (SE HOUVE ERRO) ---
// Se $mensagem não estiver vazia, exibe o alerta
if (!empty($mensagem)) {
    
    $cor_alerta = ($tipo_mensagem == "sucesso") ? "success" : "danger";

    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Aviso de Cadastro</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style> body { background-color: #FFF9EA; } </style>
    </head>
    <body>
        <div class='container mt-5 text-center'>
            <div class='alert alert-$cor_alerta' role='alert'>
                <h4>$mensagem</h4>
            </div>
            <a href='cadastro.html' class='btn btn-primary'>Voltar ao Cadastro</a>
        </div>
    </body>
    </html>";
}

// Fecha a conexão
mysqli_close($conexao);
?>