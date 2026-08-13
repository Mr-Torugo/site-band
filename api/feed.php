<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$usuario_id = $_POST['usuario_id'] ?? $_GET['usuario_id'] ?? 0;

try {
    $sql = "
        SELECT 
            a.id AS id_acao,
            'novo_adesivo' AS tipo_acao,
            u.apelido AS nome_usuario,
            a.nome_local,
            a.foto_original AS foto,
            NULL AS tipo_registro,
            a.data_criacao AS data_acao,
            NULL AS comentario,
            a.categoria,
            a.raridade,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'novo_adesivo' AND acao_id = a.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'novo_adesivo' AND acao_id = a.id AND usuario_id = :uid) AS curtido_por_mim,
            (SELECT COUNT(*) FROM comentarios WHERE tipo_acao = 'novo_adesivo' AND acao_id = a.id) AS total_comentarios
        FROM adesivos a
        JOIN usuarios u ON a.criador_id = u.id

        UNION ALL

        SELECT 
            d.id AS id_acao,
            'descoberta' AS tipo_acao,
            u.apelido AS nome_usuario,
            a.nome_local,
            d.foto_selfie AS foto,
            d.tipo_registro,
            d.data_descoberta AS data_acao,
            d.comentario,
            a.categoria,
            a.raridade,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'descoberta' AND acao_id = d.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'descoberta' AND acao_id = d.id AND usuario_id = :uid) AS curtido_por_mim,
            (SELECT COUNT(*) FROM comentarios WHERE tipo_acao = 'descoberta' AND acao_id = d.id) AS total_comentarios
        FROM descobertas d
        JOIN usuarios u ON d.descobridor_id = u.id
        JOIN adesivos a ON d.adesivo_id = a.id

        ORDER BY data_acao DESC
        LIMIT 50
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $usuario_id]);
    $feed = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso' => true, 
        'dados' => $feed
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false, 
        'erro' => 'Erro ao carregar o feed: ' . $e->getMessage()
    ]);
}
?>