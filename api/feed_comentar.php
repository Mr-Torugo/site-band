<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    
    // Cria a tabela de comentários do feed silenciosamente
    $pdo->exec("CREATE TABLE IF NOT EXISTS feed_comentarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        tipo_acao TEXT, 
        acao_id INTEGER,
        comentario TEXT,
        data_comentario DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $usuario_id = $_POST['usuario_id'] ?? 0;
    $tipo_acao = $_POST['tipo_acao'] ?? '';
    $acao_id = $_POST['acao_id'] ?? 0;
    $texto = trim($_POST['comentario'] ?? '');

    if (!$usuario_id || !$tipo_acao || !$acao_id || empty($texto)) {
        throw new Exception("Dados inválidos ou comentário vazio.");
    }

    $ins = $pdo->prepare("INSERT INTO feed_comentarios (usuario_id, tipo_acao, acao_id, comentario) VALUES (?, ?, ?, ?)");
    $ins->execute([$usuario_id, $tipo_acao, $acao_id, $texto]);

    echo json_encode(['sucesso' => true]);

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>