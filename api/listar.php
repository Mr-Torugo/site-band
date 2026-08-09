<?php
// Define que a resposta será no formato JSON
header('Content-Type: application/json');

$db_file = 'banco.sqlite';

try {
    // Conecta ao banco de dados SQLite
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Executa a consulta para pegar todos os adesivos
    $stmt = $pdo->query("SELECT * FROM adesivos ORDER BY data_criacao DESC");
    $adesivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os dados em formato JSON
    echo json_encode(['sucesso' => true, 'dados' => $adesivos]);

} catch (Exception $e) {
    // Em caso de erro, retorna status 500
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>