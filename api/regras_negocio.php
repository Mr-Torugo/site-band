<?php
// ==========================================
// FUNÇÕES DE XP E TÍTULOS
// ==========================================

function calcularXP($pdo, $usuario_id) {
    // Essa é aquela SQL gigante que calcula tudo!
    $sql = "
        SELECT COALESCE((
            SELECT SUM(
                CASE 
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'conquistado' THEN 10
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'encontrado' THEN 5
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'conquistado' THEN 50
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'encontrado' THEN 25
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'conquistado' THEN 100
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'encontrado' THEN 50
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'conquistado' THEN 500
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'encontrado' THEN 250
                    ELSE 0
                END
            ) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = ? AND d.is_latest = 1
        ), 0) + 
        COALESCE((SELECT SUM(xp_ganho) FROM missoes_concluidas WHERE usuario_id = ?), 0) AS xp_total
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id, $usuario_id]);
    return (int)$stmt->fetchColumn();
}

function definirTitulo($xp) {
    if ($xp >= 500) return 'Lenda do Bando';
    if ($xp >= 200) return 'Caçador Experiente';
    if ($xp >= 50) return 'Explorador';
    return 'Novato';
}

// ==========================================
// FUNÇÃO DE MEDALHAS
// ==========================================

function processarMedalhas($pdo, $usuario_id) {
    // Busca informações base
    $stmtCriados = $pdo->prepare("SELECT COUNT(*) FROM adesivos WHERE criador_id = ?"); 
    $stmtCriados->execute([$usuario_id]); 
    $total_criados = (int)$stmtCriados->fetchColumn();

    $stmtMissoes = $pdo->prepare("SELECT COUNT(*) FROM missoes_concluidas WHERE usuario_id = ?"); 
    $stmtMissoes->execute([$usuario_id]); 
    $total_missoes = (int)$stmtMissoes->fetchColumn();

    $stmtDesc = $pdo->prepare("SELECT d.tipo_registro, d.data_descoberta, d.comentario, a.categoria, a.raridade FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = ?"); 
    $stmtDesc->execute([$usuario_id]); 
    $descobertas = $stmtDesc->fetchAll(PDO::FETCH_ASSOC);

    // Contadores
    $c_total = count($descobertas); 
    $c_praia = 0; $c_turistico = 0; $c_moveis = 0; $c_estados_int = 0; $c_estrada = 0; 
    $c_conquistado = 0; $c_encontrado = 0; 
    $c_coruja = 0; $c_galo = 0; $c_rolezeiro = 0; 
    $c_raro = 0; $c_lendario = 0; $c_comentarios = 0;

    foreach ($descobertas as $d) { 
        $cat = $d['categoria']; $rar = $d['raridade']; $tipo = $d['tipo_registro']; 
        $coment = trim($d['comentario']); 
        $hora = (int)date('H', strtotime($d['data_descoberta'])); 
        $dia_sem = (int)date('w', strtotime($d['data_descoberta']));
        
        if ($cat === 'Praia') $c_praia++; if ($cat === 'Turísticos') $c_turistico++; if ($cat === 'Móveis') $c_moveis++; if ($cat === 'Estados' || $cat === 'Internacionais') $c_estados_int++; if ($cat === 'Estrada') $c_estrada++;
        if ($rar === 'Raro') $c_raro++; if ($rar === 'Lendário' || $rar === 'Tesouro') $c_lendario++;
        if ($tipo === 'conquistado') $c_conquistado++; if ($tipo === 'encontrado') $c_encontrado++; if (!empty($coment)) $c_comentarios++;
        if ($hora >= 0 && $hora < 5) $c_coruja++; if ($hora >= 5 && $hora <= 9) $c_galo++; if ($dia_sem === 0 || $dia_sem === 6) $c_rolezeiro++;
    }

    return [
        ['nome' => 'Pioneiro', 'icone' => '🌟', 'desc' => 'Caçador da primeira geração do Bando Map.', 'desbloqueada' => true],
        ['nome' => 'Coruja', 'icone' => '🦉', 'desc' => 'Fez um registro na calada da noite.', 'desbloqueada' => $c_coruja > 0],
        ['nome' => 'Estradeiro', 'icone' => '🏍️', 'desc' => 'Registrou um adesivo na Estrada.', 'desbloqueada' => $c_estrada > 0],
        ['nome' => 'Mito', 'icone' => '🦄', 'desc' => 'Registrou o seu primeiro adesivo Lendário.', 'desbloqueada' => $c_lendario > 0],
        ['nome' => 'Pé na Areia', 'icone' => '🏖️', 'desc' => 'Achou um adesivo da categoria Praia.', 'desbloqueada' => $c_praia > 0],
        ['nome' => 'Guia Turístico', 'icone' => '🗺️', 'desc' => 'Interagiu com 3 adesivos Turísticos.', 'desbloqueada' => $c_turistico >= 3],
        ['nome' => 'Decorador', 'icone' => '🛋️', 'desc' => 'Achou um adesivo de Móveis.', 'desbloqueada' => $c_moveis > 0],
        ['nome' => 'Passaporte', 'icone' => '✈️', 'desc' => 'Registrou de Estados/Internacionais.', 'desbloqueada' => $c_estados_int > 0],
        ['nome' => 'Semeador', 'icone' => '🌱', 'desc' => 'Colou 5 adesivos no mapa.', 'desbloqueada' => $total_criados >= 5],
        ['nome' => 'Dono da Rua', 'icone' => '🚧', 'desc' => 'Colou 15 adesivos no mapa.', 'desbloqueada' => $total_criados >= 15],
        ['nome' => 'Olheiro', 'icone' => '👁️', 'desc' => 'Fez 10 registros no total.', 'desbloqueada' => $c_total >= 10],
        ['nome' => 'Caçador Implacável', 'icone' => '🦅', 'desc' => 'Fez 50 registros no mapa.', 'desbloqueada' => $c_total >= 50],
        ['nome' => 'Top Model', 'icone' => '🤳', 'desc' => 'Fez 10 selfies (Conquistados).', 'desbloqueada' => $c_conquistado >= 10],
        ['nome' => 'Fotógrafo', 'icone' => '📸', 'desc' => 'Fez 10 fotos do local (Encontrados).', 'desbloqueada' => $c_encontrado >= 10],
        ['nome' => 'Galo da Madrugada', 'icone' => '☕', 'desc' => 'Registrou algo de manhãzinha.', 'desbloqueada' => $c_galo > 0],
        ['nome' => 'Rolezeiro', 'icone' => '🍻', 'desc' => 'Fez registro no Sábado ou Domingo.', 'desbloqueada' => $c_rolezeiro > 0],
        ['nome' => 'Caçador Recompensas', 'icone' => '💰', 'desc' => 'Completou 3 Missões Semanais.', 'desbloqueada' => $total_missoes >= 3],
        ['nome' => 'Caça-Raros', 'icone' => '💎', 'desc' => 'Interagiu com 5 adesivos Raros.', 'desbloqueada' => $c_raro >= 5],
        ['nome' => 'Crítico de Arte', 'icone' => '💬', 'desc' => 'Deixou 5 comentários nos registros.', 'desbloqueada' => $c_comentarios >= 5],
    ];
}
?>