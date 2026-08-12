<?php
// O "../" faz o PHP sair da pasta 'api' e procurar a pasta 'database'
$db_file = __DIR__ . '/../database/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha crítica na conexão com o banco de dados.']);
    exit;
}
?>