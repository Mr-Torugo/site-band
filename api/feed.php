<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    try { $pdo->exec("CREATE TABLE IF NOT EXISTS curtidas (id INTEGER PRIMARY KEY AUTOINCREMENT, usuario_id INTEGER, tipo_acao TEXT, acao_id INTEGER, data_curtida DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE(usuario_id, tipo_acao, acao_id))"); } catch (Exception $e) {}
    try { $pdo->exec("CREATE TABLE IF NOT EXISTS feed_comentarios (id INTEGER PRIMARY KEY AUTOINCREMENT, usuario_id INTEGER, tipo_acao TEXT, acao_id INTEGER, comentario TEXT, data_comentario DATETIME DEFAULT CURRENT_TIMESTAMP)"); } catch (Exception $e) {}

    $usuario_id = $_GET['usuario_id'] ?? 0;

    $sql = "
        SELECT 
            'colou' AS tipo,
            a.id AS acao_id,
            a.data_criacao AS data_acao,
            u.apelido AS quem_agiu,
            a.nome_local AS local,
            a.raridade AS raridade,
            '' AS dono_adesivo,
            a.foto_original AS foto_adesivo,
            a.foto_original AS foto_registro, 
            'colou' AS tipo_registro,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'colou' AND acao_id = a.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'colou' AND acao_id = a.id AND usuario_id = ?) AS curtiu_mim,
            (SELECT COUNT(*) FROM feed_comentarios WHERE tipo_acao = 'colou' AND acao_id = a.id) AS total_comentarios
        FROM adesivos a
        JOIN usuarios u ON a.criador_id = u.id

        UNION ALL

        SELECT 
            'achou' AS tipo,
            d.id AS acao_id,
            d.data_descoberta AS data_acao,
            u.apelido AS quem_agiu,
            a.nome_local AS local,
            a.raridade AS raridade,
            criador.apelido AS dono_adesivo,
            a.foto_original AS foto_adesivo,
            d.foto_selfie AS foto_registro, 
            d.tipo_registro AS tipo_registro,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'achou' AND acao_id = d.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'achou' AND acao_id = d.id AND usuario_id = ?) AS curtiu_mim,
            (SELECT COUNT(*) FROM feed_comentarios WHERE tipo_acao = 'achou' AND acao_id = d.id) AS total_comentarios
        FROM descobertas d
        JOIN usuarios u ON d.descobridor_id = u.id
        JOIN adesivos a ON d.adesivo_id = a.id
        JOIN usuarios criador ON a.criador_id = criador.id

        ORDER BY data_acao DESC
        LIMIT 50
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id, $usuario_id]);
    $feed = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $feed]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>