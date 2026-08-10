<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    $adesivo_id = $_GET['adesivo_id'] ?? '';
    
    // Aqui nós pegamos TODO o histórico (sem o filtro is_latest) para formar a linha do tempo do Mural!
    $stmt = $pdo->prepare("
        SELECT u.apelido, d.comentario, d.foto_selfie, d.data_descoberta, d.tipo_registro 
        FROM descobertas d 
        JOIN usuarios u ON d.descobridor_id = u.id 
        WHERE d.adesivo_id = ? 
        ORDER BY d.data_descoberta DESC
    ");
    $stmt->execute([$adesivo_id]);
    $mural = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $mural]);

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>