<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // TRUQUE: Cria a coluna nova silenciosamente se ela não existir
    try { $pdo->exec("ALTER TABLE descobertas ADD COLUMN tipo_registro TEXT DEFAULT 'conquistado'"); } catch (Exception $e) {}

    $adesivo_id = $_POST['adesivo_id'] ?? '';
    $descobridor_id = $_POST['descobridor_id'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    $tipo_registro = $_POST['tipo_registro'] ?? 'avistado'; // avistado, encontrado, conquistado
    
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

    // Define o peso numérico para saber se é um "upgrade"
    $hierarquia = ['avistado' => 1, 'encontrado' => 2, 'conquistado' => 3];
    $nivel_novo = $hierarquia[$tipo_registro];

    // Verifica se precisa de foto
    if ($nivel_novo > 1 && !$tem_foto) {
        throw new Exception("Para os níveis 'Encontrado' e 'Conquistado', você precisa enviar uma foto!");
    }

    $stmtCheck = $pdo->prepare("SELECT id, tipo_registro FROM descobertas WHERE adesivo_id = ? AND descobridor_id = ?");
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

        // Evolui o registro do usuário
        $stmtUp = $pdo->prepare("UPDATE descobertas SET tipo_registro = ?, foto_selfie = COALESCE(?, foto_selfie), comentario = ? WHERE id = ?");
        $novo_com = !empty($comentario) ? $comentario : "Evoluiu o registro!";
        $stmtUp->execute([$tipo_registro, $caminho_foto, $novo_com, $ja_existe['id']]);

        $msg = $tipo_registro === 'conquistado' ? "👑 Upgrade para Conquistado! Você ganhou XP Total e ele foi pro seu Álbum!" : "📸 Upgrade para Encontrado! Ganhou 50% de XP!";
        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);

    } else {
        // Registro Inédito
        $stmtIn = $pdo->prepare("INSERT INTO descobertas (adesivo_id, descobridor_id, comentario, foto_selfie, tipo_registro) VALUES (?, ?, ?, ?, ?)");
        $stmtIn->execute([$adesivo_id, $descobridor_id, $comentario, $caminho_foto, $tipo_registro]);

        if ($tipo_registro === 'conquistado') $msg = "👑 Conquistado! XP Total recebido e adesivo no Álbum!";
        elseif ($tipo_registro === 'encontrado') $msg = "📸 Encontrado! Você recebeu 50% do XP!";
        else $msg = "👁️ Avistado! O radar marcou que você passou por aqui.";
            
        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>