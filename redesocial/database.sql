CREATE DATABASE IF NOT EXISTS glownet_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE glownet_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS publicacoes (
    id_publicacao INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    legenda TEXT NOT NULL,
    imagem_url VARCHAR(500) NULL,
    data_publicacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_publicacoes_usuario
      FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS curtidas (
    id_curtida INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_publicacao INT UNSIGNED NOT NULL,
    data_curtida TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_usuario_publicacao UNIQUE (id_usuario, id_publicacao),
    CONSTRAINT fk_curtidas_usuario
      FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
      ON DELETE CASCADE,
    CONSTRAINT fk_curtidas_publicacao
      FOREIGN KEY (id_publicacao) REFERENCES publicacoes(id_publicacao)
      ON DELETE CASCADE
) ENGINE=InnoDB;
