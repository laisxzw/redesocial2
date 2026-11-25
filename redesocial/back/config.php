
<?php

$host = '127.0.0.1';
$dbname = 'glownet_db';
$user = 'root';
$pass = ''; // Senha do MySQL do Laragon.

try {

    $con = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'erro' => 'Falha na conexão com o banco de dados.',
        'detalhe' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

    exit;
}