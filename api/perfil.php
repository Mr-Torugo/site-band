<?php
header('Content-Type: application/json');
require_once 'conexao.php';
require_once 'regras_negocio.php'; // Importamos nossas regras de jogo!

try {
    $alvo_id = $_GET['id'] ?? 0; 
    if (!$alvo_id) throw new Exception("Usuário não informado.");

    // Busca apenas o nome de forma simples
    $stmt = $pdo->prepare("SELECT apelido FROM usuarios WHERE id = ?"); 
    $stmt->execute([$alvo_id]); 
    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
    
    if (!$user) throw new Exception("Usuário não encontrado.");

    // Olha como fica limpo usar as funções que isolamos!
    $xp = calcularXP($pdo, $alvo_id);
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