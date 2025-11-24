<?php
session_start();
header('Content-Type: application/json');

// Se não estiver logado, retorna 0
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['total' => 0]);
    exit;
}

// Conexão
$conn = new mysqli("localhost", "root", "", "mydb");
if ($conn->connect_error) { 
    echo json_encode(['total' => 0]); 
    exit; 
}

$id_usuario = $_SESSION['id'];
$data_input = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// --- LÓGICA DA SEMANA (Domingo a Sábado) ---
$dt = new DateTime($data_input);
$dia_semana = $dt->format('w'); // 0 (Domingo) a 6 (Sábado)

// Volta X dias para achar o Domingo (Início)
$inicio = clone $dt;
$inicio->modify("-$dia_semana days");

// Avança para achar o Sábado (Fim)
$fim = clone $inicio;
$fim->modify("+6 days");

$data_inicio = $inicio->format('Y-m-d');
$data_fim = $fim->format('Y-m-d');

// Soma as calorias nesse intervalo
$sql = "SELECT SUM(kcal) as total 
        FROM refeicoes 
        WHERE id_usuario = ? 
        AND data_refeicao BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $id_usuario, $data_inicio, $data_fim);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Se o resultado for nulo (nenhuma refeição), assume 0
$total = $row['total'] !== null ? (int)$row['total'] : 0;

echo json_encode(['total' => $total]);

$stmt->close();
$conn->close();
?>