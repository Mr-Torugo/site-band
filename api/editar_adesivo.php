<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    $id = $_POST['id'] ?? '';
    $usuario_id = $_POST['usuario_id'] ?? '';
    $nome_local = $_POST['nomeLocal'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $raridade = $_POST['raridade'] ?? ''; // Puxa a nova raridade!

    if(empty($id) || empty($usuario_id) || empty($nome_local) || empty($raridade)) {
        throw new Exception("Dados incompletos");
    }

    // Verifica se é admin
    $stmtAdmin = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtAdmin->execute([$usuario_id]);
    $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $is_admin = $user ? (bool)$user['is_admin'] : false;

    // Se for admin, atualiza qualquer um. Se não, só atualiza os próprios.
    if ($is_admin) {
        $stmt = $pdo->prepare("UPDATE adesivos SET nome_local = ?, categoria = ?, raridade = ? WHERE id = ?");
        $stmt->execute([$nome_local, $categoria, $raridade, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE adesivos SET nome_local = ?, categoria = ?, raridade = ? WHERE id = ? AND criador_id = ?");
        $stmt->execute([$nome_local, $categoria, $raridade, $id, $usuario_id]);
    }

    echo json_encode(['sucesso' => true, 'mensagem' => 'Adesivo atualizado com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>