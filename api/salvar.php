<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite';
$upload_dir = '../uploads/';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recebe os dados enviados pelo frontend
    $nome_local = $_POST['nomeLocal'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lng = $_POST['lng'] ?? '';
    $foto = $_FILES['foto'] ?? null;

    // ID temporário fixo até criarmos a tela de Login (1 = Vitor)
    $criador_id = 1; 

    if (empty($nome_local) || empty($lat) || empty($lng) || !$foto) {
        throw new Exception("Preencha todos os campos e envie uma foto.");
    }

    // Gera um código de colecionador aleatório (Ex: #0842)
    $codigo = '#' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Processamento da Imagem
    $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid('adesivo_') . '.' . $extensao; 
    $caminho_destino = $upload_dir . $novo_nome;

    if (move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
        
        $caminho_banco = 'uploads/' . $novo_nome;

        // INSERT adaptado para a nova estrutura relacional
        $stmt = $pdo->prepare("INSERT INTO adesivos (codigo, criador_id, nome_local, lat, lng, foto_original) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$codigo, $criador_id, $nome_local, $lat, $lng, $caminho_banco]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo salvo com sucesso!']);
    } else {
        throw new Exception("Erro ao salvar a imagem na pasta uploads.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>