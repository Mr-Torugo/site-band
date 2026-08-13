<?php
try {
    // Aponta exatamente para o banco de dados oficial na pasta "database"
    $caminhoBanco = dirname(__DIR__) . '/database/banco.sqlite';
    $pdo = new PDO("sqlite:" . $caminhoBanco);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Tabela de Usuários (senha_hash e titulo)
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        apelido TEXT NOT NULL,
        email TEXT UNIQUE,
        senha_hash TEXT NOT NULL,
        xp_total INTEGER DEFAULT 0,
        titulo TEXT DEFAULT 'Caçador Novato',
        is_admin INTEGER DEFAULT 0,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Tabela de Adesivos (Com o codigo da Hash)
    $pdo->exec("CREATE TABLE IF NOT EXISTS adesivos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        codigo TEXT,
        nome_local TEXT NOT NULL,
        categoria TEXT,
        raridade TEXT DEFAULT 'Comum',
        foto_original TEXT,
        lat REAL,
        lng REAL,
        criador_id INTEGER,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Tabela de Descobertas
    $pdo->exec("CREATE TABLE IF NOT EXISTS descobertas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        adesivo_id INTEGER,
        descobridor_id INTEGER,
        tipo_registro TEXT,
        foto_selfie TEXT,
        comentario TEXT,
        data_descoberta DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Tabela de Curtidas (Usada no Feed)
    $pdo->exec("CREATE TABLE IF NOT EXISTS curtidas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        tipo_acao TEXT,
        acao_id INTEGER,
        data_curtida DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Tabela de Comentários (Usada no Feed)
    $pdo->exec("CREATE TABLE IF NOT EXISTS comentarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        tipo_acao TEXT,
        acao_id INTEGER,
        comentario TEXT,
        data_comentario DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    echo "<h1>Setup Concluído com Sucesso! 🚀</h1>";
    echo "<p>Todas as tabelas foram criadas de forma segura no arquivo <b>database/banco.sqlite</b>.</p>";
    echo "<h3>O que fazer agora?</h3>";
    echo "<ul>";
    echo "<li>Como o banco está limpo, você precisa <b>criar uma conta nova</b>.</li>";
    echo "<li>Vá até a tela de registro (ou clique no link de 'Criar Conta' na tela de Login).</li>";
    echo "</ul>";
    echo "<p><a href='../login.html'>Ir para a tela de Login</a></p>";

} catch (PDOException $e) {
    echo "<h1 style='color:red;'>Erro crítico ao criar tabelas:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>