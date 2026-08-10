<?php
header('Content-Type: application/json');
$db_file = __DIR__ . '/banco.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try { $pdo->exec("ALTER TABLE adesivos ADD COLUMN categoria TEXT DEFAULT 'Urbano'"); } catch (Exception $e) {}

    $nome_local = $_POST['nomeLocal'] ?? '';
    $lat = (float)($_POST['lat'] ?? 0);
    $lng = (float)($_POST['lng'] ?? 0);
    $criador_id = $_POST['criador_id'] ?? '';
    $categoria = $_POST['categoria'] ?? 'Urbano'; 

    if (empty($nome_local) || empty($lat) || empty($lng) || empty($criador_id)) {
        throw new Exception("Preencha todos os campos obrigatórios.");
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("A foto do adesivo é obrigatória.");
    }

    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = 'adesivo_' . time() . '_' . rand(1000, 9999) . '.' . $extensao;
    $caminho_absoluto = __DIR__ . '/../uploads/' . $nome_arquivo;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_absoluto)) {
        throw new Exception("Erro ao salvar o arquivo de imagem.");
    }

    $foto_caminho = 'uploads/' . $nome_arquivo;

    // --- MÁGICA DA DISTÂNCIA (FÓRMULA DE HAVERSINE) ---
    // Coordenadas do Marco Zero de São Paulo
    $sp_lat = -23.550520;
    $sp_lng = -46.633308;

    $earth_radius = 6371; // Raio da Terra em KM
    $dLat = deg2rad($lat - $sp_lat);
    $dLon = deg2rad($lng - $sp_lng);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($sp_lat)) * cos(deg2rad($lat)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $distancia_km = $earth_radius * $c;

    // Define a Raridade com base na distância de SP
    if ($distancia_km <= 100) {
        $raridade = 'Comum';
    } elseif ($distancia_km <= 300) {
        $raridade = 'Raro';
    } else {
        $raridade = 'Lendário';
    }
    // ----------------------------------------------------

    $stmt = $pdo->prepare("INSERT INTO adesivos (nome_local, lat, lng, foto_original, criador_id, raridade, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome_local, $lat, $lng, $foto_caminho, $criador_id, $raridade, $categoria]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo Registrado com sucesso! Raridade: ' . $raridade]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>