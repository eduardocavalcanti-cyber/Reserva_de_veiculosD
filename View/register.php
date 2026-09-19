<?php
session_start();
require_once '../vendor/autoload.php';

use Controller\UsuarioControlador;

$c = new UsuarioControlador();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim((string)($_POST['email'] ?? ''));
    $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    $senha = $_POST['password'] ?? '';
    $conf = $_POST['confirmacao'] ?? '';
    if ($nome === '' || $email === '' || $senha === '') $erro = 'Preencha todos os campos corretamente.';
    elseif (!$c->senhasConferem($senha, $conf)) $erro = 'As senhas estão diferentes.';
    elseif (!$c->validarSenha($senha)) $erro = 'Senha: 8-33 caracteres, com maiúscula, minúscula e número.';
    elseif ($c->existePorEmail($email)) $erro = 'E-mail já cadastrado.';
    elseif ($c->criar($nome, $email, $senha)) {
        header('Location: ../index.php');
        exit;
    } else $erro = 'Erro ao registrar. Tente novamente.';
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/css/register.css">
    <title>Criar conta - Reserva de Veiculos</title>
</head>

<body>
    <form method="POST" class="box">
        <h1>Criar conta</h1>
        <p class="muted">Preencha seus dados</p>
        <?php if ($erro): ?><div class="msg err"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <label>Nome completo</label>
        <input name="nome" required placeholder="Seu nome">
        <label>Email</label>
        <input type="email" name="email" required placeholder="seu@email.com">
        <label>Senha</label>
        <input type="password" name="password" required placeholder="Sua senha">
        <label>Confirmar senha</label>
        <input type="password" name="confirmacao" required placeholder="Confirme sua senha">
        <button>Cadastrar</button>
        <p class="alt">Ja tem conta? <a href="../index.php">Faca login</a></p>
    </form>
</body>

</html>