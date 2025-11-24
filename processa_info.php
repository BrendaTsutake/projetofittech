<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_usuario = $_SESSION['id']; 
    $altura = $_POST['altura'];
    $peso_input = $_POST['peso'];
    $objetivo = $_POST['objetivo'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];

    $peso = str_replace(',', '.', $peso_input);
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