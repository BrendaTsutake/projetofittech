<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }

$id_usuario = $_SESSION['id'];
$mes = $_GET['mes']; 
$ano = $_GET['ano'];

$conn = new mysqli("localhost", "root", "", "mydb");
$sql = "SELECT data_nota, texto FROM calendario_notas 
        WHERE id_usuario = ? AND MONTH(data_nota) = ? AND YEAR(data_nota) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_usuario, $mes, $ano);
$stmt->execute();
$result = $stmt->get_result();

$notas = [];
while($row = $result->fetch_assoc()) {
    $notas[$row['data_nota']] = $row['texto'];
}

echo json_encode($notas);
?>