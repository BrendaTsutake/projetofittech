<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id']; 
    $metas_string = $_POST['metas_selecionadas'];
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Preparar o SQL para ATUALIZAR (UPDATE) o usuário
    $sql = "UPDATE usuarios SET metas = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmt->bind_param("si", $metas_string, $id_usuario);

    // Executar e Redirecionar
    if ($stmt->execute()) {
        header("Location: restricoes.html");
        exit;
    } else {
        // Falha
        echo "Erro ao salvar suas metas: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: personalizar.php");
    exit;
}
?>