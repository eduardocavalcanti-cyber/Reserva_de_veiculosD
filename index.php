<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}
require_once 'vendor/autoload.php';

use Controller\UsuarioControlador;

$c = new UsuarioControlador();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    $senha = (string)($_POST['password'] ?? '');
    if ($email !== '' && $senha !== '' && $c->entrar($email, $senha)) {
        header('Location: View/home.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="templates/css/login.css">
    <title>Reserva de Veiculos - Entrar</title>
</head>

<body>
    <form method="POST" class="box">
        <h1>Entrar</h1>
        <p class="muted">Reserva de veiculos</p>
        <?php if ($erro): ?><div class="msg err"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <label>Email</label>
        <input type="email" name="email" required placeholder="seu@email.com">
        <label>Senha</label>
        <input type="password" name="password" required placeholder="Sua senha">
        <button>Entrar</button>
        <p class="alt">Nao tem conta? <a href="View/register.php">Cadastre-se</a></p>
    </form>
</body>

</html>