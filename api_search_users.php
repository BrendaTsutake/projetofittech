<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }

$conn = new mysqli("localhost", "root", "", "mydb");
if ($conn->connect_error) { die("Erro: " . $conn->connect_error); }

$termo = isset($_GET['q']) ? $_GET['q'] : '';
$id_logado = $_SESSION['id'];

if (strlen($termo) > 0) {
    $sql = "SELECT id, username, profile_pic FROM usuarios 
            WHERE username LIKE ? AND id != ? LIMIT 5";
    
    $stmt = $conn->prepare($sql);
    $param = "%" . $termo . "%";
    $stmt->bind_param("si", $param, $id_logado);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Garante que a foto não seja nula
        if (empty($row['profile_pic'])) {
            $row['profile_pic'] = 'img/avatar_padrao.png';
        }
        $users[] = $row;
    }
    
    echo json_encode($users);
} else {
    echo json_encode([]);
}
$conn->close();
?>