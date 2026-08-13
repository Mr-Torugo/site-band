<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$usuario_id = $_POST['usuario_id'] ?? 0;
$tipo_acao = $_POST['tipo_acao'] ?? '';
$acao_id = $_POST['acao_id'] ?? 0;

if (!$usuario_id || !$tipo_acao || !$acao_id) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos para curtir.']);
    exit;
}

try {
    // Verifica se o usuário já curtiu este post
    $stmt = $pdo->prepare("SELECT id FROM curtidas WHERE usuario_id = ? AND tipo_acao = ? AND acao_id = ?");
    $stmt->execute([$usuario_id, $tipo_acao, $acao_id]);
    $curtida = $stmt->fetch();

    if ($curtida) {
        // Se já curtiu, o clique vai REMOVER a curtida
        $stmt = $pdo->prepare("DELETE FROM curtidas WHERE id = ?");
        $stmt->execute([$curtida['id']]);
        $acao = 'descurtiu';
    } else {
        // Se não curtiu, o clique vai ADICIONAR a curtida
        $stmt = $pdo->prepare("INSERT INTO curtidas (usuario_id, tipo_acao, acao_id) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $tipo_acao, $acao_id]);
        $acao = 'curtiu';
    }

    // Pega o novo total de curtidas para atualizar a tela em tempo real
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM curtidas WHERE tipo_acao = ? AND acao_id = ?");
    $stmt->execute([$tipo_acao, $acao_id]);
    $total = $stmt->fetchColumn();

    echo json_encode(['sucesso' => true, 'acao' => $acao, 'total_curtidas' => $total]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco: ' . $e->getMessage()]);
}
?>