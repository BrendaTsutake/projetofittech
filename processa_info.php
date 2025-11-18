<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obter os dados
    $id_usuario = $_SESSION['id']; 
    
    $altura = $_POST['altura'];
    $peso_input = $_POST['peso']; // Este é o peso que o usuário acabou de digitar
    $objetivo = $_POST['objetivo'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];

    // Limpar o peso
    $peso = str_replace(',', '.', $peso_input);

    // Conectar ao Banco de Dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Preparar o SQL para atualizar o usuário
    $sql = "UPDATE usuarios SET 
                altura = ?, 
                peso_atual = ?, 
                peso_inicial = ?, 
                objetivo = ?, 
                idade = ?, 
                genero = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // "iddsisi" = int, double, double, str, int, str, int
    $stmt->bind_param("iddsisi", $altura, $peso, $peso, $objetivo, $idade, $genero, $id_usuario);

    // Executar e Redirecionar
    if ($stmt->execute()) {
        // Sucesso! Redireciona para a página principal
        header("Location: paginicial.php");
        exit;
    } else {
        echo "Erro ao salvar suas informações: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: info.php");
    exit;
}
?>