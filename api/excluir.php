<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dados = json_decode(file_get_contents('php://input'), true);
    $id = $dados['id'] ?? '';

    if (empty($id)) {
        throw new Exception("ID do adesivo não informado.");
    }

    $stmt = $pdo->prepare("SELECT foto_caminho FROM adesivos WHERE id = ?");
    $stmt->execute([$id]);
    $adesivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($adesivo) {
        $caminho_arquivo = '../' . $adesivo['foto_caminho'];
        
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo); 
        }

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