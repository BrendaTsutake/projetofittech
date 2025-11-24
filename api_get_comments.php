<?php
session_start();
header('Content-Type: application/json');

if (!isset($_GET['id_post'])) { 
    echo json_encode([]); 
    exit; 
}

$id_post = intval($_GET['id_post']);
$conn = new mysqli("localhost", "root", "", "mydb");
$sql = "SELECT c.id, c.id_usuario, c.texto, u.username 
        FROM comentarios c 
        JOIN usuarios u ON c.id_usuario = u.id 
        WHERE c.id_post = ? 
        ORDER BY c.data_comentario ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_post);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while($row = $result->fetch_assoc()) {
    $comentarios[] = $row;
}

echo json_encode($comentarios);
$conn->close();
?>