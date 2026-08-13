<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$usuario_id = $_POST['usuario_id'] ?? 0;
$tipo_acao = $_POST['tipo_acao'] ?? '';
$acao_id = $_POST['acao_id'] ?? 0;
$comentario = trim($_POST['comentario'] ?? '');

if (!$usuario_id || !$tipo_acao || !$acao_id || empty($comentario)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Não é possível enviar comentário vazio.']);
    exit;
}

try {
    // Insere o comentário no banco de dados
    $stmt = $pdo->prepare("INSERT INTO comentarios (usuario_id, tipo_acao, acao_id, comentario) VALUES (?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $tipo_acao, $acao_id, $comentario]);
    
    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco: ' . $e->getMessage()]);
}
?>