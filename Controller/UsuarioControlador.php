<?php
namespace Controller;

use Model\Usuario;

class UsuarioControlador
{
    private Usuario $usuarioModelo;

    public function __construct()
    {
        $this->usuarioModelo = new Usuario();
    }

    public function validarSenha(string $senha): bool
    {
        return (bool) preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,33}$/', $senha);
    }

    public function senhasConferem(string $senha, string $confirmacao): bool
    {
        return hash_equals($senha, $confirmacao);
    }

    private function criptografar(string $senha): string
    {
        return password_hash($senha, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 17,
            'time_cost' => 4,
            'threads' => 2,
        ]);
    }

    public function buscarDados(int $id): array|false
    {
        return $this->usuarioModelo->buscarInfo($id);
    }

    public function criar(string $nome, string $email, string $senha): bool
    {
        return $this->usuarioModelo->registrar($nome, $email, $this->criptografar($senha));
    }

    public function existePorEmail(string $email): bool
    {
        return (bool) $this->usuarioModelo->buscarPorEmail($email);
    }

    public function entrar(string $email, string $senha): bool
    {
        $usuario = $this->usuarioModelo->buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['id'] = (int) $usuario['id'];
        $_SESSION['nome_completo'] = $usuario['nome_completo'];
        $_SESSION['email'] = $usuario['email'];

        return true;
    }

    public function estaLogado(): bool
    {
        return isset($_SESSION['id']) && (int) $_SESSION['id'] > 0;
    }
}
