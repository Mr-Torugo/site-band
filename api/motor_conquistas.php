<?php
// Traz o seu arquivo principal de regras para dentro do motor
require_once 'regras_negocio.php';

function checarEAtualizarMedalhas($pdo, $usuario_id) {
    
    // 1. O Motor "pergunta" para o seu regras_negocio.php quais medalhas o usuário já tem o direito de ganhar
    $todas_as_medalhas = processarMedalhas($pdo, $usuario_id);

    // 2. O Motor varre a lista que o regras_negocio devolveu
    foreach ($todas_as_medalhas as $medalha) {
        
        // Se a sua regra disse que ela está desbloqueada...
        if ($medalha['desbloqueada'] == true) {
            
            // Verifica se o usuário já tem essa medalha na nossa "gaveta" do Feed
            $stmtCheck = $pdo->prepare("SELECT id FROM usuario_medalhas WHERE usuario_id = ? AND nome = ?");
            $stmtCheck->execute([$usuario_id, $medalha['nome']]);
            
            // Se ele bateu a meta E ainda não tem a medalha registrada na gaveta...
            if (!$stmtCheck->fetch()) {
                // Ele insere no banco de dados agora mesmo e o Feed avisa todo mundo!
                $stmtInsert = $pdo->prepare("INSERT INTO usuario_medalhas (usuario_id, nome, icone, descricao) VALUES (?, ?, ?, ?)");
                $stmtInsert->execute([$usuario_id, $medalha['nome'], $medalha['icone'], $medalha['desc']]);
            }
        }
    }
}
?>