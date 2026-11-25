<?php
function inserirPublicacao(PDO $con, int $idUsuario, string $legenda): int {
    $stmt = $con->prepare('INSERT INTO publicacoes (id_usuario, legenda) VALUES (?, ?)');
    $stmt->execute([$idUsuario, $legenda]);
    return (int) $con->lastInsertId();
}

function listarPublicacoes(PDO $con, int $idUsuario): array {
    $sql = "SELECT p.id_publicacao, p.id_usuario, p.legenda, p.imagem_url, p.data_publicacao,
                   u.nome AS autor, u.username,
                   (SELECT COUNT(*) FROM curtidas c WHERE c.id_publicacao = p.id_publicacao) AS total_curtidas,
                   EXISTS(SELECT 1 FROM curtidas c2 WHERE c2.id_publicacao = p.id_publicacao AND c2.id_usuario = ?) AS usuario_curtiu
            FROM publicacoes p
            INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
            ORDER BY p.data_publicacao DESC, p.id_publicacao DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$idUsuario]);
    return $stmt->fetchAll();
}

function buscarPublicacao(PDO $con, int $idPublicacao, int $idUsuario): ?array {
    $sql = "SELECT p.id_publicacao, p.id_usuario, p.legenda, p.imagem_url, p.data_publicacao,
                   u.nome AS autor, u.username,
                   (SELECT COUNT(*) FROM curtidas c WHERE c.id_publicacao = p.id_publicacao) AS total_curtidas,
                   EXISTS(SELECT 1 FROM curtidas c2 WHERE c2.id_publicacao = p.id_publicacao AND c2.id_usuario = ?) AS usuario_curtiu
            FROM publicacoes p
            INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
            WHERE p.id_publicacao = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->execute([$idUsuario, $idPublicacao]);
    $post = $stmt->fetch();
    return $post ?: null;
}

function removerPublicacao(PDO $con, int $idPublicacao, int $idUsuario): bool {
    $stmt = $con->prepare('DELETE FROM publicacoes WHERE id_publicacao = ? AND id_usuario = ?');
    $stmt->execute([$idPublicacao, $idUsuario]);
    return $stmt->rowCount() > 0;
}
