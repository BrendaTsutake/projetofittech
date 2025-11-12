<?php
// 1. Iniciar a sessão (necessário para o auto-login)
session_start();

// Configurações do Banco de Dados
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "mydb";

// 2. Criar a Conexão
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// 3. Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Obter os dados do formulário
    // Usamos trim() para remover espaços em branco no início/fim
    $nome = trim($_POST['nome']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $confirmar_email = trim($_POST['confirmar_email']);
    $senha = $_POST['confirmar_senha']; // Senha não deve ter trim
    $confirmar_senha = $_POST['confirmar_senha'];

    // 5. VALIDAÇÕES DO SERVIDOR
    
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
    
    // 6. Verificar se username ou e-mail já existem
    $sql_check = "SELECT id FROM usuarios WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        die("Erro: Nome de usuário ou e-mail já cadastrado. <a href='javascript:history.back()'>Voltar</a>");
    }
    $stmt_check->close();

    // 7. Criptografar a Senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 8. Inserir no Banco de Dados
    $sql_insert = "INSERT INTO usuarios (nome, username, email, senha) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    
    $stmt_insert->bind_param("ssss", $nome, $username, $email, $senha_hash);

    // 9. Executar, Fazer Auto-Login e Redirecionar
    if ($stmt_insert->execute()) {
        
        // Pega o ID do usuário que acabamos de criar
        $user_id = $conn->insert_id; 

        // Guarda os dados na sessão (Logando o usuário)
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['nome'] = $nome;

        // Redireciona para personalizar.php (lembre-se de proteger essa página)
        header("Location: personalizar.php");
        exit; 

    } else {
        echo "Erro ao cadastrar: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}

// Fechar a conexão
$conn->close();
?>