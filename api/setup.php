<?php
header('Content-Type: application/json');

require_once 'conexao.php';

try {

    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        apelido TEXT UNIQUE NOT NULL,
        senha_hash TEXT NOT NULL,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS adesivos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        codigo TEXT UNIQUE,
        criador_id INTEGER,
        nome_local TEXT,
        cidade TEXT,
        lat REAL,
        lng REAL,
        foto_original TEXT,
        raridade TEXT DEFAULT 'Comum',
        status TEXT DEFAULT 'Ativo',
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (criador_id) REFERENCES usuarios(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS descobertas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        adesivo_id INTEGER,
        descobridor_id INTEGER,
        foto_selfie TEXT,
        comentario TEXT,
        data_descoberta DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (adesivo_id) REFERENCES adesivos(id),
        FOREIGN KEY (descobridor_id) REFERENCES usuarios(id),
        UNIQUE(adesivo_id, descobridor_id)
    )");

    echo json_encode(['sucesso' => true, 'mensagem' => 'Banco e tabelas criados com sucesso!']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>