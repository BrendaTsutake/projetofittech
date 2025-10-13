<?php
// Define constantes para os detalhes da conexão com o banco de dados
// DB_SERVER: endereço dp servidor onde o MySQL está rodando
define('DB_SERVER', 'localhost');

// DB_USERNAME: Nome do usuário para acessar o banco de dados MySQL
define('DB_USERNAME', 'root');

//DB_PASSWORD: Senha do usuário do banco de dados 
define('DB_PASSWORD', '');

//DB_NAME: Recebe o nome do banco de dados que criamos 
define('DB_NAME', 'diagrama tcc');

// Tenta estabelecer a conexão com o servidor usando a função mysqli_connect()
// Passa o servidor, usuario, senha e o nome do banco de dados 
$conexao = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD,DB_NAME);

//Verifica se a conexão falhou
// A funçao mysqli_connect() retorna 'false' em caso de erro

if($conexao === false){
    //se a conexão falhar, termina a execuçao do script (die) e exibe uma mensagem de erro
    // mysqli_connect_error() retorna a descrição do último erro de conexão
    die ("ERRO: Não foi possível conectar ao banco de dados. " . mysqli_connect_error());
}

// define o conjuntode caracteres da conexão para UTF-8
// isso é crucial para garantir que acentos e caracteres especiais(ç, á, é, etc)
// sejam armazenados e recuperados corretamente do banco de dados

mysqli_set_charset($conexao, "utf8");
