<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    $tipo_acao = $_GET['tipo_acao'] ?? '';
    $acao_id = $_GET['acao_id'] ?? 0;

    if (!$tipo_acao || !$acao_id) {
        throw new Exception("Referência da publicação não informada.");
    }

    // Puxa os comentários junto com o nome de quem comentou, ordenado do mais antigo para o mais novo
    $sql = "
        SELECT c.id, c.comentario, c.data_comentario, u.apelido 
        FROM feed_comentarios c
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.tipo_acao = ? AND c.acao_id = ?
        ORDER BY c.data_comentario ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo_acao, $acao_id]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $comentarios]);

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>