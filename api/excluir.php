<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dados = json_decode(file_get_contents('php://input'), true);
    $id = $dados['id'] ?? '';
    $usuario_id = $dados['usuario_id'] ?? ''; // ID de quem apertou o botão

    if (empty($id) || empty($usuario_id)) {
        throw new Exception("Dados insuficientes para exclusão.");
    }

    // 1. Puxa a foto e QUEM CRIOU o adesivo
    $stmt = $pdo->prepare("SELECT foto_original, criador_id FROM adesivos WHERE id = ?");
    $stmt->execute([$id]);
    $adesivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($adesivo) {
        
        if ($adesivo['criador_id'] != $usuario_id) {
            throw new Exception("Permissão negada! Apenas quem colou este adesivo pode excluí-lo.");
        }

        $caminho_arquivo = '../' . $adesivo['foto_original'];
        
        if (!empty($adesivo['foto_original']) && file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo); 
        }

        $stmtDescobertas = $pdo->prepare("DELETE FROM descobertas WHERE adesivo_id = ?");
        $stmtDescobertas->execute([$id]);

        $stmtDelete = $pdo->prepare("DELETE FROM adesivos WHERE id = ?");
        $stmtDelete->execute([$id]);

        echo json_encode(['sucesso' => true]);
    } else {
        throw new Exception("Adesivo não encontrado no banco de dados.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>