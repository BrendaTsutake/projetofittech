<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { exit; }

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

$id_usuario = $_SESSION['id'];
$uploadDir = 'uploads/avatars/'; // Pasta que criamos
$fileName = basename($_FILES["avatarFile"]["name"]);
$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Cria um nome de arquivo único (ex: 1_1678886400.jpg)
$newFileName = $id_usuario . '_' . time() . '.' . $fileType;
$targetFilePath = $uploadDir . $newFileName;

// Validação simples de tipo
$allowTypes = array('jpg','png','jpeg');
if(in_array($fileType, $allowTypes) && $_FILES["avatarFile"]["size"] > 0){
    // Move o arquivo do cache do PHP para nossa pasta
    if(move_uploaded_file($_FILES["avatarFile"]["tmp_name"], $targetFilePath)){
        // Atualiza o caminho no banco de dados
        $sql = "UPDATE usuarios SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $targetFilePath, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}
$conn->close();
header("Location: perfil.php"); // Volta para o perfil
?>