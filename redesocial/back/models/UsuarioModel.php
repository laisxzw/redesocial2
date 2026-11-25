<?php
function buscarUsuarioPorEmail(PDO $con, string $email): ?array {
    $stmt = $con->prepare('SELECT id_usuario, nome, username, email, senha FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    return $usuario ?: null;
}

function buscarUsuarioPorUsername(PDO $con, string $username): ?array {
    $stmt = $con->prepare('SELECT id_usuario FROM usuarios WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $usuario = $stmt->fetch();
    return $usuario ?: null;
}

function inserirUsuario(PDO $con, array $dados): int {
    $stmt = $con->prepare('INSERT INTO usuarios (nome, username, email, senha) VALUES (?, ?, ?, ?)');
    $stmt->execute([$dados['nome'], $dados['username'], $dados['email'], $dados['senha']]);
    return (int) $con->lastInsertId();
}

function usuarioPublico(array $usuario): array {
    return [
        'id_usuario' => (int) $usuario['id_usuario'],
        'nome' => $usuario['nome'],
        'username' => $usuario['username'],
        'email' => $usuario['email'],
    ];
}
