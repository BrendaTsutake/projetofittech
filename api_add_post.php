<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { exit; }

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

$id_usuario = $_SESSION['id'];
$caption = $_POST['caption'];
$uploadDir = 'uploads/posts/'; // Pasta que criamos
$fileName = basename($_FILES["postFile"]["name"]);
$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$newFileName = $id_usuario . '_post_' . time() . '.' . $fileType;
$targetFilePath = $uploadDir . $newFileName;

$allowTypes = array('jpg','png','jpeg');
if(in_array($fileType, $allowTypes) && $_FILES["postFile"]["size"] > 0){
    if(move_uploaded_file($_FILES["postFile"]["tmp_name"], $targetFilePath)){
        $sql = "INSERT INTO postagens (id_usuario, imagem_path, caption) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $id_usuario, $targetFilePath, $caption);
        $stmt->execute();
        $stmt->close();
    }
}
$conn->close();
header("Location: perfil.php");
?>