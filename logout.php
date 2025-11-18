<?php
// 1. Inicia a sessão
session_start();

// 2. Remove todas as variáveis de sessão
$_SESSION = array();

// 3. Destrói a sessão
session_destroy();

// 4. Redireciona para a página de login
header("Location: login.html");
exit;
?>