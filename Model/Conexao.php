<?php
namespace Model;

require_once __DIR__ . '/../Config/configuracao.php';

use PDO;
use PDOException;

class Conexao
{
    private static ?PDO $instancia = null;

    public static function getInstancia(): PDO
    {
        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
                    'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $erro) {
                error_log('Erro na conexão com o banco: ' . $erro->getMessage());
                throw new PDOException('Não foi possível conectar ao banco de dados.');
            }
        }

        return self::$instancia;
    }
}
