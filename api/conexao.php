<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Caminho absoluto exato apontando para a pasta database
    $caminhoBanco = dirname(__DIR__) . '/database/banco.sqlite';
    
    // Mostra na tela o caminho exato para sabermos se o PHP está achando o lugar certo
    // echo "Conectando em: " . $caminhoBanco . "<br>";

    $pdo = new PDO("sqlite:" . $caminhoBanco);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Testa se a tabela usuarios realmente existe neste arquivo
    $resultado = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='usuarios'")->fetch();
    
    if (!$resultado) {
        die("⚠️ ERRO CRÍTICO: O arquivo do banco de dados foi encontrado, mas a tabela 'usuarios' NÃO EXISTE dentro dele! Este arquivo está vazio.");
    }

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>