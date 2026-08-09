<?php
header('Content-Type: application/json');

$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT 
                u.id, 
                u.apelido,
                (SELECT COUNT(*) FROM descobertas d WHERE d.descobridor_id = u.id) as achados,
                (SELECT COUNT(*) FROM adesivos a WHERE a.criador_id = u.id) as colados,
                (
                    SELECT COALESCE(SUM(
                        CASE a2.raridade
                            WHEN 'Comum' THEN 10
                            WHEN 'Raro' THEN 30
                            WHEN 'Lendário' THEN 500
                            ELSE 10
                        END
                    ), 0)
                    FROM descobertas d2
                    JOIN adesivos a2 ON d2.adesivo_id = a2.id
                    WHERE d2.descobridor_id = u.id
                ) as xp_achados
            FROM usuarios u
            ORDER BY (xp_achados + (colados * 15)) DESC"; 

    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ranking = [];
    foreach ($usuarios as $user) {
        $xp_total = $user['xp_achados'] + ($user['colados'] * 15); 
        
        $badges = '';
        if ($user['colados'] >= 5) $badges .= '🎨 '; 
        if ($user['achados'] >= 10) $badges .= '🕵️ '; 
        if ($xp_total >= 1000) $badges .= '🔥 '; 
        
        $user['xp_total'] = $xp_total;
        $user['badges'] = $badges;
        $ranking[] = $user;
    }

    echo json_encode(['sucesso' => true, 'dados' => $ranking]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>