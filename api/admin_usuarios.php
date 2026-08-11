<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    $admin_id = $_GET['admin_id'] ?? 0;

    // TRAVA DE SEGURANÇA: Verifica se quem está pedindo a lista é realmente um Admin
    $stmtAdmin = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtAdmin->execute([$admin_id]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !$admin['is_admin']) {
        throw new Exception("Acesso negado! Você não é um administrador.");
    }

    // Puxa a lista de todo mundo
    $stmt = $pdo->query("SELECT id, apelido, is_admin FROM usuarios ORDER BY id ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $usuarios]);

} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>