<?php
session_start();
// 1. Proteção
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// 2. Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Obter TODOS os dados do formulário de edição
    $id_usuario = $_SESSION['id'];
    $peso_atual_input = $_POST['peso_atual'];
    $peso_inicial_input = $_POST['peso_inicial']; // <-- NOVO
    $objetivo = $_POST['objetivo'];

    // 4. Limpar os pesos
    $peso_atual = str_replace(',', '.', $peso_atual_input);
    $peso_inicial = str_replace(',', '.', $peso_inicial_input); // <-- NOVO

    // 5. Conexão
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // 6. SQL ATUALIZADO (agora inclui peso_inicial)
    $sql = "UPDATE usuarios SET peso_atual = ?, peso_inicial = ?, objetivo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // "ddsi" = double, double, string, integer (TIPO ATUALIZADO)
    $stmt->bind_param("ddsi", $peso_atual, $peso_inicial, $objetivo, $id_usuario);

    // 7. Executar e voltar
    if ($stmt->execute()) {
        header("Location: progresso.php");
        exit;
    } else {
        echo "Erro ao atualizar perfil: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: progresso.php");
    exit;
}
?>