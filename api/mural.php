<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adesivo_id = $_GET['adesivo_id'] ?? '';

    if (empty($adesivo_id)) {
        throw new Exception("ID do adesivo não informado.");
    }

    $sql = "SELECT 
                u.apelido, 
                d.foto_selfie, 
                d.comentario, 
                d.data_descoberta 
            FROM descobertas d
            JOIN usuarios u ON d.descobridor_id = u.id
            WHERE d.adesivo_id = ?
            ORDER BY d.data_descoberta DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$adesivo_id]);
    $mural = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $mural]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>