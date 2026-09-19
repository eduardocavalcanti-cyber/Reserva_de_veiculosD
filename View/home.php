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
$usuario = $uc->buscarDados($_SESSION['id']);
$veiculos = $vm->listarTodos() ?: [];
$msg = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVeiculo = (int)($_POST['veiculo'] ?? 0);
    $inicio = $_POST['data_inicio'] ?? '';
    $fim = $_POST['data_fim'] ?? '';
    $v = $rc->validarDados($idVeiculo, $inicio, $fim);
    if ($v) $erro = $v;
    elseif (!$rc->salvar($_SESSION['id'], $idVeiculo, $inicio, $fim)) $erro = 'Veículo já reservado neste período.';
    else $msg = 'Reserva realizada com sucesso.';
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/css/index.css">
    <title>Reservar - Reserva de Veiculos</title>
</head>

<body>
    <header>
        <strong>Reserva de Veiculos</strong>
        <nav>
            <span class="muted"><?= htmlspecialchars($usuario['nome_completo']) ?> - <?= htmlspecialchars($usuario['email']) ?></span>
            <a href="home.php">Reservar</a>
            <a href="history.php">Historico</a>
            <a href="../index.php">Sair</a>
        </nav>
    </header>
    <main>
        <h1>Reservar veiculo</h1>
        <p class="muted">Escolha o veiculo e o periodo</p>
        <?php if ($msg): ?><div class="card" style="margin-top:12px"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="card" style="margin-top:12px;background:#111;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <div class="grid">
            <div class="card">
                <h2>Nova reserva</h2>
                <form method="POST" class="form">
                    <label>Veiculo</label>
                    <select name="veiculo" required>
                        <option value="">Escolha o veiculo</option>
                        <?php foreach ($veiculos as $v): ?>
                            <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nome']) ?> - R$ <?= $v['diaria'] ?>/dia</option>
                        <?php endforeach; ?>
                    </select>
                    <label>Data inicio</label>
                    <input type="date" name="data_inicio" required>
                    <label>Data fim</label>
                    <input type="date" name="data_fim" required>
                    <button style="margin-top:12px;width:100%">Reservar</button>
                </form>
            </div>
            <div class="card">
                <h2>Veiculos disponiveis</h2>
                <div class="list">
                    <ul>
                        <?php foreach ($veiculos as $v): ?>
                            <li><?= htmlspecialchars($v['nome']) ?> - R$ <?= $v['diaria'] ?>/dia</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</body>

</html>