<?php
session_start();
// Proteção
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

// Conexão
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

// Pega os dados do usuário e a data
$id_usuario = $_SESSION['id'];
$data = $_GET['date']; 

// Prepara e executa a busca
$sql = "SELECT id, tipo_refeicao, nome_alimento, quantidade, unidade, kcal 
        FROM refeicoes 
        WHERE id_usuario = ? AND data_refeicao = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_usuario, $data);
$stmt->execute();
$result = $stmt->get_result();

$refeicoes = [];
while ($row = $result->fetch_assoc()) {
    $refeicoes[] = $row;
}

$stmt->close();
$conn->close();

// Devolve os dados como JSON
header('Content-Type: application/json');
echo json_encode($refeicoes);
exit;
?>