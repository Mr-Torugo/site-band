<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adesivo_id = $_POST['adesivo_id'] ?? '';
    $descobridor_id = $_POST['descobridor_id'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    $tem_foto = isset($_FILES['selfie']) && $_FILES['selfie']['error'] === UPLOAD_ERR_OK;

    if (empty($adesivo_id) || empty($descobridor_id)) {
        throw new Exception("Dados incompletos.");
    }

    // Impede o usuário de pegar a própria figurinha
    $stmtAd = $pdo->prepare("SELECT criador_id FROM adesivos WHERE id = ?");
    $stmtAd->execute([$adesivo_id]);
    $adesivo = $stmtAd->fetch(PDO::FETCH_ASSOC);
    
    if ($adesivo['criador_id'] == $descobridor_id) {
        throw new Exception("Você não pode descobrir seu próprio adesivo!");
    }

    // Verifica se o usuário já descobriu isso antes
    $stmtCheck = $pdo->prepare("SELECT id, foto_selfie FROM descobertas WHERE adesivo_id = ? AND descobridor_id = ?");
    $stmtCheck->execute([$adesivo_id, $descobridor_id]);
    $ja_existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $caminho_foto = null;
    if ($tem_foto) {
        $extensao = pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = 'selfie_' . time() . '_' . rand(1000, 9999) . '.' . $extensao;
        $caminho_absoluto = __DIR__ . '/../uploads/' . $nome_arquivo;
        
        if (!move_uploaded_file($_FILES['selfie']['tmp_name'], $caminho_absoluto)) {
            throw new Exception("Erro ao salvar a imagem.");
        }
        $caminho_foto = 'uploads/' . $nome_arquivo;
    }

    if ($ja_existe) {
        
        if (!empty($ja_existe['foto_selfie'])) {
            throw new Exception("Você já anexou a foto antes e resgatou 100% do XP!");
        }
        if (!$tem_foto) {
            throw new Exception("Você já registrou sem foto. Envie uma imagem agora para ganhar o restante do XP!");
        }

        // Se ele chegou aqui, é porque voltou com a foto! (Atualiza a descoberta)
        $stmtUp = $pdo->prepare("UPDATE descobertas SET foto_selfie = ?, comentario = ? WHERE id = ?");
        $novo_com = !empty($comentario) ? $comentario : "Voltei para deixar minha selfie!";
        $stmtUp->execute([$caminho_foto, $novo_com, $ja_existe['id']]);

        echo json_encode(['sucesso' => true, 'mensagem' => '📸 Selfie adicionada com sucesso! Você garantiu os outros 50% de XP!']);

    } else {
        // É UMA DESCOBERTA INÉDITA
        $stmtIn = $pdo->prepare("INSERT INTO descobertas (adesivo_id, descobridor_id, comentario, foto_selfie) VALUES (?, ?, ?, ?)");
        $stmtIn->execute([$adesivo_id, $descobridor_id, $comentario, $caminho_foto]);

        // Define a mensagem dependendo se mandou foto ou não
        $msg = $tem_foto 
            ? "🎉 Descoberta completa! Você ganhou 100% do XP!" 
            : "✅ Adesivo achado! Ganhou 50% do XP. Volte depois e adicione uma foto para ganhar o resto!";
            
        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>