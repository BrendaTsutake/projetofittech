<?php
// --- BLOCO DE SEGURANÇA ---
// Desativa a exibição de erros na tela (eles quebram o JSON)
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

try {
    // 1. Verifica Login
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        throw new Exception('Usuário não logado');
    }

    // 2. Recebe os dados
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Nenhum dado recebido');
    }

    // 3. Prepara variáveis
    $id_usuario = $_SESSION['id'];
    $data_refeicao = $data['data_refeicao'];
    $tipo_refeicao = $data['tipo_refeicao'];
    $nome_alimento = $data['nome_alimento'];
    $quantidade = floatval($data['quantidade']);
    $kcal = intval($data['kcal']);
    
    // Define unidade padrão se não vier
    $unidade = (isset($data['unidade']) && !empty($data['unidade'])) ? $data['unidade'] : 'unid';

    // 4. Conexão
    $conn = new mysqli("localhost", "root", "", "mydb");
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }

    // 5. SQL
    $sql = "INSERT INTO refeicoes (id_usuario, data_refeicao, tipo_refeicao, nome_alimento, quantidade, unidade, kcal) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erro na preparação do SQL: ' . $conn->error);
    }

    $stmt->bind_param("isssdsi", $id_usuario, $data_refeicao, $tipo_refeicao, $nome_alimento, $quantidade, $unidade, $kcal);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Erro ao executar SQL: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Se der qualquer erro, devolve um JSON limpo com a mensagem
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>