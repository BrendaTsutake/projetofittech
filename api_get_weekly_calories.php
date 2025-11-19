<?php
session_start();
// Proteção de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['total' => 0]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "mydb");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$id_usuario = $_SESSION['id'];
$data_ref = $_GET['date']; // A data que o usuário selecionou no calendário

// 1. Lógica para descobrir o início (Domingo) e fim (Sábado) da semana dessa data
$dateObj = new DateTime($data_ref);
$dia_da_semana = $dateObj->format('w'); // 0 (Domingo) a 6 (Sábado)

// Volta X dias para chegar no Domingo anterior
$inicio_semana = clone $dateObj;
$inicio_semana->modify("-$dia_da_semana days");

// Avança para chegar no Sábado
$fim_semana = clone $inicio_semana;
$fim_semana->modify("+6 days");

$start_str = $inicio_semana->format('Y-m-d');
$end_str = $fim_semana->format('Y-m-d');

// 2. Soma as calorias de TUDO que está entre essas datas
$sql = "SELECT SUM(kcal) as total_semanal 
        FROM refeicoes 
        WHERE id_usuario = ? AND data_refeicao BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $id_usuario, $start_str, $end_str);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Se não tiver refeições, retorna 0, senão retorna a soma
$total = $row['total_semanal'] ? $row['total_semanal'] : 0;

header('Content-Type: application/json');
echo json_encode(['total' => $total]);

$stmt->close();
$conn->close();
?>