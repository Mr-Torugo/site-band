<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    $sql = "
        SELECT 
            'colou' AS tipo,
            a.data_criacao AS data_acao,
            u.apelido AS quem_agiu,
            a.nome_local AS local,
            a.raridade AS raridade,
            '' AS dono_adesivo,
            a.foto_original AS foto_adesivo,
            'colou' AS tipo_registro
        FROM adesivos a
        JOIN usuarios u ON a.criador_id = u.id

        UNION ALL

        SELECT 
            'achou' AS tipo,
            d.data_descoberta AS data_acao,
            u.apelido AS quem_agiu,
            a.nome_local AS local,
            a.raridade AS raridade,
            criador.apelido AS dono_adesivo,
            a.foto_original AS foto_adesivo,
            d.tipo_registro AS tipo_registro
        FROM descobertas d
        JOIN usuarios u ON d.descobridor_id = u.id
        JOIN adesivos a ON d.adesivo_id = a.id
        JOIN usuarios criador ON a.criador_id = criador.id

        ORDER BY data_acao DESC
        LIMIT 50
    ";
    
    $stmt = $pdo->query($sql);
    $feed = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $feed]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>