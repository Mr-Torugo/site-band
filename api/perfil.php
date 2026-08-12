<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    try { $pdo->exec("CREATE TABLE IF NOT EXISTS missoes_concluidas (id INTEGER PRIMARY KEY AUTOINCREMENT, usuario_id INTEGER, ano_semana TEXT, xp_ganho INTEGER, data_conclusao DATETIME DEFAULT CURRENT_TIMESTAMP)"); } catch (Exception $e) {}
    $alvo_id = $_GET['id'] ?? 0; if (!$alvo_id) throw new Exception("Usuário não informado.");
    $sqlUser = "SELECT u.apelido, COALESCE((SELECT SUM(CASE 
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'conquistado' THEN 10 WHEN a.raridade = 'Comum' AND d.tipo_registro = 'encontrado' THEN 5
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'conquistado' THEN 50 WHEN a.raridade = 'Raro' AND d.tipo_registro = 'encontrado' THEN 25
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'conquistado' THEN 100 WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'encontrado' THEN 50
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'conquistado' THEN 500 WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'encontrado' THEN 250
                    ELSE 0 END) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = u.id AND d.is_latest = 1), 0) + 
        COALESCE((SELECT SUM(xp_ganho) FROM missoes_concluidas WHERE usuario_id = u.id), 0) AS xp_total FROM usuarios u WHERE u.id = ?";
    $stmt = $pdo->prepare($sqlUser); $stmt->execute([$alvo_id]); $user = $stmt->fetch(PDO::FETCH_ASSOC); if (!$user) throw new Exception("Usuário não encontrado.");
    $xp = (int)$user['xp_total']; $titulo = 'Novato'; if ($xp >= 500) $titulo = 'Lenda do Bando'; elseif ($xp >= 200) $titulo = 'Caçador Experiente'; elseif ($xp >= 50) $titulo = 'Explorador';
    $stmtCriados = $pdo->prepare("SELECT COUNT(*) FROM adesivos WHERE criador_id = ?"); $stmtCriados->execute([$alvo_id]); $total_criados = (int)$stmtCriados->fetchColumn();
    $stmtMissoes = $pdo->prepare("SELECT COUNT(*) FROM missoes_concluidas WHERE usuario_id = ?"); $stmtMissoes->execute([$alvo_id]); $total_missoes = (int)$stmtMissoes->fetchColumn();
    $stmtDesc = $pdo->prepare("SELECT d.tipo_registro, d.data_descoberta, d.comentario, a.categoria, a.raridade FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = ?"); $stmtDesc->execute([$alvo_id]); $descobertas = $stmtDesc->fetchAll(PDO::FETCH_ASSOC);
    $c_total = count($descobertas); $c_praia = 0; $c_turistico = 0; $c_moveis = 0; $c_estados_int = 0; $c_estrada = 0; $c_conquistado = 0; $c_encontrado = 0; $c_coruja = 0; $c_galo = 0; $c_rolezeiro = 0; $c_raro = 0; $c_lendario = 0; $c_comentarios = 0;
    foreach ($descobertas as $d) { $cat = $d['categoria']; $rar = $d['raridade']; $tipo = $d['tipo_registro']; $coment = trim($d['comentario']); $hora = (int)date('H', strtotime($d['data_descoberta'])); $dia_sem = (int)date('w', strtotime($d['data_descoberta']));
        if ($cat === 'Praia') $c_praia++; if ($cat === 'Turísticos') $c_turistico++; if ($cat === 'Móveis') $c_moveis++; if ($cat === 'Estados' || $cat === 'Internacionais') $c_estados_int++; if ($cat === 'Estrada') $c_estrada++;
        if ($rar === 'Raro') $c_raro++; if ($rar === 'Lendário' || $rar === 'Tesouro') $c_lendario++; // Tesouro conta como lenda para medalha
        if ($tipo === 'conquistado') $c_conquistado++; if ($tipo === 'encontrado') $c_encontrado++; if (!empty($coment)) $c_comentarios++;
        if ($hora >= 0 && $hora < 5) $c_coruja++; if ($hora >= 5 && $hora <= 9) $c_galo++; if ($dia_sem === 0 || $dia_sem === 6) $c_rolezeiro++;
    }
    $medalhas = [
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
    echo json_encode(['sucesso' => true, 'perfil' => ['apelido' => $user['apelido'], 'titulo' => $titulo, 'xp' => $xp, 'medalhas' => $medalhas]]);
} catch (Exception $e) { echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]); }
?>