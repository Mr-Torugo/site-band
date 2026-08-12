<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    try { $pdo->exec("ALTER TABLE usuarios ADD COLUMN is_admin INTEGER DEFAULT 0"); } catch (Exception $e) {}
    
    $usuario_id = $_GET['usuario_id'] ?? 0;
    
    // Descobre se quem pediu a lista é Admin
    $stmtAdmin = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtAdmin->execute([$usuario_id]);
    $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $is_admin = $user ? (bool)$user['is_admin'] : false;

    $sql = "SELECT a.id, a.nome_local, a.lat, a.lng, a.foto_original as foto_caminho, a.raridade, a.criador_id, a.categoria, u.apelido as quem_colou
            FROM adesivos a JOIN usuarios u ON a.criador_id = u.id ORDER BY a.id ASC";
            
    $stmt = $pdo->query($sql);
    $adesivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($adesivos as &$ad) {
        $ad['codigo'] = '#' . str_pad($ad['id'], 2, "0", STR_PAD_LEFT);
        if(empty($ad['categoria'])) $ad['categoria'] = 'Urbano';
        
        $stmtDesc = $pdo->prepare("SELECT u.apelido, d.foto_selfie, d.tipo_registro FROM descobertas d JOIN usuarios u ON d.descobridor_id = u.id WHERE d.adesivo_id = ? AND d.is_latest = 1 ORDER BY d.data_descoberta ASC");
        $stmtDesc->execute([$ad['id']]);
        $descobertas = $stmtDesc->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($descobertas as &$desc) {
            $desc['tem_selfie'] = !empty($desc['foto_selfie']);
            if(!isset($desc['tipo_registro'])) $desc['tipo_registro'] = 'conquistado';
        }
        $ad['descobertas'] = $descobertas;
    }

    // Retorna a flag is_admin junto com os dados!
    echo json_encode(['sucesso' => true, 'is_admin' => $is_admin, 'dados' => $adesivos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>