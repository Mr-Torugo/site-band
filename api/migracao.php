<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inicia uma transação de segurança (se algo der errado, ele desfaz tudo)
    $pdo->beginTransaction();

    // 1. Cria a nova tabela de descobertas SEM a trava UNIQUE
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS descobertas_nova (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            adesivo_id INTEGER,
            descobridor_id INTEGER,
            data_descoberta DATETIME DEFAULT CURRENT_TIMESTAMP,
            foto_selfie TEXT,
            comentario TEXT,
            tipo_registro TEXT DEFAULT 'conquistado',
            is_latest INTEGER DEFAULT 1
        )
    ");

    // 2. Copia todos os registros antigos para a nova tabela
    $pdo->exec("
        INSERT INTO descobertas_nova (id, adesivo_id, descobridor_id, data_descoberta, foto_selfie, comentario, tipo_registro, is_latest)
        SELECT id, adesivo_id, descobridor_id, data_descoberta, foto_selfie, comentario, tipo_registro, is_latest 
        FROM descobertas
    ");

    // 3. Apaga a tabela velha com a trava
    $pdo->exec("DROP TABLE descobertas");

    // 4. Renomeia a tabela nova para assumir o lugar oficial
    $pdo->exec("ALTER TABLE descobertas_nova RENAME TO descobertas");

    // Confirma as alterações no banco
    $pdo->commit();

    echo json_encode(['sucesso' => true, 'mensagem' => '🚀 Banco de dados atualizado! A trava UNIQUE foi removida com sucesso.']);

} catch (Exception $e) {
    // Se der erro, desfaz qualquer alteração pela metade
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>