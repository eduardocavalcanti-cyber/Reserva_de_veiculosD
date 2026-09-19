<?php
session_start();
require_once '../vendor/autoload.php';

use Controller\ReservaControlador;
use Controller\UsuarioControlador;

$rc = new ReservaControlador();
$uc = new UsuarioControlador();
if (!$uc->estaLogado()) {
    header('Location: ../index.php');
    exit;
}
$usuario = $uc->buscarDados($_SESSION['id']);
if (isset($_GET['excluir'])) {
    $rc->excluir((int)$_GET['excluir'], $_SESSION['id']);
    header('Location: history.php');
    exit;
}
$historico = $rc->buscarHistorico($_SESSION['id']);
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/css/history.css">
    <title>Historico - Reserva de Veiculos</title>
</head>

<body>
    <header>
        <strong>Reserva de Veiculos</strong>
        <nav>
            <span class="muted"><?= htmlspecialchars($usuario['nome_completo']) ?></span>
            <a href="home.php">Reservar</a>
            <a href="history.php">Historico</a>
            <a href="../index.php?logout=1">Sair</a>
        </nav>
    </header>
    <main>
        <h1>Historico</h1>
        <p class="muted"><?= count($historico) ?> reserva(s)</p>
        <?php if (!$historico): ?>
            <div class="card" style="margin-top:12px">Nenhuma reserva ainda.</div>
        <?php else: ?>
            <div class="cards">
                <?php foreach ($historico as $r): $dias = (strtotime($r['data_fim']) - strtotime($r['data_inicio'])) / 86400 + 1;
                    $total = $dias * $r['diaria']; ?>
                    <div class="card">
                        <small><?= htmlspecialchars($r['criado_em']) ?></small>
                        <h3><?= htmlspecialchars($r['veiculo_nome']) ?></h3>
                        <p><?= htmlspecialchars($r['data_inicio']) ?> ate <?= htmlspecialchars($r['data_fim']) ?></p>
                        <p class="muted"><?= (int)$dias ?> diaria(s) - R$ <?= number_format($total, 2, ',', '.') ?> (R$ <?= number_format((float)$r['diaria'], 2, ',', '.') ?>/dia)</p>
                        <div class="actions"><a href="edit.php?id=<?= (int)$r['id'] ?>">Editar</a> <a href="?excluir=<?= (int)$r['id'] ?>" onclick="return confirm('Deseja excluir esta reserva?')">Excluir</a></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>