<?php
header('Content-Type: application/json');
require_once 'conexao.php';

$usuario_id = $_POST['usuario_id'] ?? 0;

if (!$usuario_id || !isset($_FILES['foto_perfil'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Nenhuma imagem foi enviada.']);
    exit;
}

$foto = $_FILES['foto_perfil'];

if ($foto['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no upload da imagem.']);
    exit;
}

$extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
$extensoes_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($extensao, $extensoes_validas)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Formato inválido. Use JPG, PNG ou WEBP.']);
    exit;
}

// Cria a pasta uploads se não existir
$pasta_destino = dirname(__DIR__) . '/uploads/';
if (!is_dir($pasta_destino)) {
    mkdir($pasta_destino, 0777, true);
}

// Nome único: perfil_ID_timestamp.jpg
$novo_nome = 'perfil_' . $usuario_id . '_' . time() . '.' . $extensao;
$caminho_absoluto = $pasta_destino . $novo_nome;

if (move_uploaded_file($foto['tmp_name'], $caminho_absoluto)) {
    $caminho_banco = 'uploads/' . $novo_nome;
    
    // Atualiza a foto no banco de dados
    $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
    $stmt->execute([$caminho_banco, $usuario_id]);
    
    echo json_encode(['sucesso' => true, 'foto_perfil' => $caminho_banco]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar a imagem no servidor.']);
}
?>