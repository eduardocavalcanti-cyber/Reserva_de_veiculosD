<?php
namespace Model;

use PDO;
use PDOException;

class Usuario
{
    private PDO $banco;

    public function __construct()
    {
        $this->banco = Conexao::getInstancia();
    }

    public function registrar(string $nomeCompleto, string $email, string $senha): bool
    {
        try {
            $sql = 'INSERT INTO usuarios (nome_completo, email, senha) VALUES (:nome, :email, :senha)';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':nome', $nomeCompleto, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':senha', $senha, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $erro) {
            error_log('Erro ao registrar usuário: ' . $erro->getMessage());
            return false;
        }
    }

    public function buscarPorEmail(string $email): array|false
    {
        try {
            $sql = 'SELECT id, nome_completo, email, senha FROM usuarios WHERE email = :email LIMIT 1';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $erro) {
            error_log('Erro ao buscar usuário: ' . $erro->getMessage());
            return false;
        }
    }

    public function buscarInfo(int $id): array|false
    {
        try {
            $sql = 'SELECT nome_completo, email FROM usuarios WHERE id = :id LIMIT 1';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $erro) {
            error_log('Erro ao obter informações: ' . $erro->getMessage());
            return false;
        }
    }
}
