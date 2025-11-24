<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

// Pega o ID enviado pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$id_refeicao = $data['id'];
$id_usuario = $_SESSION['id'];

// Conexão com o banco
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha na conexão']);
    exit;
}

// Prepara e executa o SQL DELETE
$sql = "DELETE FROM refeicoes WHERE id = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_refeicao, $id_usuario);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>