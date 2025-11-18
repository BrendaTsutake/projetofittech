<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { exit; }

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];

    $sql = "UPDATE usuarios SET username = ?, bio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $bio, $id_usuario);
    $stmt->execute();
    
    $_SESSION['username'] = $username; // Atualiza a sessão
    
    $stmt->close();
    $conn->close();
    header("Location: perfil.php"); // Volta para o perfil
}
?>