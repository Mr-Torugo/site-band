<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    // TRUQUES: Cria as colunas novas silenciosamente se elas não existirem
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN tipo_registro TEXT DEFAULT 'conquistado'"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN is_latest INTEGER DEFAULT 1"); } catch (Exception $e) {}

    $adesivo_id = $_POST['adesivo_id'] ?? '';
    $descobridor_id = $_POST['descobridor_id'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    $tipo_registro = $_POST['tipo_registro'] ?? 'avistado'; 
    
    $tem_foto = isset($_FILES['selfie']) && $_FILES['selfie']['error'] === UPLOAD_ERR_OK;

    if (empty($adesivo_id) || empty($descobridor_id)) {
        throw new Exception("Dados incompletos.");
    }

    $stmtAd = $pdo->prepare("SELECT criador_id FROM adesivos WHERE id = ?");
    $stmtAd->execute([$adesivo_id]);
    $adesivo = $stmtAd->fetch(PDO::FETCH_ASSOC);
    
    if ($adesivo['criador_id'] == $descobridor_id) {
        throw new Exception("Você não pode descobrir seu próprio adesivo!");
    }

    $hierarquia = ['avistado' => 1, 'encontrado' => 2, 'conquistado' => 3];
    $nivel_novo = $hierarquia[$tipo_registro];

    if ($nivel_novo > 1 && !$tem_foto) {
        throw new Exception("Para os níveis 'Encontrado' e 'Conquistado', você precisa enviar uma foto!");
    }

    // Busca apenas o registro ativo (is_latest = 1)
    $stmtCheck = $pdo->prepare("SELECT id, tipo_registro FROM descobertas WHERE adesivo_id = ? AND descobridor_id = ? AND is_latest = 1");
    $stmtCheck->execute([$adesivo_id, $descobridor_id]);
    $ja_existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $caminho_foto = null;
    if ($tem_foto) {
        $extensao = pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = 'registro_' . time() . '_' . rand(1000, 9999) . '.' . $extensao;
        move_uploaded_file($_FILES['selfie']['tmp_name'], __DIR__ . '/../uploads/' . $nome_arquivo);
        $caminho_foto = 'uploads/' . $nome_arquivo;
    }

    if ($ja_existe) {
        $nivel_atual = $hierarquia[$ja_existe['tipo_registro'] ?? 'avistado'];
        
        if ($nivel_novo <= $nivel_atual) {
            throw new Exception("Você já atingiu este nível ou superior neste adesivo!");
        }

        // MARCA O ANTIGO COMO HISTÓRICO (is_latest = 0)
        $stmtUp = $pdo->prepare("UPDATE descobertas SET is_latest = 0 WHERE adesivo_id = ? AND descobridor_id = ?");
        $stmtUp->execute([$adesivo_id, $descobridor_id]);

        // INSERE O NOVO REGISTRO (is_latest = 1) para gerar a linha do tempo!
        $novo_com = !empty($comentario) ? $comentario : "Evoluiu o registro para " . ucfirst($tipo_registro) . "!";
        $stmtIn = $pdo->prepare("INSERT INTO descobertas (adesivo_id, descobridor_id, comentario, foto_selfie, tipo_registro, is_latest) VALUES (?, ?, ?, ?, ?, 1)");
        $stmtIn->execute([$adesivo_id, $descobridor_id, $novo_com, $caminho_foto, $tipo_registro]);

        $msg = $tipo_registro === 'conquistado' ? "👑 Upgrade para Conquistado! Você ganhou XP Total e ele foi pro seu Álbum!" : "📸 Upgrade para Encontrado! Ganhou 50% de XP!";
        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);

    } else {
        $stmtIn = $pdo->prepare("INSERT INTO descobertas (adesivo_id, descobridor_id, comentario, foto_selfie, tipo_registro, is_latest) VALUES (?, ?, ?, ?, ?, 1)");
        $stmtIn->execute([$adesivo_id, $descobridor_id, $comentario, $caminho_foto, $tipo_registro]);

        if ($tipo_registro === 'conquistado') $msg = "👑 Conquistado! XP Total recebido e adesivo no Álbum!";
        elseif ($tipo_registro === 'encontrado') $msg = "📸 Encontrado! Você recebeu 50% do XP!";
        else $msg = "👁️ Avistado! O radar marcou que você passou por aqui.";

        // --- VERIFICA MEDALHAS AUTOMATICAMENTE ---
        require_once 'motor_conquistas.php';
        checarEAtualizarMedalhas($pdo, $descobridor_id); // Chama o árbitro!
        // -----------------------------------------
            
        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>