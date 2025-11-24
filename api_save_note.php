<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }

$data = json_decode(file_get_contents('php://input'), true);
$id_usuario = $_SESSION['id'];
$data_nota = $data['data'];
$texto = $data['texto'];

$conn = new mysqli("localhost", "root", "", "mydb");

// Verifica se já existe nota nesse dia
$check = $conn->prepare("SELECT id FROM calendario_notas WHERE id_usuario = ? AND data_nota = ?");
$check->bind_param("is", $id_usuario, $data_nota);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Atualiza
    $stmt = $conn->prepare("UPDATE calendario_notas SET texto = ? WHERE id_usuario = ? AND data_nota = ?");
    $stmt->bind_param("sis", $texto, $id_usuario, $data_nota);
} else {
    // Insere nova
    $stmt = $conn->prepare("INSERT INTO calendario_notas (id_usuario, data_nota, texto) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_usuario, $data_nota, $texto);
}

$stmt->execute();
echo json_encode(['success' => true]);
?>