<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN tipo_registro TEXT DEFAULT 'conquistado'"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN is_latest INTEGER DEFAULT 1"); } catch (Exception $e) {}
    try { $pdo->exec("CREATE TABLE IF NOT EXISTS missoes_concluidas (id INTEGER PRIMARY KEY AUTOINCREMENT, usuario_id INTEGER, ano_semana TEXT, xp_ganho INTEGER, data_conclusao DATETIME DEFAULT CURRENT_TIMESTAMP)"); } catch (Exception $e) {}
    $sql = "SELECT u.id, u.apelido, (SELECT COUNT(*) FROM adesivos WHERE criador_id = u.id) AS adesivos_colados, (SELECT COUNT(*) FROM descobertas WHERE descobridor_id = u.id AND is_latest = 1) AS adesivos_achados,
            COALESCE((SELECT SUM(CASE 
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'conquistado' THEN 10
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'encontrado' THEN 5
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'conquistado' THEN 50
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'encontrado' THEN 25
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'conquistado' THEN 1500
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'encontrado' THEN 750
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'conquistado' THEN 500
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'encontrado' THEN 250
                    ELSE 0 END) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = u.id AND d.is_latest = 1), 0) + 
            COALESCE((SELECT SUM(xp_ganho) FROM missoes_concluidas WHERE usuario_id = u.id), 0) AS xp_total
        FROM usuarios u ORDER BY xp_total DESC LIMIT 50";
    $stmt = $pdo->query($sql); $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($ranking as &$user) { $xp = $user['xp_total']; if ($xp >= 500) { $user['titulo'] = 'Lenda do Bando'; } elseif ($xp >= 200) { $user['titulo'] = 'Caçador Experiente'; } elseif ($xp >= 50) { $user['titulo'] = 'Explorador'; } else { $user['titulo'] = 'Novato'; } }
    echo json_encode(['sucesso' => true, 'dados' => $ranking]);
} catch (Exception $e) { echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]); }
?>