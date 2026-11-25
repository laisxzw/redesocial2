<?php
require_once __DIR__ . '/../controllers/UsuarioController.php';
require_once __DIR__ . '/../controllers/publicacaoController.php';
require_once __DIR__ . '/../controllers/curtidaController.php';

function executarRotas(PDO $con): void {
    $rota = $_GET['rota'] ?? '';
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($rota === 'usuario') {
        if ($metodo === 'POST') criarUsuario($con);
        elseif ($metodo === 'GET' && ($_GET['acao'] ?? '') === 'me') consultarSessao();
        else {
            http_response_code(405);
            echo json_encode(['erro' => 'Operação não permitida para usuários.'], JSON_UNESCAPED_UNICODE);
        }
        return;
    }

    if ($rota === 'login' && $metodo === 'POST') {
        loginUsuario($con);
        return;
    }

    if ($rota === 'logout' && $metodo === 'POST') {
        logoutUsuario();
        return;
    }

    if ($rota === 'publicacao') {
        if ($metodo === 'GET') listarPosts($con);
        elseif ($metodo === 'POST') criarPost($con);
        elseif ($metodo === 'DELETE') excluirPost($con);
        else {
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
        }
        return;
    }

    if ($rota === 'curtida') {
        if ($metodo === 'GET') consultarCurtida($con);
        elseif ($metodo === 'POST') alternarCurtidaPost($con);
        else {
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
        }
        return;
    }

    if ($rota === 'teste' && $metodo === 'GET') {
        echo json_encode(['mensagem' => 'PHP e API local estão respondendo.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    http_response_code(404);
    echo json_encode(['erro' => 'Rota não encontrada.'], JSON_UNESCAPED_UNICODE);
}
