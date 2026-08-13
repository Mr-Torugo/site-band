<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    // Busca os adesivos e conta quantas vezes cada um foi descoberto
    $sql = "
        SELECT 
            a.id, 
            a.codigo, 
            a.nome_local, 
            a.foto_original,
            COALESCE(u.apelido, 'Caçador Anônimo') AS quem_colou,
            COUNT(d.id) AS total_achados
        FROM adesivos a
        LEFT JOIN usuarios u ON a.criador_id = u.id
        LEFT JOIN descobertas d ON d.adesivo_id = a.id
        GROUP BY a.id
        ORDER BY total_achados DESC, a.id DESC
        LIMIT 20
    ";

    $stmt = $pdo->query($sql);
    $adesivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso' => true,
        'dados' => $adesivos
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
?>