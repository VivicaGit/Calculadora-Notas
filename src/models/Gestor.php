<?php
class Gestor
{
    private static ?PDO $instancia = null;

    private function __construct()
    {
    }

    public static function getConexion(): PDO
    {
        if (self::$instancia === null) {
            $host = 'db';
            $bd = 'calculadora_notas';
            $usuario = 'victor';
            $password = 'victor';

            $dsn = "mysql:host=$host;dbname=$bd;charset=utf8mb4";

            self::$instancia = new PDO($dsn, $usuario, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$instancia;
    }
}