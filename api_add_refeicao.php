<?php
session_start();
// Proteção
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

// Pega os dados enviados pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);

$id_usuario = $_SESSION['id'];
$data_refeicao = $data['data_refeicao'];
$tipo_refeicao = $data['tipo_refeicao'];
$nome_alimento = $data['nome_alimento'];
$quantidade = $data['quantidade'];
$unidade = $data['unidade']; // NOVO
$kcal = $data['kcal'];

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

// Prepara e insere (SQL ATUALIZADO)
$sql = "INSERT INTO refeicoes (id_usuario, data_refeicao, tipo_refeicao, nome_alimento, quantidade, unidade, kcal) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
// "isssdsi" = i, str, str, str, double, str, i (TIPO ATUALIZADO)
$stmt->bind_param("isssdsi", $id_usuario, $data_refeicao, $tipo_refeicao, $nome_alimento, $quantidade, $unidade, $kcal);

if ($stmt->execute()) {
    $new_id = $conn->insert_id;
    // Devolve o item que acabou de criar (como JSON)
    header('Content-Type: application/json');
    echo json_encode([
        'id' => $new_id,
        'id_usuario' => $id_usuario,
        'data_refeicao' => $data_refeicao,
        'tipo_refeicao' => $tipo_refeicao,
        'nome_alimento' => $nome_alimento,
        'quantidade' => $quantidade, // NOME ATUALIZADO
        'unidade' => $unidade,     // NOVO
        'kcal' => $kcal
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>