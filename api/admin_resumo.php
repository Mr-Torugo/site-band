<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    $admin_id = $_GET['admin_id'] ?? 0;

    // TRAVA DE SEGURANÇA: Verifica se é Admin
    $stmtAdmin = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtAdmin->execute([$admin_id]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !$admin['is_admin']) {
        throw new Exception("Acesso negado! Você não é um administrador.");
    }

    // Faz as contagens rápidas para o Dashboard
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $total_adesivos = $pdo->query("SELECT COUNT(*) FROM adesivos")->fetchColumn();
    $total_descobertas = $pdo->query("SELECT COUNT(*) FROM descobertas")->fetchColumn();
    
    // Como criamos as missões recentemente, a tabela pode estar vazia. Usamos um try/catch rápido para evitar erros.
    $total_missoes = 0;
    try {
        $total_missoes = $pdo->query("SELECT COUNT(*) FROM missoes_concluidas")->fetchColumn();
    } catch (Exception $e) { $total_missoes = 0; }

    echo json_encode([
        'sucesso' => true, 
        'dados' => [
            'usuarios' => $total_usuarios,
            'adesivos' => $total_adesivos,
            'descobertas' => $total_descobertas,
            'missoes' => $total_missoes
        ]
    ]);

} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>