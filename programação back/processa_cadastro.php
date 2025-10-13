<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once "conexao.php";

// Verifica se a requisição HTTP foi feita usando o método POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // -- Validação de Entrada --
    // Verifica se os campos obrigatórios foram enviados e não estão vazios.
    if (
        isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['confirmar_senha'], $_POST['username'], $_POST['telefone']) &&
        !empty(trim($_POST['nome'])) &&
        !empty(trim($_POST['email'])) &&
        !empty(trim($_POST['senha'])) &&
        !empty(trim($_POST['confirmar_senha'])) &&
        !empty(trim($_POST['username'])) &&
        !empty(trim($_POST['telefone']))
    ) {
        // -- Coleta e limpeza de Dados --
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);
        $confirmar_senha = trim($_POST['confirmar_senha']);
        $username = trim($_POST['username']);
        $telefone = trim($_POST['telefone']);

        // Verifica se as senhas coincidem
        if ($senha !== $confirmar_senha) {
            $mensagem = "Erro: As senhas não coincidem.";
            $tipo_mensagem = "erro";
        } else {
            // Hash da senha para armazenamento seguro
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // -- Lógica de Inserção no Banco de Dados --
            $sql = "INSERT INTO usuario (nome, email, senha, username, telefone) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($conexao, $sql)) {
                // Vincula os parâmetros
                mysqli_stmt_bind_param($stmt, "sssss", $param_nome, $param_email, $param_senha, $param_username, $param_telefone);

                // Define os valores dos parâmetros
                $param_nome = $nome;
                $param_email = $email;
                $param_senha = $senha_hash;
                $param_username = $username;
                $param_telefone = $telefone;

                // Tenta executar a consulta
                if (mysqli_stmt_execute($stmt)) {
                    $mensagem = "Cadastro concluído com sucesso! Você será redirecionado para a página inicial.";
                    $tipo_mensagem = "sucesso";

                    // Redireciona para a página inicial
                    header("Location: index.html");
                    exit;
                } else {
                    // Tratamento de erro para e-mail ou username duplicados
                    if (mysqli_stmt_errno($stmt) == 1062) {
                        $mensagem = "Erro: O e-mail ou username já existe.";
                    } else {
                        $mensagem = "Erro ao cadastrar o usuário: " . mysqli_stmt_error($stmt);
                    }
                    $tipo_mensagem = "erro";
                }

                // Fecha o statement
                mysqli_stmt_close($stmt);
            } else {
                $mensagem = "Erro ao preparar a consulta: " . mysqli_error($conexao);
                $tipo_mensagem = "erro";
            }
        }
    } else {
        $mensagem = "Erro: Preencha todos os campos obrigatórios e tente novamente.";
        $tipo_mensagem = "erro";
    }
}

// Fecha a conexão
mysqli_close($conexao);
?>