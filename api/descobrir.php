<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';
$upload_dir = __DIR__ . '/../uploads/';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adesivo_id = $_POST['adesivo_id'] ?? '';
    $descobridor_id = $_POST['descobridor_id'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    $foto = $_FILES['selfie'] ?? null;

    if (empty($adesivo_id) || empty($descobridor_id)) {
        throw new Exception("Faltam dados da descoberta.");
    }

    $caminho_banco = null;

    // Processa a selfie, se o usuário tiver enviado uma
    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid('selfie_') . '.' . $extensao;
        $caminho_destino = $upload_dir . $novo_nome;

        if (move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
            $caminho_banco = 'uploads/' . $novo_nome;
        } else {
            throw new Exception("Erro ao salvar a selfie na pasta uploads.");
        }
    }

    // Grava a descoberta no banco
    $stmt = $pdo->prepare("INSERT INTO descobertas (adesivo_id, descobridor_id, foto_selfie, comentario) VALUES (?, ?, ?, ?)");
    $stmt->execute([$adesivo_id, $descobridor_id, $caminho_banco, $comentario]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Descoberta registrada com sucesso!']);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'erro' => 'Você já encontrou este adesivo! Ele já está no seu álbum.']);
    } else {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>