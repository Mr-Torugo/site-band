<?php
// OBRIGATÓRIO: Esta primeira linha indica ao servidor que isso é um código PHP

// Define que a resposta será no formato JSON para o JavaScript entender
header('Content-Type: application/json');

// Caminho do banco de dados (será criado na mesma pasta /api)
$db_file = 'banco.sqlite'; 

// Caminho da pasta onde as fotos serão salvas (uma pasta acima da /api)
$upload_dir = '../uploads/'; 

try {
    // 1. Conexão com o SQLite (cria o arquivo banco.sqlite se não existir)
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Criação da tabela (só executa se a tabela ainda não existir)
    $pdo->exec("CREATE TABLE IF NOT EXISTS adesivos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome_local TEXT,
        lat REAL,
        lng REAL,
        foto_caminho TEXT,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Recebe os dados enviados pelo JavaScript (FormData)
    $nome_local = $_POST['nomeLocal'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lng = $_POST['lng'] ?? '';
    $foto = $_FILES['foto'] ?? null;

    // Validação básica
    if (empty($nome_local) || empty($lat) || empty($lng) || !$foto) {
        throw new Exception("Preencha todos os campos e envie uma foto.");
    }

    // 4. Processamento e salvamento da imagem
    // Pega a extensão da imagem (jpg, png, etc) e cria um nome único para não sobrepor outras
    $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid('adesivo_') . '.' . $extensao; 
    
    // Caminho completo onde a imagem será salva no servidor
    $caminho_destino = $upload_dir . $novo_nome;

    // Move o arquivo temporário da memória do PHP para a pasta uploads
    if (move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
        
        // Caminho relativo que o HTML vai usar para exibir a imagem na tela
        $caminho_banco = 'uploads/' . $novo_nome;

        // 5. Salva no banco de dados SQLite
        $stmt = $pdo->prepare("INSERT INTO adesivos (nome_local, lat, lng, foto_caminho) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome_local, $lat, $lng, $caminho_banco]);

        // Retorna SUCESSO para o JavaScript
        echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo salvo com sucesso!']);
    } else {
        throw new Exception("Erro ao salvar a imagem na pasta uploads/. Verifique se a pasta existe.");
    }

} catch (Exception $e) {
    // Em caso de qualquer erro, retorna status 500 e a mensagem de erro
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>