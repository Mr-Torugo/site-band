<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Busca todos os adesivos
    $sqlAdesivos = "SELECT 
                a.id, a.codigo, a.nome_local, a.lat, a.lng, 
                a.foto_original AS foto_caminho, u.apelido AS quem_colou, a.raridade
            FROM adesivos a
            JOIN usuarios u ON a.criador_id = u.id
            ORDER BY a.data_criacao DESC";
    $stmt = $pdo->query($sqlAdesivos);
    $adesivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Busca todo o histórico de descobertas
    $sqlDesc = "SELECT d.adesivo_id, u.apelido, d.foto_selfie 
                FROM descobertas d
                JOIN usuarios u ON d.descobridor_id = u.id
                ORDER BY d.data_descoberta ASC";
    $stmtDesc = $pdo->query($sqlDesc);
    $descobertas = $stmtDesc->fetchAll(PDO::FETCH_ASSOC);

    // 3. Organiza as descobertas dentro do ID de cada adesivo
    $historico = [];
    foreach ($descobertas as $desc) {
        $historico[$desc['adesivo_id']][] = [
            'apelido' => $desc['apelido'],
            'tem_selfie' => !empty($desc['foto_selfie'])
        ];
    }

    // 4. Cola o histórico dentro do adesivo correspondente
    foreach ($adesivos as &$adesivo) {
        $adesivo['descobertas'] = $historico[$adesivo['id']] ?? [];
    }

    echo json_encode(['sucesso' => true, 'dados' => $adesivos]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>