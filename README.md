# Reserva de Veículos

Sistema acadêmico de reserva de veículos desenvolvido em PHP com MVC simples, orientação a objetos e banco de dados MySQL.

## Tecnologias

- PHP 8.3+
- MySQL
- PDO
- HTML5 e CSS3
- Bootstrap 5
- Composer
- Git e GitHub

## Configuração

O arquivo `Config/configuracao.php` lê as seguintes configurações do `.env`:

```text
DB_HOST=localhost
DB_PORT=3306
DB_NAME=reserva_veiculos
DB_USER=root
DB_PASSWORD=
```

Também existem valores padrão para desenvolvimento local caso o `.env` não exista.


## BANCO DE DADOS

CREATE DATABASE IF NOT EXISTS reserva_veiculos
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE reserva_veiculos;

-- TABELA DE USUARIOS

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABELA DE VEICULOS

CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    diaria DECIMAL(10,2) NOT NULL
);

-- Veiculos iniciais
INSERT INTO veiculos (nome, diaria)
VALUES
    ('Fiat Argo', 120.00),
    ('HB20', 140.00),
    ('Corolla', 250.00),
    ('Hilux', 350.00);

-- TABELA DE RESERVAS

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_veiculo INT NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reserva_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reserva_veiculo
        FOREIGN KEY (id_veiculo)
        REFERENCES veiculos(id)
        ON DELETE RESTRICT
);