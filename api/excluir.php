<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try { 
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? '';
    $usuario_id = $data['usuario_id'] ?? '';

    if(empty($id) || empty($usuario_id)) throw new Exception("Dados incompletos");

    $stmtAdmin = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtAdmin->execute([$usuario_id]);
    $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $is_admin = $user ? (bool)$user['is_admin'] : false;

    if ($is_admin) {
        $stmt = $pdo->prepare("DELETE FROM adesivos WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM adesivos WHERE id = ? AND criador_id = ?");
        $stmt->execute([$id, $usuario_id]);
    }
    
    if ($stmt->rowCount() > 0) {
        $pdo->prepare("DELETE FROM descobertas WHERE adesivo_id = ?")->execute([$id]);
        echo json_encode(['sucesso' => true]);
    } else {
        throw new Exception("Você não tem permissão para excluir.");
    }

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>