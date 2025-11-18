<?php
// Iniciar a sessão
session_start();

// Configurações do Banco de Dados
$servername = "localhost"; 
$username_db = "root";   
$password_db = "";         
$dbname = "mydb";          

//Criar a Conexão
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

//Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

//Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Obter dados do formulário
    $login_identifier = $_POST['username'];
    $senha_digitada = $_POST['senha'];

    //Preparar a consulta SQL (Prevenção contra SQL Injection)
    //procurar o usuário pelo 'username' OU pelo 'email'
    $sql = "SELECT id, nome, username, senha FROM usuarios WHERE username = ? OR email = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // "ss" informa que estamos passando dois parâmetros do tipo string
    $stmt->bind_param("ss", $login_identifier, $login_identifier);
    
    //Executar e obter resultados
    $stmt->execute();
    $result = $stmt->get_result();

    //Verificar se o usuário foi encontrado (deve ser 1 resultado)
    if ($result->num_rows == 1) {
        
        //Usuário encontrado, buscar os dados
        $user = $result->fetch_assoc();
        $senha_hash_db = $user['senha']; // Pega a senha criptografada do banco

        // Verificar a senha
        // Compara a senha digitada ($senha_digitada) com a senha hash ($senha_hash_db)
        if (password_verify($senha_digitada, $senha_hash_db)) {

            
            // Armazenar dados do usuário na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nome'] = $user['nome'];

            // Redirecionar para uma página protegida 
            header("Location: paginicial.php"); // Você precisará criar esta página
            exit; // Garante que o script pare após o redirecionamento

        } else {
            // Senha incorreta
            echo "Usuário ou senha inválidos. <a href='login.html'>Tentar novamente</a>";
        }

    } else {
        // Usuário não encontrado (0 resultados ou mais de 1, o que não deve acontecer)
        echo "Usuário ou senha inválidos. <a href='login.html'>Tentar novamente</a>";
    }

    // Fechar o statement
    $stmt->close();

} else {
    // Se alguém tentar acessar login.php diretamente sem enviar o formulário
    header("Location: login.html");
    exit;
}

// Fechar a conexão
$conn->close();
?>