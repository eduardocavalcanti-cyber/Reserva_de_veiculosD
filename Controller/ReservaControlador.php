<?php
namespace Controller;

use Model\Reserva;
use Model\Veiculo;

class ReservaControlador
{
    private Reserva $reservaModelo;
    private Veiculo $veiculoModelo;

    public function __construct()
    {
        $this->reservaModelo = new Reserva();
        $this->veiculoModelo = new Veiculo();
    }

    public function validarDados(int $idVeiculo, string $dataInicio, string $dataFim): ?string
    {
        if ($idVeiculo <= 0) {
            return 'Selecione um veículo.';
        }

        if (!$this->veiculoModelo->buscarPorId($idVeiculo)) {
            return 'Veículo inválido.';
        }

        if (!$this->dataValida($dataInicio) || !$this->dataValida($dataFim)) {
            return 'Informe datas válidas.';
        }

        if ($dataInicio > $dataFim) {
            return 'A data de fim deve ser igual ou posterior à data de início.';
        }

        if ($dataInicio < date('Y-m-d')) {
            return 'A data de início não pode estar no passado.';
        }

        return null;
    }

    private function dataValida(string $data): bool
    {
        $dataFormatada = \DateTime::createFromFormat('Y-m-d', $data);
        return $dataFormatada !== false && $dataFormatada->format('Y-m-d') === $data;
    }

    public function salvar(int $idUsuario, int $idVeiculo, string $dataInicio, string $dataFim): bool
    {
        if ($this->reservaModelo->temConflito($idVeiculo, $dataInicio, $dataFim)) {
            return false;
        }

        return $this->reservaModelo->criar($idUsuario, $idVeiculo, $dataInicio, $dataFim);
    }

    public function buscar(int $id, int $idUsuario): array|false
    {
        return $this->reservaModelo->buscarPorId($id, $idUsuario);
    }

    public function atualizar(int $id, int $idUsuario, int $idVeiculo, string $dataInicio, string $dataFim): ?string
    {
        $erro = $this->validarDados($idVeiculo, $dataInicio, $dataFim);
        if ($erro) {
            return $erro;
        }

        if (!$this->reservaModelo->buscarPorId($id, $idUsuario)) {
            return 'Reserva não encontrada.';
        }

        if ($this->reservaModelo->temConflito($idVeiculo, $dataInicio, $dataFim, $id)) {
            return 'Veículo já reservado neste período.';
        }

        if (!$this->reservaModelo->atualizar($id, $idUsuario, $idVeiculo, $dataInicio, $dataFim)) {
            return 'Não foi possível atualizar a reserva.';
        }

        return null;
    }

    public function excluir(int $id, int $idUsuario): bool
    {
        return $this->reservaModelo->excluir($id, $idUsuario);
    }

    public function buscarHistorico(int $idUsuario): array
    {
        $reservas = $this->reservaModelo->listarHistorico($idUsuario);
        return $reservas ?: [];
    }
}
