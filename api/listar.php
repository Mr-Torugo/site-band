<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // O comando JOIN junta o Adesivo (a) com o Usuário (u)
    $sql = "SELECT 
                a.id,
                a.codigo,
                a.nome_local,
                a.lat,
                a.lng,
                a.foto_original AS foto_caminho, 
                u.apelido AS quem_colou,
                a.raridade
            FROM adesivos a
            JOIN usuarios u ON a.criador_id = u.id
            ORDER BY a.data_criacao DESC";

    $stmt = $pdo->query($sql);
    $adesivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $adesivos]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>