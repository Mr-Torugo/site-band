<?php
header('Content-Type: application/json');
require_once '../api/conexao.php';

try {
    $apelido = trim($_POST['apelido'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($apelido) || empty($senha)) {
        throw new Exception("Preencha todos os campos.");
    }

    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE apelido = ?");
    $stmtCheck->execute([$apelido]);
    if ($stmtCheck->fetch()) {
        throw new Exception("Este apelido já está em uso. Escolha outro!");
    }

    // Criptografa a senha
    $senhaHash_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    // MUDANÇA AQUI: Usando senha_hash em vez de senha
    $stmt = $pdo->prepare("INSERT INTO usuarios (apelido, senha_hash, is_admin) VALUES (?, ?, 0)");
    $stmt->execute([$apelido, $senhaHash_criptografada]);
    
    $novoId = $pdo->lastInsertId();

    $_SESSION['usuario_id'] = $novoId;
    $_SESSION['is_admin'] = 0;

    echo json_encode(['sucesso' => true, 'id' => $novoId, 'apelido' => $apelido]);

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>