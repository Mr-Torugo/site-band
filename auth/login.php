<?php
header('Content-Type: application/json');

$db_file = __DIR__ . '/../api/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dados = json_decode(file_get_contents('php://input'), true);
    $apelido = trim($dados['apelido'] ?? '');
    $senha = $dados['senha'] ?? '';

    if (empty($apelido) || empty($senha)) {
        throw new Exception("Preencha todos os campos.");
    }

    $stmt = $pdo->prepare("SELECT id, senha_hash FROM usuarios WHERE apelido = ?");
    $stmt->execute([$apelido]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        echo json_encode(['sucesso' => true, 'id' => $usuario['id'], 'apelido' => $apelido]);
    } else {
        throw new Exception("Apelido ou senha incorretos.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>