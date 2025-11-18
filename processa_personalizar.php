<?php
// 1. Iniciar a sessão
session_start();

// 2. Verificar se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não estiver logado, não tem como salvar. Expulsa.
    header("Location: login.html");
    exit;
}

// 3. Verificar se o formulário foi enviado 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Obter os dados
    // Pega o ID do usuário que está logado
    $id_usuario = $_SESSION['id']; 
    
    // Pega o texto com as metas 
    $metas_string = $_POST['metas_selecionadas'];

    // Conectar ao Banco de Dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "mydb";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Preparar o SQL para ATUALIZAR (UPDATE) o usuário
    $sql = "UPDATE usuarios SET metas = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // "si" significa que o primeiro '?' é uma string (s) e o segundo é um inteiro (i)
    $stmt->bind_param("si", $metas_string, $id_usuario);

    // Executar e Redirecionar
    if ($stmt->execute()) {
        // Sucesso! Redireciona para a próxima página
        header("Location: restricoes.html");
        exit;
    } else {
        // Falha
        echo "Erro ao salvar suas metas: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Se alguém tentar acessar esse arquivo direto, manda de volta
    header("Location: personalizar.php");
    exit;
}
?>