<?php
header('Content-Type: application/json');

require_once 'conexao.php';

try {

    // Seus dados de teste
    $apelido = 'Vitor';
    $senha_plana = '123456'; // Essa é a senha que você digitaria no login
    
    // O PHP criptografa a senha antes de salvar no banco
    $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

    // 1. Verifica se o Vitor já existe para não dar erro de duplicidade
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE apelido = ?");
    $stmt->execute([$apelido]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        echo json_encode([
            'sucesso' => true, 
            'mensagem' => 'O usuário Vitor já existe no banco!', 
            'id_usuario' => $usuario['id']
        ]);
    } else {
        // 2. Insere você no banco de dados
        $stmt = $pdo->prepare("INSERT INTO usuarios (apelido, senha_hash) VALUES (?, ?)");
        $stmt->execute([$apelido, $senha_hash]);
        
        $novo_id = $pdo->lastInsertId();

        echo json_encode([
            'sucesso' => true, 
            'mensagem' => 'Usuário Vitor criado com sucesso!', 
            'id_usuario' => $novo_id
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>