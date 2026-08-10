<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN tipo_registro TEXT DEFAULT 'conquistado'"); } catch (Exception $e) {}
    
    $sql = "SELECT a.id, a.nome_local, a.lat, a.lng, a.foto_original as foto_caminho, a.raridade, a.criador_id, u.apelido as quem_colou
            FROM adesivos a JOIN usuarios u ON a.criador_id = u.id ORDER BY a.id ASC";
            
    $stmt = $pdo->query($sql);
    $adesivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($adesivos as &$ad) {
        $ad['codigo'] = '#' . str_pad($ad['id'], 2, "0", STR_PAD_LEFT);
        
        $stmtDesc = $pdo->prepare("SELECT u.apelido, d.foto_selfie, d.tipo_registro FROM descobertas d JOIN usuarios u ON d.descobridor_id = u.id WHERE d.adesivo_id = ? ORDER BY d.data_descoberta ASC");
        $stmtDesc->execute([$ad['id']]);
        $descobertas = $stmtDesc->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($descobertas as &$desc) {
            $desc['tem_selfie'] = !empty($desc['foto_selfie']);
            if(!isset($desc['tipo_registro'])) $desc['tipo_registro'] = 'conquistado';
        }
        $ad['descobertas'] = $descobertas;
    }

    echo json_encode(['sucesso' => true, 'dados' => $adesivos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>