
<?php

require_once __DIR__ . '/../models/UsuarioModel.php';

function dadosJson(): array {
    $dados = json_decode(file_get_contents('php://input'), true);
    return is_array($dados) ? $dados : [];
}

function criarUsuario(PDO $con): void {

    $dados = dadosJson();

    $nome = trim($dados['nome'] ?? '');
    $username = strtolower(trim($dados['username'] ?? ''));
    $email = strtolower(trim($dados['email'] ?? ''));
    $senha = $dados['senha'] ?? '';

    if ($nome === '' || $username === '' || $email === '' || $senha === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Preencha todos os campos.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Digite um e-mail válido.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!preg_match('/^[a-z0-9._]{3,30}$/', $username)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome de usuário inválido.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    if (strlen($senha) < 6) {
        http_response_code(400);
        echo json_encode(['erro' => 'A senha deve ter pelo menos 6 caracteres.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    if (buscarUsuarioPorEmail($con, $email)) {
        http_response_code(409);
        echo json_encode(['erro' => 'Este e-mail já está cadastrado.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    if (buscarUsuarioPorUsername($con, $username)) {
        http_response_code(409);
        echo json_encode(['erro' => 'Este nome de usuário já está em uso.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    try {

        $id = inserirUsuario($con, [
            'nome' => $nome,
            'username' => $username,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT)
        ]);

        http_response_code(201);

        echo json_encode([
            'mensagem' => 'Conta criada com sucesso!',
            'id_usuario' => $id
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            'erro' => 'Não foi possível cadastrar o usuário.',
            'detalhe' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function loginUsuario(PDO $con): void {

    $dados = dadosJson();

    $email = strtolower(trim($dados['email'] ?? ''));
    $senha = $dados['senha'] ?? '';

    $usuario = buscarUsuarioPorEmail($con, $email);

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        http_response_code(401);
        echo json_encode(['erro' => 'E-mail ou senha incorretos.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    session_regenerate_id(true);

    $_SESSION['usuario'] = usuarioPublico($usuario);

    echo json_encode([
        'mensagem' => 'Login realizado com sucesso!',
        'usuario' => $_SESSION['usuario']
    ], JSON_UNESCAPED_UNICODE);
}

function usuarioLogado(): ?array {
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin(): ?array {

    $usuario = usuarioLogado();

    if (!$usuario) {
        http_response_code(401);
        echo json_encode(['erro' => 'Faça login para continuar.'], JSON_UNESCAPED_UNICODE);
        return null;
    }

    return $usuario;
}

function consultarSessao(): void {
    $usuario = exigirLogin();

    if ($usuario) {
        echo json_encode(['usuario' => $usuario], JSON_UNESCAPED_UNICODE);
    }
}

function logoutUsuario(): void {

    $_SESSION = [];
    session_destroy();

    echo json_encode([
        'mensagem' => 'Você saiu da sua conta.'
    ], JSON_UNESCAPED_UNICODE);
}