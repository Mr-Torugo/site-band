<?php
header('Content-Type: application/json');

require_once 'conexao.php';

try {

    $dados = json_decode(file_get_contents('php://input'), true);
    $apelido = trim($dados['apelido'] ?? '');
    $senha = $dados['senha'] ?? '';

    if (empty($apelido) || empty($senha)) {
        throw new Exception("Preencha todos os campos.");
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE apelido = ?");
    $stmt->execute([$apelido]);
    if ($stmt->fetch()) {
        throw new Exception("Esse apelido já está em uso por outro membro do Bando!");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (apelido, senha_hash) VALUES (?, ?)");
    $stmt->execute([$apelido, $senha_hash]);
    
    $novo_id = $pdo->lastInsertId();

    echo json_encode(['sucesso' => true, 'id' => $novo_id, 'apelido' => $apelido]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>