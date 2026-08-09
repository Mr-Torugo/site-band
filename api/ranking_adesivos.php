<?php
header('Content-Type: application/json');

$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Busca os adesivos e conta quantas descobertas cada um tem
    $sql = "SELECT 
                a.id, 
                a.codigo, 
                a.nome_local, 
                a.foto_original, 
                a.raridade,
                u.apelido AS quem_colou,
                (SELECT COUNT(*) FROM descobertas d WHERE d.adesivo_id = a.id) as total_achados
            FROM adesivos a
            JOIN usuarios u ON a.criador_id = u.id
            ORDER BY total_achados DESC, a.data_criacao DESC";

    $stmt = $pdo->query($sql);
    $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $ranking]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>