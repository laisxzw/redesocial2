<?php
function alternarCurtida(PDO $con, int $idUsuario, int $idPublicacao): bool {
    $verificar = $con->prepare('SELECT id_curtida FROM curtidas WHERE id_usuario = ? AND id_publicacao = ? LIMIT 1');
    $verificar->execute([$idUsuario, $idPublicacao]);
    $curtida = $verificar->fetch();

    if ($curtida) {
        $remover = $con->prepare('DELETE FROM curtidas WHERE id_curtida = ?');
        $remover->execute([$curtida['id_curtida']]);
        return false;
    }

    $inserir = $con->prepare('INSERT INTO curtidas (id_usuario, id_publicacao) VALUES (?, ?)');
    $inserir->execute([$idUsuario, $idPublicacao]);
    return true;
}
