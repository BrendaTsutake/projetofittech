<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
$nome_usuario = htmlspecialchars($_SESSION['nome']);
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