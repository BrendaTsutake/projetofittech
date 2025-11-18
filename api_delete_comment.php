<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin'])) { 
    echo json_encode(['success' => false]);
    exit; 
}

$data = json_decode(file_get_contents('php://input'), true);
$id_comentario = intval($data['id_comentario']);
$id_usuario = $_SESSION['id'];

$conn = new mysqli("localhost", "root", "", "mydb");

// Deleta APENAS se o id_usuario bater com o dono da sessão
$sql = "DELETE FROM comentarios WHERE id = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_comentario, $id_usuario);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$conn->close();
?>