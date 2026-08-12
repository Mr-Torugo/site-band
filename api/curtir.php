<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS curtidas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        tipo_acao TEXT, 
        acao_id INTEGER,
        data_curtida DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(usuario_id, tipo_acao, acao_id)
    )");

    $usuario_id = $_POST['usuario_id'] ?? 0;
    $tipo_acao = $_POST['tipo_acao'] ?? ''; // Pode ser 'colou' ou 'achou'
    $acao_id = $_POST['acao_id'] ?? 0;      // ID do adesivo ou ID da descoberta

    if (!$usuario_id || !$tipo_acao || !$acao_id) {
        throw new Exception("Dados inválidos para curtir.");
    }

    // Verifica se já curtiu
    $stmt = $pdo->prepare("SELECT id FROM curtidas WHERE usuario_id = ? AND tipo_acao = ? AND acao_id = ?");
    $stmt->execute([$usuario_id, $tipo_acao, $acao_id]);
    $curtida = $stmt->fetch();

    if ($curtida) {
        // Se já tem curtida, o clique significa "Remover curtida"
        $del = $pdo->prepare("DELETE FROM curtidas WHERE id = ?");
        $del->execute([$curtida['id']]);
        echo json_encode(['sucesso' => true, 'acao' => 'descurtiu']);
    } else {
        // Se não tem, insere a curtida
        $ins = $pdo->prepare("INSERT INTO curtidas (usuario_id, tipo_acao, acao_id) VALUES (?, ?, ?)");
        $ins->execute([$usuario_id, $tipo_acao, $acao_id]);
        echo json_encode(['sucesso' => true, 'acao' => 'curtiu']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>