<?php
require_once __DIR__ . '/../models/curtidaModel.php';
require_once __DIR__ . '/../models/publicacaoModel.php';
require_once __DIR__ . '/UsuarioController.php';

function alternarCurtidaPost(PDO $con): void {
    $usuario = exigirLogin();
    if (!$usuario) return;
    $dados = json_decode(file_get_contents('php://input'), true);
    $idPublicacao = filter_var($dados['id_publicacao'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$idPublicacao) {
        http_response_code(400);
        echo json_encode(['erro' => 'Publicação inválida.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $post = buscarPublicacao($con, (int) $idPublicacao, (int) $usuario['id_usuario']);
    if (!$post) {
        http_response_code(404);
        echo json_encode(['erro' => 'Publicação não encontrada.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $curtiu = alternarCurtida($con, (int) $usuario['id_usuario'], (int) $idPublicacao);
    $postAtualizado = buscarPublicacao($con, (int) $idPublicacao, (int) $usuario['id_usuario']);
    echo json_encode([
        'curtiu' => $curtiu,
        'total_curtidas' => (int) $postAtualizado['total_curtidas'],
        'mensagem' => $curtiu ? 'Você curtiu a publicação.' : 'Curtida removida.',
    ], JSON_UNESCAPED_UNICODE);
}

function consultarCurtida(PDO $con): void {
    $usuario = exigirLogin();
    if (!$usuario) return;
    $id = filter_var($_GET['id_publicacao'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['erro' => 'Informe uma publicação válida.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $post = buscarPublicacao($con, (int) $id, (int) $usuario['id_usuario']);
    if (!$post) {
        http_response_code(404);
        echo json_encode(['erro' => 'Publicação não encontrada.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    echo json_encode(['total_curtidas' => (int) $post['total_curtidas'], 'curtiu' => (bool) $post['usuario_curtiu']], JSON_UNESCAPED_UNICODE);
}
