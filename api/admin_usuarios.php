<?php
header('Content-Type: application/json');
require_once 'conexao.php'; // A conexão já possui o session_start()

try {
    // ==========================================
    // 🛡️ NOVA TRAVA DE SEGURANÇA (BLINDADA)
    // Olha direto na memória do servidor, ignorando o que vem do Javascript
    // ==========================================
    if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
        throw new Exception("Acesso negado! Você não é um administrador.");
    }

    // Pega a ação que o front-end está pedindo.
    // Se não enviar nenhuma ação (como era no seu código antigo), ele assume 'listar'
    $acao = $_POST['acao'] ?? $_GET['acao'] ?? 'listar';

    // 1️⃣ LISTAR TODOS OS USUÁRIOS (O seu código original adaptado)
    if ($acao === 'listar') {
        $stmt = $pdo->query("SELECT id, apelido, is_admin FROM usuarios ORDER BY id ASC");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Mantive a palavra 'dados' para não quebrar o seu Javascript atual
        echo json_encode(['sucesso' => true, 'dados' => $usuarios]);
    }
    
    // 2️⃣ DAR PODERES DE ADMIN
    elseif ($acao === 'promover') {
        $alvo_id = $_POST['alvo_id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE usuarios SET is_admin = 1 WHERE id = ?");
        $stmt->execute([$alvo_id]);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário promovido a Admin!']);
    }
    
    // 3️⃣ TIRAR PODERES DE ADMIN
    elseif ($acao === 'rebaixar') {
        $alvo_id = $_POST['alvo_id'] ?? 0;
        
        if ($alvo_id == $_SESSION['usuario_id']) {
            throw new Exception("Você não pode remover o seu próprio acesso de administrador.");
        }
        
        $stmt = $pdo->prepare("UPDATE usuarios SET is_admin = 0 WHERE id = ?");
        $stmt->execute([$alvo_id]);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Poderes de Admin removidos.']);
    }
    
    // 4️⃣ EXCLUIR USUÁRIO
    elseif ($acao === 'excluir') {
        $alvo_id = $_POST['alvo_id'] ?? 0;
        
        if ($alvo_id == $_SESSION['usuario_id']) {
            throw new Exception("Você não pode excluir a si mesmo do sistema.");
        }
        
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$alvo_id]);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso.']);
    }
    
    // ==========================================
    // 5️⃣ RESETAR SENHA (NOVO!)
    // ==========================================
    elseif ($acao === 'resetar_senha') {
        $alvo_id = $_POST['alvo_id'] ?? 0;
        $nova_senha = $_POST['nova_senha'] ?? '';
        
        if (empty($nova_senha)) {
            throw new Exception("A nova senha não pode ser vazia.");
        }

        // Criptografa a nova senha temporária
        $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Salva no banco de dados
        $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
        $stmt->execute([$senhaHash, $alvo_id]);
        
        echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso!']);
    }
    
    else {
        throw new Exception("Ação inválida no painel.");
    }

} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>