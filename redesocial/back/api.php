<?php
header('Content-Type: application/json; charset=utf-8');

// O front-end e o back-end são servidos pelo mesmo endereço local.
// Portanto, não é necessário liberar CORS (*).
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    exit;
}

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => false, // O projeto roda em http://localhost durante o desenvolvimento.
]);
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/routes/rotas.php';

try {
    executarRotas($con);
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'erro' => 'Erro interno no PHP.',
        'detalhe' => $e->getMessage(),
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
