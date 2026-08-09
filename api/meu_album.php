<?php
header('Content-Type: application/json');

// Usando o banco correto na pasta api
$db_file = __DIR__ . '/banco.sqlite'; 

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $usuario_id = $_GET['usuario_id'] ?? '';

    if (empty($usuario_id)) {
        throw new Exception("Usuário não informado.");
    }

    // 1. Busca os adesivos que o usuário ENCONTROU
    $sqlAchados = "SELECT 
                    d.data_descoberta, d.foto_selfie, d.comentario, 
                    a.codigo, a.nome_local, a.foto_original, a.raridade 
                   FROM descobertas d
                   JOIN adesivos a ON d.adesivo_id = a.id
                   WHERE d.descobridor_id = ?
                   ORDER BY d.data_descoberta DESC";
    $stmtAchados = $pdo->prepare($sqlAchados);
    $stmtAchados->execute([$usuario_id]);
    $achados = $stmtAchados->fetchAll(PDO::FETCH_ASSOC);

    // 2. Busca os adesivos que o usuário COLOU NO MAPA
    $sqlColados = "SELECT 
                    id, codigo, nome_local, foto_original, raridade, data_criacao 
                   FROM adesivos 
                   WHERE criador_id = ?
                   ORDER BY data_criacao DESC";
    $stmtColados = $pdo->prepare($sqlColados);
    $stmtColados->execute([$usuario_id]);
    $colados = $stmtColados->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso' => true, 
        'achados' => $achados, 
        'colados' => $colados
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>