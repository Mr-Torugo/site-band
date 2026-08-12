<?php
session_start();
session_destroy(); // Destrói todas as informações de sessão
header('Content-Type: application/json');
echo json_encode(['sucesso' => true, 'mensagem' => 'Deslogado com segurança.']);
?>