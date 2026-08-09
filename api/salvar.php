<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite'; 
$upload_dir = '../uploads/'; 

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS adesivos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome_local TEXT,
        quem_colou TEXT,
        lat REAL,
        lng REAL,
        foto_caminho TEXT,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $nome_local = $_POST['nomeLocal'] ?? '';
    $quem_colou = $_POST['quemColou'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lng = $_POST['lng'] ?? '';
    $foto = $_FILES['foto'] ?? null;

    if (empty($nome_local) || empty($quem_colou) || empty($lat) || empty($lng) || !$foto) {
        throw new Exception("Preencha todos os campos e envie uma foto.");
    }

    $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid('adesivo_') . '.' . $extensao; 
    $caminho_destino = $upload_dir . $novo_nome;

    if (move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
        
        $caminho_banco = 'uploads/' . $novo_nome;

        $stmt = $pdo->prepare("INSERT INTO adesivos (nome_local, quem_colou, lat, lng, foto_caminho) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome_local, $quem_colou, $lat, $lng, $caminho_banco]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo salvo com sucesso!']);
    } else {
        throw new Exception("Erro ao salvar a imagem.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}   

?>