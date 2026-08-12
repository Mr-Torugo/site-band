<?php
// Caminho absoluto para o banco de dados
$db_file = __DIR__ . '/banco.sqlite';

try {
    // Cria a conexão (única para todo o sistema)
    $pdo = new PDO("sqlite:" . $db_file);
    
    // Configura o PDO para sempre avisar quando houver erros no SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Se o banco falhar, devolve um erro bonito em JSON e para tudo na hora
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha crítica na conexão com o banco de dados.']);
    exit;
}
?>