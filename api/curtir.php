<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    // 1. TRAVA DE SEGURANÇA: Se não tem sessão ativa, bloqueia a requisição!
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(401);
        throw new Exception("Acesso Negado. Você não está logado.");
    }

    // 2. PEGA O ID BLINDADO DO SERVIDOR (Hacker não consegue falsificar isso)
    $usuario_id = $_SESSION['usuario_id'];
    
    $tipo_acao = $_POST['tipo_acao'] ?? '';
    $acao_id = $_POST['acao_id'] ?? 0;

    if (!$tipo_acao || !$acao_id) {
        throw new Exception("Dados inválidos para curtir.");
    }

    // Cria a tabela de curtidas silenciosamente caso não exista
    $pdo->exec("CREATE TABLE IF NOT EXISTS curtidas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        tipo_acao TEXT, 
        acao_id INTEGER,
        data_curtida DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(usuario_id, tipo_acao, acao_id)
    )");

    // Verifica se já curtiu
    $stmt = $pdo->prepare("SELECT id FROM curtidas WHERE usuario_id = ? AND tipo_acao = ? AND acao_id = ?");
    $stmt->execute([$usuario_id, $tipo_acao, $acao_id]);
    $curtida = $stmt->fetch();

    if ($curtida) {
        // Remover curtida
        $del = $pdo->prepare("DELETE FROM curtidas WHERE id = ?");
        $del->execute([$curtida['id']]);
        echo json_encode(['sucesso' => true, 'acao' => 'descurtiu']);
    } else {
        // Inserir curtida
        $ins = $pdo->prepare("INSERT INTO curtidas (usuario_id, tipo_acao, acao_id) VALUES (?, ?, ?)");
        $ins->execute([$usuario_id, $tipo_acao, $acao_id]);
        echo json_encode(['sucesso' => true, 'acao' => 'curtiu']);
    }

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>