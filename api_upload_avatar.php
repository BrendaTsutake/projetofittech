<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { exit; }

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

$id_usuario = $_SESSION['id'];
$uploadDir = 'uploads/avatars/';
$fileName = basename($_FILES["avatarFile"]["name"]);
$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$newFileName = $id_usuario . '_' . time() . '.' . $fileType;
$targetFilePath = $uploadDir . $newFileName;

$allowTypes = array('jpg','png','jpeg');
if(in_array($fileType, $allowTypes) && $_FILES["avatarFile"]["size"] > 0){
    if(move_uploaded_file($_FILES["avatarFile"]["tmp_name"], $targetFilePath)){
        $sql = "UPDATE usuarios SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $targetFilePath, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}
$conn->close();
header("Location: perfil.php");
?>