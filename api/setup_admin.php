<?php
$db_file = __DIR__ . '/banco.sqlite';
try {
    $pdo = new PDO("sqlite:" . $db_file);
    // Cria a coluna is_admin silenciosamente caso não exista
    try { $pdo->exec("ALTER TABLE usuarios ADD COLUMN is_admin INTEGER DEFAULT 0"); } catch (Exception $e) {}
    
    $id = $_GET['id'] ?? '';
    if ($id) {
        $pdo->exec("UPDATE usuarios SET is_admin = 1 WHERE id = " . intval($id));
        echo "✅ Sucesso! O usuário com ID $id agora é um Administrador Supremo do Bando Map!";
    } else {
        echo "❌ Informe o seu ID na URL. Exemplo: setup_admin.php?id=1";
    }
} catch (Exception $e) { echo "Erro: " . $e->getMessage(); }
?>