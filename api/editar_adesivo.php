<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$id = $_POST['id'] ?? 0;
$usuario_id = $_POST['usuario_id'] ?? 0;
$nomeLocal = trim($_POST['nomeLocal'] ?? '');
$categoria = $_POST['categoria'] ?? 'Urbano';
$raridade_enviada = $_POST['raridade'] ?? '';

if (!$id || !$usuario_id || empty($nomeLocal)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos para edição.']);
    exit;
}

try {
    // 1. Puxa os dados atuais e verifica se o usuário é Admin
    $stmt = $pdo->prepare("SELECT criador_id, raridade, (SELECT is_admin FROM usuarios WHERE id = ?) AS is_admin FROM adesivos WHERE id = ?");
    $stmt->execute([$usuario_id, $id]);
    $adesivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adesivo) {
        echo json_encode(['sucesso' => false, 'erro' => 'Adesivo não encontrado.']);
        exit;
    }

    // Trava principal: Só o criador ou Admin podem editar as informações básicas
    if ($adesivo['criador_id'] != $usuario_id && $adesivo['is_admin'] != 1) {
        echo json_encode(['sucesso' => false, 'erro' => 'Você não tem permissão para editar este adesivo.']);
        exit;
    }

    // 2. A TRAVA DA RARIDADE: Somente o Admin tem o poder de alterar.
    if ($adesivo['is_admin'] == 1 && !empty($raridade_enviada)) {
        // Se for admin, aceita a raridade nova que ele escolheu
        $raridade_final = $raridade_enviada;
    } else {
        // Se não for admin, mantém a raridade atual
        $raridade_final = $adesivo['raridade'];
    }

    $is_evento = ($raridade_final === 'Tesouro') ? 1 : 0;

    // 3. Salva no banco de dados
    try {
        $sql = "UPDATE adesivos SET nome_local = ?, categoria = ?, raridade = ?, is_evento = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomeLocal, $categoria, $raridade_final, $is_evento, $id]);
        
    } catch (PDOException $e) {
        $sql = "UPDATE adesivos SET nome_local = ?, categoria = ?, raridade = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomeLocal, $categoria, $raridade_final, $id]);
    }

    echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo atualizado com sucesso!']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco: ' . $e->getMessage()]);
}
?>