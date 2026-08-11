<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    // TRUQUE: Cria as colunas novas silenciosamente caso o usuário abra o ranking primeiro
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN tipo_registro TEXT DEFAULT 'conquistado'"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN is_latest INTEGER DEFAULT 1"); } catch (Exception $e) {}
    
    $sql = "
        SELECT 
            u.id,
            u.apelido,
            (SELECT COUNT(*) FROM adesivos WHERE criador_id = u.id) AS adesivos_colados,
            (SELECT COUNT(*) FROM descobertas WHERE descobridor_id = u.id AND is_latest = 1) AS adesivos_achados,
            COALESCE((
                SELECT SUM(
                    CASE 
                        WHEN a.raridade = 'Comum' AND d.tipo_registro = 'conquistado' THEN 10
                        WHEN a.raridade = 'Comum' AND d.tipo_registro = 'encontrado' THEN 5
                        
                        WHEN a.raridade = 'Raro' AND d.tipo_registro = 'conquistado' THEN 50
                        WHEN a.raridade = 'Raro' AND d.tipo_registro = 'encontrado' THEN 25
                        
                        WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'conquistado' THEN 100
                        WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'encontrado' THEN 50
                        ELSE 0
                    END
                )
                FROM descobertas d
                JOIN adesivos a ON d.adesivo_id = a.id
                WHERE d.descobridor_id = u.id AND d.is_latest = 1
            ), 0) AS xp_total
        FROM usuarios u
        ORDER BY xp_total DESC
        LIMIT 50
    ";
    
    $stmt = $pdo->query($sql);
    $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ranking as &$user) {
        $xp = $user['xp_total'];
        if ($xp >= 2500) { $user['titulo'] = 'Lenda do Bando'; }
        elseif ($xp >= 800) { $user['titulo'] = 'Caçador Experiente'; }
        elseif ($xp >= 50) { $user['titulo'] = 'Explorador'; }
        else { $user['titulo'] = 'Novato'; }
    }

    echo json_encode(['sucesso' => true, 'dados' => $ranking]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>