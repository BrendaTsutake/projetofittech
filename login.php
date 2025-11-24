<?php
session_start();
$servername = "localhost"; 
$username_db = "root";   
$password_db = "";         
$dbname = "mydb";          

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login_identifier = $_POST['username'];
    $senha_digitada = $_POST['senha'];

    //Preparar a consulta SQL
    $sql = "SELECT id, nome, username, senha FROM usuarios WHERE username = ? OR email = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmt->bind_param("ss", $login_identifier, $login_identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $senha_hash_db = $user['senha'];

        if (password_verify($senha_digitada, $senha_hash_db)) {

            
            // Armazenar dados do usuário na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nome'] = $user['nome'];

            // Redirecionar para uma página protegida 
            header("Location: paginicial.php"); 
            exit; 

        } else {
            // Senha incorreta
            echo "Usuário ou senha inválidos. <a href='login.html'>Tentar novamente</a>";
        }

    } else {
        // Usuário não encontrado (0 resultados ou mais de 1, o que não deve acontecer)
        echo "Usuário ou senha inválidos. <a href='login.html'>Tentar novamente</a>";
    }
    $stmt->close();

} else {
    header("Location: login.html");
    exit;
}
$conn->close();
?>