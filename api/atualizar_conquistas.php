<?php
require_once 'conexao.php';

try {
    // Cria a tabela que vai guardar o histórico de conquistas
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuario_medalhas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        nome TEXT,
        icone TEXT,
        descricao TEXT,
        data_conquista DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    echo "<h1>Tabela de Conquistas pronta! 🏆</h1>";
    echo "<p>Agora o feed já tem de onde puxar as medalhas.</p>";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>