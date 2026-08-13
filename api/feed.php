<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$usuario_id = $_POST['usuario_id'] ?? $_GET['usuario_id'] ?? 0;

try {
    $sql = "
        SELECT 
            a.id AS id_acao,
            a.id AS adesivo_id, /* 👈 ID do Adesivo */
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
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'novo_adesivo' AND acao_id = a.id AND usuario_id = :uid1) AS curtido_por_mim,
            (SELECT COUNT(*) FROM comentarios WHERE tipo_acao = 'novo_adesivo' AND acao_id = a.id) AS total_comentarios
        FROM adesivos a
        JOIN usuarios u ON a.criador_id = u.id

        UNION ALL

        SELECT 
            d.id AS id_acao,
            d.adesivo_id AS adesivo_id, /* 👈 ID do Adesivo */
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
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'descoberta' AND acao_id = d.id AND usuario_id = :uid2) AS curtido_por_mim,
            (SELECT COUNT(*) FROM comentarios WHERE tipo_acao = 'descoberta' AND acao_id = d.id) AS total_comentarios
        FROM descobertas d
        JOIN usuarios u ON d.descobridor_id = u.id
        JOIN adesivos a ON d.adesivo_id = a.id

        UNION ALL

        SELECT 
            um.id AS id_acao,
            NULL AS adesivo_id, /* Medalha não tem mapa */
            'conquista' AS tipo_acao,
            u.apelido AS nome_usuario,
            um.nome AS nome_local, 
            um.icone AS foto, 
            NULL AS tipo_registro,
            um.data_conquista AS data_acao,
            um.descricao AS comentario,
            'Conquista' AS categoria,
            'Tesouro' AS raridade, 
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'conquista' AND acao_id = um.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'conquista' AND acao_id = um.id AND usuario_id = :uid3) AS curtido_por_mim,
            (SELECT COUNT(*) FROM comentarios WHERE tipo_acao = 'conquista' AND acao_id = um.id) AS total_comentarios
        FROM usuario_medalhas um
        JOIN usuarios u ON um.usuario_id = u.id

        UNION ALL

        SELECT 
            mc.id AS id_acao,
            NULL AS adesivo_id, /* Missão não tem mapa */
            'missao' AS tipo_acao,
            u.apelido AS nome_usuario,
            'Missão da Semana' AS nome_local, 
            '🎯' AS foto, 
            NULL AS tipo_registro,
            mc.data_conclusao AS data_acao, 
            'Ganhou +' || mc.xp_ganho || ' XP!' AS comentario,
            'Missão' AS categoria,
            'Lendário' AS raridade, 
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'missao' AND acao_id = mc.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE tipo_acao = 'missao' AND acao_id = mc.id AND usuario_id = :uid4) AS curtido_por_mim,
            (SELECT COUNT(*) FROM comentarios WHERE tipo_acao = 'missao' AND acao_id = mc.id) AS total_comentarios
        FROM missoes_concluidas mc
        JOIN usuarios u ON mc.usuario_id = u.id

        ORDER BY data_acao DESC
        LIMIT 50
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'uid1' => $usuario_id,
        'uid2' => $usuario_id,
        'uid3' => $usuario_id,
        'uid4' => $usuario_id
    ]);
    
    $feed = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso' => true, 
        'dados' => $feed
    ]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro do Banco: ' . $e->getMessage()]);
}
?>