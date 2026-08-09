<?php
header('Content-Type: application/json');

$db_file = 'banco.sqlite'; 
$upload_dir = '../uploads/'; 

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome_local = $_POST['nomeLocal'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lng = $_POST['lng'] ?? '';
    $criador_id = $_POST['criador_id'] ?? ''; 
    $foto = $_FILES['foto'] ?? null;

    if (empty($criador_id)) {
        throw new Exception("Você precisa estar logado para colar um adesivo.");
    }

    if (empty($nome_local) || empty($lat) || empty($lng) || !$foto) {
        throw new Exception("Preencha todos os campos e envie uma foto.");
    }

    $codigo = '#' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // --- NOVA LÓGICA DE RARIDADE POR DISTÂNCIA DE SÃO PAULO ---
    $lat_sp = -23.5505;
    $lon_sp = -46.6333;
    
    $lat_adesivo = (float) $lat;
    $lon_adesivo = (float) $lng;

    // Cálculo de Distância (Fórmula de Haversine) em Quilômetros
    $raio_terra = 6371;
    
    $dLat = deg2rad($lat_adesivo - $lat_sp);
    $dLon = deg2rad($lon_adesivo - $lon_sp);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat_sp)) * cos(deg2rad($lat_adesivo)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distancia = $raio_terra * $c; 

    // Define a raridade baseada nos Km de distância
    if ($distancia <= 100) {
        $raridade = 'Comum';
    } elseif ($distancia <= 700) {
        $raridade = 'Raro';
    } else {
        $raridade = 'Lendário';
    }

    $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid('adesivo_') . '.' . $extensao; 
    $caminho_destino = $upload_dir . $novo_nome;

    if (move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
        
        $caminho_banco = 'uploads/' . $novo_nome;

        $stmt = $pdo->prepare("INSERT INTO adesivos (codigo, criador_id, nome_local, lat, lng, foto_original, raridade) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$codigo, $criador_id, $nome_local, $lat, $lng, $caminho_banco, $raridade]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo salvo com sucesso!']);
    } else {
        throw new Exception("Erro ao salvar a imagem na pasta uploads.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>