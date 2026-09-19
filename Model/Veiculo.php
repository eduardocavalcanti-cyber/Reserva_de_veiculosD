<?php
namespace Model;

use PDO;
use PDOException;

class Veiculo
{
    private PDO $banco;

    public function __construct()
    {
        $this->banco = Conexao::getInstancia();
    }

    public function listarTodos(): array|false
    {
        try {
            $sql = 'SELECT id, nome, diaria FROM veiculos ORDER BY diaria ASC';
            $stmt = $this->banco->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $erro) {
            error_log('Erro ao buscar veículos: ' . $erro->getMessage());
            return false;
        }
    }

    public function buscarPorId(int $id): array|false
    {
        try {
            $sql = 'SELECT id, nome, diaria FROM veiculos WHERE id = :id';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $erro) {
            error_log('Erro ao buscar veículo: ' . $erro->getMessage());
            return false;
        }
    }
}
