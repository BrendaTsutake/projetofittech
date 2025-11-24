<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id']; 
    
    // Pega os checkboxes
    $restricoes_array = [];
    if (isset($_POST['restricoes'])) {
        $restricoes_array = $_POST['restricoes'];
    }

    // Pega o texto da alergia 
    $alergia_detalhes = trim($_POST['alergia_detalhes']);
    
    // Adiciona a alergia ao array, se ela não estiver vazia
    if (!empty($alergia_detalhes)) {
        $restricoes_array[] = $alergia_detalhes;
    }

    // Junta tudo em uma única string
    $restricoes_string = null;
    if (!empty($restricoes_array)) {
        $restricoes_string = implode(',', $restricoes_array);
    }

    // Conectar ao Banco de Dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Preparar o SQL
    $sql = "UPDATE usuarios SET restricoes = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("si", $restricoes_string, $id_usuario);

    // Executar e Redirecionar
    if ($stmt->execute()) {
        header("Location: exercicio.php");
        exit;
    } else {
        echo "Erro ao salvar suas restrições: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: restricoes.php");
    exit;
}
?>