<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    
    $admin_id = $_POST['admin_id'] ?? 0;
    $alvo_id = $_POST['alvo_id'] ?? 0;
    $nova_senha = $_POST['nova_senha'] ?? '';

    if (empty($admin_id) || empty($alvo_id) || empty($nova_senha)) {
        throw new Exception("Dados incompletos.");
    }

    // TRAVA DE SEGURANÇA: Verifica se é Admin de novo antes de alterar a senha
    $stmtAdmin = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtAdmin->execute([$admin_id]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !$admin['is_admin']) {
        throw new Exception("Acesso negado! Só administradores podem trocar senhas.");
    }

    // Criptografa a nova senha para bater com o sistema de login
    $hash_senha = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Salva no banco de dados do usuário
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->execute([$hash_senha, $alvo_id]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'A senha foi atualizada com sucesso!']);

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>