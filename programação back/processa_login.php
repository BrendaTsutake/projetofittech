<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $login = trim($_POST["email"] || $_POST["username"]); // Pode ser e-mail ou username
    $senha = trim($_POST["senha"]);

    // Prepara a consulta SQL para verificar o e-mail ou username
    $sql = "SELECT id, nome, senha FROM usuario WHERE email = ? OR username = ?";

    if ($stmt = mysqli_prepare($conexao, $sql)) {
        // Vincula os parâmetros
        mysqli_stmt_bind_param($stmt, "ss", $param_login, $param_login);
        $param_login = $login;

        // Executa a consulta
        if (mysqli_stmt_execute($stmt)) {
            // Obtém o resultado
            mysqli_stmt_store_result($stmt);

            // Verifica se o e-mail ou username existe
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Vincula as variáveis de resultado
                mysqli_stmt_bind_result($stmt, $id, $username, $senha_hash);
                if (mysqli_stmt_fetch($stmt)) {
                    // Verifica a senha
                    if (password_verify($senha, $senha_hash)) {
                        // Senha correta: inicia a sessão
                        $_SESSION["logado"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["nome"] = $username;

                        // Redireciona para a página inicial
                        header("location: index.html");
                        exit;
                    } else {
                        // Senha incorreta
                        $mensagem = "Senha inválida, tente novamente.";
                        $tipo_mensagem = "erro";
                    }
                }
            } else {
                // E-mail ou username não encontrado
                $mensagem = "Nenhuma conta encontrada com este e-mail ou username.";
                $tipo_mensagem = "erro";
            }
        } else {
            $mensagem = "Erro ao executar a consulta: " . mysqli_stmt_error($stmt);
            $tipo_mensagem = "erro";
        }

        // Fecha o statement
        mysqli_stmt_close($stmt);
    } else {
        $mensagem = "Erro ao preparar a consulta: " . mysqli_error($conexao);
        $tipo_mensagem = "erro";
    }
}

// Fecha a conexão
mysqli_close($conexao);
?>