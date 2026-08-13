<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$tipo_acao = $_GET['tipo_acao'] ?? '';
$acao_id = $_GET['acao_id'] ?? 0;

if (!$tipo_acao || !$acao_id) {
    echo json_encode(['sucesso' => false, 'erro' => 'Faltam parâmetros para buscar comentários.']);
    exit;
}

try {
    // Busca os comentários deste post específico e junta com o nome de quem comentou
    $sql = "
        SELECT c.*, u.apelido 
        FROM comentarios c
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.tipo_acao = ? AND c.acao_id = ?
        ORDER BY c.data_comentario ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo_acao, $acao_id]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['sucesso' => true, 'dados' => $comentarios]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco: ' . $e->getMessage()]);
}
?>