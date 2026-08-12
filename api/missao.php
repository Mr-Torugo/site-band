<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

require_once 'conexao.php';

try {

    // Cria a tabela de histórico de missões caso não exista
    $pdo->exec("CREATE TABLE IF NOT EXISTS missoes_concluidas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        ano_semana TEXT,
        xp_ganho INTEGER,
        data_conclusao DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $usuario_id = $_GET['usuario_id'] ?? ($_POST['usuario_id'] ?? 0);
    $acao = $_GET['acao'] ?? ($_POST['acao'] ?? 'status');

    if (!$usuario_id) throw new Exception("Usuário não informado.");

    // MÁGICA DO TEMPO: Pega a semana atual e as datas de segunda a domingo
    $ano_semana = date('Y-W'); // Ex: 2026-33
    $inicio_semana = date('Y-m-d 00:00:00', strtotime('monday this week'));
    $fim_semana = date('Y-m-d 23:59:59', strtotime('sunday this week'));
    $semana_num = (int)date('W');

    // ==========================================
    // ROTAÇÃO DAS MISSÕES (Ciclo de 5 Semanas)
    // ==========================================
    $total_missoes = 5; // <-- SE QUISER ADICIONAR MAIS, AUMENTE ESTE NÚMERO!
    $tipo_missao = $semana_num % $total_missoes;
    
    $missao = [];
    $progresso_atual = 0;

    // MISSÃO 0: Explorador da Semana
    if ($tipo_missao === 0) {
        $missao = ['titulo' => 'Explorador da Semana', 'desc' => 'Faça 3 registros de descoberta no mapa (Avistar, Encontrar ou Conquistar).', 'meta' => 3, 'xp' => 100];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM descobertas WHERE descobridor_id = ? AND data_descoberta >= ? AND data_descoberta <= ?");
        $stmt->execute([$usuario_id, $inicio_semana, $fim_semana]);
        $progresso_atual = (int)$stmt->fetchColumn();
    } 
    // MISSÃO 1: Expansão do Bando
    elseif ($tipo_missao === 1) {
        $missao = ['titulo' => 'Expansão do Bando', 'desc' => 'Cole 2 novos adesivos pela cidade.', 'meta' => 2, 'xp' => 150];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM adesivos WHERE criador_id = ? AND data_criacao >= ? AND data_criacao <= ?");
        $stmt->execute([$usuario_id, $inicio_semana, $fim_semana]);
        $progresso_atual = (int)$stmt->fetchColumn();
    } 
    // MISSÃO 2: Caçador Urbano
    elseif ($tipo_missao === 2) {
        $missao = ['titulo' => 'Caçador Urbano', 'desc' => 'Conquiste (Tire Selfie) com 2 adesivos da categoria Urbano.', 'meta' => 2, 'xp' => 200];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = ? AND d.tipo_registro = 'conquistado' AND a.categoria = 'Urbano' AND d.data_descoberta >= ? AND d.data_descoberta <= ?");
        $stmt->execute([$usuario_id, $inicio_semana, $fim_semana]);
        $progresso_atual = (int)$stmt->fetchColumn();
    }
    // MISSÃO 3: Trilha Ecológica 
    elseif ($tipo_missao === 3) {
        $missao = ['titulo' => 'Trilha Ecológica', 'desc' => 'Encontre (Foto) ou Conquiste (Selfie) 2 adesivos da categoria Natureza.', 'meta' => 2, 'xp' => 150];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = ? AND d.tipo_registro IN ('encontrado', 'conquistado') AND a.categoria = 'Natureza' AND d.data_descoberta >= ? AND d.data_descoberta <= ?");
        $stmt->execute([$usuario_id, $inicio_semana, $fim_semana]);
        $progresso_atual = (int)$stmt->fetchColumn();
    }
    // MISSÃO 4: Caçador de Lendas 
    elseif ($tipo_missao === 4) {
        $missao = ['titulo' => 'Caçador de Lendas', 'desc' => 'Visite e registre (qualquer nível) 1 adesivo de raridade Lendário.', 'meta' => 1, 'xp' => 2000];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = ? AND a.raridade = 'Lendário' AND d.data_descoberta >= ? AND d.data_descoberta <= ?");
        $stmt->execute([$usuario_id, $inicio_semana, $fim_semana]);
        $progresso_atual = (int)$stmt->fetchColumn();
    }

    // ==========================================

    // Verifica se já resgatou o XP desta semana
    $stmtCheck = $pdo->prepare("SELECT id FROM missoes_concluidas WHERE usuario_id = ? AND ano_semana = ?");
    $stmtCheck->execute([$usuario_id, $ano_semana]);
    $ja_resgatou = $stmtCheck->fetch() ? true : false;

    $progresso_atual = min($progresso_atual, $missao['meta']); // Não deixa passar de 100%
    $concluida = $progresso_atual >= $missao['meta'];

    if ($acao === 'resgatar') {
        if ($ja_resgatou) throw new Exception("Recompensa já resgatada nesta semana!");
        if (!$concluida) throw new Exception("Você ainda não completou a missão!");

        $stmtIn = $pdo->prepare("INSERT INTO missoes_concluidas (usuario_id, ano_semana, xp_ganho) VALUES (?, ?, ?)");
        $stmtIn->execute([$usuario_id, $ano_semana, $missao['xp']]);
        
        echo json_encode(['sucesso' => true, 'mensagem' => '🎉 Missão cumprida! Você ganhou ' . $missao['xp'] . ' XP!']);
        exit;
    }

    echo json_encode([
        'sucesso' => true,
        'missao' => $missao,
        'progresso' => $progresso_atual,
        'concluida' => $concluida,
        'ja_resgatou' => $ja_resgatou,
        'prazo' => date('d/m/Y', strtotime('sunday this week'))
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>