<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite'; 

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try { $pdo->exec("ALTER TABLE adesivos ADD COLUMN categoria TEXT DEFAULT 'Urbano'"); } catch (Exception $e) {}

    $usuario_id = $_GET['usuario_id'] ?? '';
    if (empty($usuario_id)) throw new Exception("Usuário não informado.");

    $sqlAchados = "SELECT 
                    d.data_descoberta, d.foto_selfie, d.comentario, 
                    a.id, a.nome_local, a.foto_original, a.raridade, a.categoria 
                   FROM descobertas d
                   JOIN adesivos a ON d.adesivo_id = a.id
                   WHERE d.descobridor_id = ? AND d.tipo_registro = 'conquistado' AND d.is_latest = 1
                   ORDER BY a.id ASC";
    $stmtAchados = $pdo->prepare($sqlAchados);
    $stmtAchados->execute([$usuario_id]);
    $achados = $stmtAchados->fetchAll(PDO::FETCH_ASSOC);

    $sqlColados = "SELECT id, nome_local, foto_original, raridade, categoria, data_criacao FROM adesivos WHERE criador_id = ? ORDER BY id ASC";
    $stmtColados = $pdo->prepare($sqlColados);
    $stmtColados->execute([$usuario_id]);
    $colados = $stmtColados->fetchAll(PDO::FETCH_ASSOC);

    foreach ($achados as &$item) { 
        $item['codigo'] = '#' . str_pad($item['id'], 2, "0", STR_PAD_LEFT); 
        if(empty($item['categoria'])) $item['categoria'] = 'Urbano';
    }
    foreach ($colados as &$item) { 
        $item['codigo'] = '#' . str_pad($item['id'], 2, "0", STR_PAD_LEFT); 
        if(empty($item['categoria'])) $item['categoria'] = 'Urbano';
    }

    echo json_encode(['sucesso' => true, 'achados' => $achados, 'colados' => $colados]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>