<?php
session_start();
require_once '../vendor/autoload.php';

use Controller\ReservaControlador;
use Controller\UsuarioControlador;
use Model\Veiculo;

$rc = new ReservaControlador();
$uc = new UsuarioControlador();
$vm = new Veiculo();

if (!$uc->estaLogado()) {
    header('Location: ../index.php');
    exit;
}

$idReserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idReserva || $idReserva <= 0) {
    header('Location: history.php');
    exit;
}

$reserva = $rc->buscar($idReserva, (int)$_SESSION['id']);
if (!$reserva) {
    header('Location: history.php');
    exit;
}

$veiculos = $vm->listarTodos() ?: [];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVeiculo = (int)($_POST['veiculo'] ?? 0);
    $inicio = trim((string)($_POST['data_inicio'] ?? ''));
    $fim = trim((string)($_POST['data_fim'] ?? ''));

    $erro = $rc->atualizar($idReserva, (int)$_SESSION['id'], $idVeiculo, $inicio, $fim) ?? '';

    if ($erro === '') {
        header('Location: history.php');
        exit;
    }

    $reserva['id_veiculo'] = $idVeiculo;
    $reserva['data_inicio'] = $inicio;
    $reserva['data_fim'] = $fim;
}

$usuario = $uc->buscarDados((int)$_SESSION['id']);
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/css/index.css">
    <title>Editar reserva - Reserva de Veículos</title>
</head>

<body>
    <header>
        <strong>Reserva de Veiculos</strong>
        <nav>
            <span class="muted"><?= htmlspecialchars($usuario['nome_completo'] ?? '') ?></span>
            <a href="home.php">Reservar</a>
            <a href="history.php">Historico</a>
            <a href="../index.php?logout=1">Sair</a>
        </nav>
    </header>
    <main>
        <h1>Editar reserva</h1>
        <p class="muted">Altere o veiculo ou o periodo da reserva.</p>

        <?php if ($erro): ?>
            <div class="card" style="margin-top:12px;background:#111;color:#fff"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="card" style="max-width:600px;margin-top:20px">
            <form method="POST" class="form">
                <label>Veiculo</label>
                <select name="veiculo" required>
                    <option value="">Escolha o veiculo</option>
                    <?php foreach ($veiculos as $v): ?>
                        <option value="<?= (int)$v['id'] ?>" <?= (int)$reserva['id_veiculo'] === (int)$v['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v['nome']) ?> - R$ <?= number_format((float)$v['diaria'], 2, ',', '.') ?>/dia
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Data inicio</label>
                <input type="date" name="data_inicio" value="<?= htmlspecialchars($reserva['data_inicio']) ?>" required>

                <label>Data fim</label>
                <input type="date" name="data_fim" value="<?= htmlspecialchars($reserva['data_fim']) ?>" required>

                <button style="margin-top:12px;width:100%">Salvar alterações</button>
            </form>
            <p style="margin-top:12px"><a href="history.php">Voltar ao historico</a></p>
        </div>
    </main>
</body>

</html>