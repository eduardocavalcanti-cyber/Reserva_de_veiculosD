<?php
namespace Model;

use PDO;
use PDOException;

class Reserva
{
    private PDO $banco;

    public function __construct()
    {
        $this->banco = Conexao::getInstancia();
    }

    public function criar(int $idUsuario, int $idVeiculo, string $dataInicio, string $dataFim): bool
    {
        try {
            $sql = 'INSERT INTO reservas (id_usuario, id_veiculo, data_inicio, data_fim, criado_em)
                    VALUES (:idUsuario, :idVeiculo, :dataInicio, :dataFim, NOW())';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(':idVeiculo', $idVeiculo, PDO::PARAM_INT);
            $stmt->bindValue(':dataInicio', $dataInicio, PDO::PARAM_STR);
            $stmt->bindValue(':dataFim', $dataFim, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $erro) {
            error_log('Erro ao criar reserva: ' . $erro->getMessage());
            return false;
        }
    }

    public function buscarPorId(int $id, int $idUsuario): array|false
    {
        try {
            $sql = 'SELECT r.*, v.nome AS veiculo_nome, v.diaria
                    FROM reservas r
                    INNER JOIN veiculos v ON v.id = r.id_veiculo
                    WHERE r.id = :id AND r.id_usuario = :idUsuario';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $erro) {
            error_log('Erro ao buscar reserva: ' . $erro->getMessage());
            return false;
        }
    }

    public function atualizar(int $id, int $idUsuario, int $idVeiculo, string $dataInicio, string $dataFim): bool
    {
        try {
            $sql = 'UPDATE reservas
                    SET id_veiculo = :idVeiculo, data_inicio = :dataInicio, data_fim = :dataFim
                    WHERE id = :id AND id_usuario = :idUsuario';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(':idVeiculo', $idVeiculo, PDO::PARAM_INT);
            $stmt->bindValue(':dataInicio', $dataInicio, PDO::PARAM_STR);
            $stmt->bindValue(':dataFim', $dataFim, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $erro) {
            error_log('Erro ao atualizar reserva: ' . $erro->getMessage());
            return false;
        }
    }

    public function temConflito(int $idVeiculo, string $dataInicio, string $dataFim, ?int $idIgnorar = null): bool
    {
        try {
            $sql = 'SELECT 1 FROM reservas
                    WHERE id_veiculo = :idVeiculo
                    AND NOT (data_fim < :dataInicio OR data_inicio > :dataFim)';

            if ($idIgnorar !== null) {
                $sql .= ' AND id <> :idIgnorar';
            }

            $sql .= ' LIMIT 1';

            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':idVeiculo', $idVeiculo, PDO::PARAM_INT);
            $stmt->bindValue(':dataInicio', $dataInicio, PDO::PARAM_STR);
            $stmt->bindValue(':dataFim', $dataFim, PDO::PARAM_STR);

            if ($idIgnorar !== null) {
                $stmt->bindValue(':idIgnorar', $idIgnorar, PDO::PARAM_INT);
            }

            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $erro) {
            error_log('Erro ao verificar conflito: ' . $erro->getMessage());
            return true;
        }
    }

    public function listarHistorico(int $idUsuario): array|false
    {
        try {
            $sql = 'SELECT r.*, v.nome AS veiculo_nome, v.diaria
                    FROM reservas r
                    INNER JOIN veiculos v ON v.id = r.id_veiculo
                    WHERE r.id_usuario = :idUsuario
                    ORDER BY r.criado_em DESC, r.id DESC';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $erro) {
            error_log('Erro ao buscar reservas: ' . $erro->getMessage());
            return false;
        }
    }

    public function excluir(int $id, int $idUsuario): bool
    {
        try {
            $sql = 'DELETE FROM reservas WHERE id = :id AND id_usuario = :idUsuario';
            $stmt = $this->banco->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $erro) {
            error_log('Erro ao excluir reserva: ' . $erro->getMessage());
            return false;
        }
    }
}
