<?php
header('Content-Type: application/json');

$db_file = __DIR__ . '/../api/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $usuario_id = $_GET['usuario_id'] ?? '';

    if (empty($usuario_id)) {
        throw new Exception("ID do usuário não informado.");
    }

    $sql = "SELECT 
                a.codigo,
                a.nome_local,
                a.raridade,
                a.foto_original,
                d.foto_selfie,
                d.comentario,
                d.data_descoberta
            FROM descobertas d
            JOIN adesivos a ON d.adesivo_id = a.id
            WHERE d.descobridor_id = ?
            ORDER BY d.data_descoberta DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $colecao = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $colecao]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>