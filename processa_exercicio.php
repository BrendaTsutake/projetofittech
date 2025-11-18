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
    $frequencia = $_POST['frequencia']; 

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
    $sql = "UPDATE usuarios SET frequencia_exercicio = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // "si" = string (frequencia), integer (id)
    $stmt->bind_param("si", $frequencia, $id_usuario);

    // Executar e Redirecionar
    if ($stmt->execute()) {
        // Sucesso! Redireciona para a página principal do app
        header("Location: info.php");
        exit;
    } else {
        echo "Erro ao salvar sua frequência de exercícios: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Se alguém tentar acessar esse arquivo direto, manda de volta
    header("Location: exercicio.php");
    exit;
}
?>