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
    $nome = trim($_POST['nome']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $confirmar_email = trim($_POST['confirmar_email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validação 1: Campos obrigatórios não podem estar vazios
    if (empty($nome) || empty($username) || empty($email) || empty($senha)) {
        die("Erro: Todos os campos principais são obrigatórios. <a href='javascript:history.back()'>Voltar</a>");
    }

    // Validação 2: E-mails devem corresponder
    if ($email !== $confirmar_email) {
        die("Erro: Os e-mails não correspondem. <a href='javascript:history.back()'>Voltar</a>");
    }

    // Validação 3: Senhas devem corresponder
    if ($senha !== $confirmar_senha) {
        die("Erro: As senhas não correspondem. <a href='javascript:history.back()'>Voltar</a>");
    }
    
    //Verificar se username ou e-mail já existem
    $sql_check = "SELECT id FROM usuarios WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        die("Erro: Nome de usuário ou e-mail já cadastrado. <a href='javascript:history.back()'>Voltar</a>");
    }
    $stmt_check->close();

    //Criptografar a Senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    //Inserir no Banco de Dados
    $sql_insert = "INSERT INTO usuarios (nome, username, email, senha) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    
    $stmt_insert->bind_param("ssss", $nome, $username, $email, $senha_hash);

    //Executar, Fazer Auto-Login e Redirecionar
    if ($stmt_insert->execute()) {
        
        // Pega o ID do usuário que acabamos de criar
        $user_id = $conn->insert_id; 
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['nome'] = $nome;

        header("Location: personalizar.php");
        exit; 

    } else {
        echo "Erro ao cadastrar: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}
$conn->close();
?>