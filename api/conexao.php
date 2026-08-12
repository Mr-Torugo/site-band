<?php
session_start(); // LIGA O MOTOR DE SESSÕES SEGURAS DO PHP

// O "../" faz o PHP sair da pasta 'api' e procurar a pasta 'database'
$db_file = __DIR__ . '/../database/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==========================================
    // ATUALIZADOR AUTOMÁTICO DO BANCO
    // Tenta adicionar as colunas novas caso o banco seja antigo.
    // ==========================================
    try { $pdo->exec("ALTER TABLE usuarios ADD COLUMN email TEXT"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE usuarios ADD COLUMN senha TEXT"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE usuarios ADD COLUMN is_admin INTEGER DEFAULT 0"); } catch (Exception $e) {}

} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha crítica na conexão com o banco de dados.']);
    exit;
}
?>