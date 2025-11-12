<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
// Se 'loggedin' não existir ou for falso, redireciona para o login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Se o script chegou até aqui, o usuário está logado!
// Podemos usar os dados da sessão, como o nome.
$nome_usuario = htmlspecialchars($_SESSION['nome']); // htmlspecialchars por segurança
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
    </style>
</head>
<body>
    <h1>Bem-vindo, <?php echo $nome_usuario; ?>!</h1>
    <p>Você está logado no sistema.</p>
    
    <a href="logout.php">Sair (Logout)</a>
</body>
</html>