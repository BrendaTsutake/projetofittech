<?php
session_start();
header('Content-Type: application/json'); 

if (!isset($_SESSION['loggedin'])) { 
    echo json_encode(['success' => false, 'error' => 'Não logado']);
    exit; 
}
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id_post']) || !isset($data['texto'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

$id_usuario = $_SESSION['id'];
$id_post = intval($data['id_post']);
$texto = trim($data['texto']);

if (empty($texto)) {
    echo json_encode(['success' => false, 'error' => 'Texto vazio']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "mydb");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Erro banco']);
    exit;
}

$sql = "INSERT INTO comentarios (id_post, id_usuario, texto) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $id_post, $id_usuario, $texto);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>