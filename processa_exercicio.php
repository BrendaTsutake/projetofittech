<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_usuario = $_SESSION['id'];
    $frequencia = $_POST['frequencia']; 
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
    $sql = "UPDATE usuarios SET frequencia_exercicio = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmt->bind_param("si", $frequencia, $id_usuario);

    if ($stmt->execute()) {
        header("Location: info.php");
        exit;
    } else {
        echo "Erro ao salvar sua frequência de exercícios: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: exercicio.php");
    exit;
}
?>