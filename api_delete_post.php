<?php
session_start();
// 1. Proteção
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403); // Proibido
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

// 2. Pega o ID enviado pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$id_post = $data['id'];
$id_usuario = $_SESSION['id'];

// 3. Conexão
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha na conexão']);
    exit;
}

// 4. PASSO A: Encontrar o caminho do arquivo ANTES de apagar
$sql_find = "SELECT imagem_path FROM postagens WHERE id = ? AND id_usuario = ?";
$stmt_find = $conn->prepare($sql_find);
$stmt_find->bind_param("ii", $id_post, $id_usuario);
$stmt_find->execute();
$result = $stmt_find->get_result();
$post = $result->fetch_assoc();
$stmt_find->close();

$caminho_arquivo = $post ? $post['imagem_path'] : null;

// 5. PASSO B: Apagar o post do Banco de Dados
$sql_delete = "DELETE FROM postagens WHERE id = ? AND id_usuario = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("ii", $id_post, $id_usuario);

if ($stmt_delete->execute()) {
    // 6. PASSO C: Apagar o arquivo do servidor (se ele existir)
    if ($caminho_arquivo && file_exists($caminho_arquivo)) {
        unlink($caminho_arquivo); // Apaga o arquivo
    }
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt_delete->error]);
}

$stmt_delete->close();
$conn->close();
?>