<?php
header('Content-Type: application/json');
require_once '../api/conexao.php';

try {
    $apelido = $_POST['apelido'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($apelido) || empty($senha)) {
        throw new Exception("Preencha todos os campos.");
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE apelido = ?");
    $stmt->execute([$apelido]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // MUDANÇA AQUI: Lendo $user['senha_hash'] do banco
    if ($user && password_verify($senha, $user['senha_hash'])) {
        
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        echo json_encode([
            'sucesso' => true, 
            'id' => $user['id'], 
            'apelido' => $user['apelido'],
            'is_admin' => $user['is_admin']
        ]);
    } else {
        throw new Exception("Usuário ou senha incorretos.");
    }

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>