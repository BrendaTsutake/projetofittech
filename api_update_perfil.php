<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

//Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Obter TODOS os dados do formulário de edição
    $id_usuario = $_SESSION['id'];
    $peso_atual_input = $_POST['peso_atual'];
    $peso_inicial_input = $_POST['peso_inicial']; 
    $objetivo = $_POST['objetivo'];

    //Limpar os pesos
    $peso_atual = str_replace(',', '.', $peso_atual_input);
    $peso_inicial = str_replace(',', '.', $peso_inicial_input); 

    //Conexão
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    $sql = "UPDATE usuarios SET peso_atual = ?, peso_inicial = ?, objetivo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddsi", $peso_atual, $peso_inicial, $objetivo, $id_usuario);

    //Executar e voltar
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