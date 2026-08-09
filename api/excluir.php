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

    // 1. Busca a foto usando o novo nome da coluna: foto_original
    $stmt = $pdo->prepare("SELECT foto_original FROM adesivos WHERE id = ?");
    $stmt->execute([$id]);
    $adesivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($adesivo) {
        $caminho_arquivo = '../' . $adesivo['foto_original'];
        
        // Apaga a imagem física da pasta uploads se ela existir
        if (!empty($adesivo['foto_original']) && file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo); 
        }

        // 2. Limpa o histórico de "descobertas" ligadas a esse adesivo para não deixar lixo no banco
        $stmtDescobertas = $pdo->prepare("DELETE FROM descobertas WHERE adesivo_id = ?");
        $stmtDescobertas->execute([$id]);

        // 3. Deleta o adesivo principal
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