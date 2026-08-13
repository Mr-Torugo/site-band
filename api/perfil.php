<?php
header('Content-Type: application/json');
require_once 'conexao.php';
require_once 'regras_negocio.php'; // Importamos nossas regras de jogo!

try {
    $alvo_id = $_GET['id'] ?? 0; 
    if (!$alvo_id) throw new Exception("Usuário não informado.");

    // 👇 Busca o apelido e calcula o XP em TEMPO REAL (idêntico ao Ranking) 👇
    $sql = "SELECT u.apelido,
            COALESCE((SELECT SUM(CASE 
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'conquistado' THEN 10
                    WHEN a.raridade = 'Comum' AND d.tipo_registro = 'encontrado' THEN 5
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'conquistado' THEN 50
                    WHEN a.raridade = 'Raro' AND d.tipo_registro = 'encontrado' THEN 25
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'conquistado' THEN 1500
                    WHEN a.raridade = 'Lendário' AND d.tipo_registro = 'encontrado' THEN 750
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'conquistado' THEN 500
                    WHEN a.raridade = 'Tesouro' AND d.tipo_registro = 'encontrado' THEN 250
                    ELSE 0 END) FROM descobertas d JOIN adesivos a ON d.adesivo_id = a.id WHERE d.descobridor_id = u.id AND d.is_latest = 1), 0) + 
            COALESCE((SELECT SUM(xp_ganho) FROM missoes_concluidas WHERE usuario_id = u.id), 0) AS xp_real
        FROM usuarios u WHERE u.id = ?";
        
    $stmt = $pdo->prepare($sql); 
    $stmt->execute([$alvo_id]); 
    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
    
    if (!$user) throw new Exception("Usuário não encontrado.");

    // Alimentamos as variáveis com o valor calculado
    $xp = $user['xp_real'];
    $titulo = definirTitulo($xp);
    $medalhas = processarMedalhas($pdo, $alvo_id);

    echo json_encode([
        'sucesso' => true, 
        'perfil' => [
            'apelido' => $user['apelido'], 
            'titulo' => $titulo, 
            'xp' => $xp, 
            'medalhas' => $medalhas
        ]
    ]);

} catch (Exception $e) { 
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]); 
}
?>