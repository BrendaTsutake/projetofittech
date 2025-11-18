<?php
session_start();
// 1. Segurança
if (!isset($_SESSION['loggedin'])) {
    http_response_code(403);
    exit;
}

// 2. Recebe os dados
$data = json_decode(file_get_contents('php://input'), true);
$id_usuario = $_SESSION['id'];
$data_nota = $data['data']; // A data para apagar

// 3. Conecta e Apaga
$conn = new mysqli("localhost", "root", "", "mydb");

$sql = "DELETE FROM calendario_notas WHERE id_usuario = ? AND data_nota = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_usuario, $data_nota);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $conn->error]);
}
?>