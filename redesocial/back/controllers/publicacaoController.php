<?php
require_once __DIR__ . '/../models/publicacaoModel.php';
require_once __DIR__ . '/UsuarioController.php';

function listarPosts(PDO $con): void {
    $usuario = exigirLogin();
    if (!$usuario) return;
    echo json_encode(listarPublicacoes($con, (int) $usuario['id_usuario']), JSON_UNESCAPED_UNICODE);
}

function criarPost(PDO $con): void {
    $usuario = exigirLogin();
    if (!$usuario) return;
    $dados = json_decode(file_get_contents('php://input'), true);
    $legenda = is_array($dados) ? trim((string) ($dados['legenda'] ?? '')) : '';
    if ($legenda === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Escreva algo antes de publicar.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    if (strlen($legenda) > 8000) {
        http_response_code(400);
        echo json_encode(['erro' => 'A publicação deve ter no máximo 2.000 caracteres.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $id = inserirPublicacao($con, (int) $usuario['id_usuario'], $legenda);
    http_response_code(201);
    echo json_encode(['mensagem' => 'Publicação criada!', 'id_publicacao' => $id], JSON_UNESCAPED_UNICODE);
}

function excluirPost(PDO $con): void {
    $usuario = exigirLogin();
    if (!$usuario) return;
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id || !removerPublicacao($con, (int) $id, (int) $usuario['id_usuario'])) {
        http_response_code(404);
        echo json_encode(['erro' => 'Publicação não encontrada ou você não é o autor.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    echo json_encode(['mensagem' => 'Publicação removida.'], JSON_UNESCAPED_UNICODE);
}
