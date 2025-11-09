<?php
/* Arquivo: conexao.php 
 Conexão usando a biblioteca MySQLi
*/

// --- Configure seus dados de acesso abaixo ---
$servername = "localhost";    // Geralmente é "localhost" ou "127.0.0.1"
$username   = "root";         // Seu usuário do XAMPP (padrão é "root")
$password   = "";             // Sua senha do XAMPP (padrão é "")
$dbname     = "mydb";         // O nome do seu banco de dados (pelas suas imagens)
// ----------------------------------------------


// Tenta criar a conexão
$conexao = mysqli_connect($servername, $username, $password, $dbname);

// Verifica se a conexão falhou
if (!$conexao) {
    // Interrompe o script e exibe o erro
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define o charset para UTF-8 (para suportar acentos e caracteres especiais)
mysqli_set_charset($conexao, "utf8");

?>