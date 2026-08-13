<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$usuario_id = $_GET['usuario_id'] ?? 0;

if (!$usuario_id) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não identificado.']);
    exit;
}

try {
    // 1. Busca apenas os adesivos que o usuário COLOU (criador)
    $sqlColados = "
        SELECT 
            id, 
            COALESCE(codigo, id) AS codigo, 
            nome_local, 
            foto_original, 
            raridade, 
            data_criacao 
        FROM adesivos 
        WHERE criador_id = :uid
        ORDER BY data_criacao DESC
    ";
    $stmtColados = $pdo->prepare($sqlColados);
    $stmtColados->execute(['uid' => $usuario_id]);
    $colados = $stmtColados->fetchAll(PDO::FETCH_ASSOC);

    // 2. Busca apenas os adesivos que o usuário ENCONTROU (conquistado / 100% XP)
    $sqlAchados = "
        SELECT 
            a.id, 
            COALESCE(a.codigo, a.id) AS codigo, 
            a.nome_local, 
            a.foto_original, 
            a.raridade, 
            d.data_descoberta, 
            d.foto_selfie, 
            d.comentario
        FROM descobertas d
        JOIN adesivos a ON d.adesivo_id = a.id
        WHERE d.descobridor_id = :uid AND d.tipo_registro = 'conquistado'
        ORDER BY d.data_descoberta DESC
    ";
    $stmtAchados = $pdo->prepare($sqlAchados);
    $stmtAchados->execute(['uid' => $usuario_id]);
    $achados = $stmtAchados->fetchAll(PDO::FETCH_ASSOC);

    // Retorna o pacote exatamente com os nomes que o seu JavaScript espera!
    echo json_encode([
        'sucesso' => true,
        'colados' => $colados,
        'achados' => $achados
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
?>