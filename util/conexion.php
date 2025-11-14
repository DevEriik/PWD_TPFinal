<?php
class Conexion {
    /** @var PDO */
    private static $pdo = null;

    public static function obtenerConexion() {
        if (self::$pdo !== null) return self::$pdo;

        $cfg = include __DIR__ . '/../configuracion.php';
        $db  = $cfg['db'];

        $host = $db['host'] ?? '127.0.0.1';
        $port = $db['port'] ?? '3306';
        $name = $db['dbname'] ?? 'bdcarritocompras';
        $user = $db['user'] ?? 'root';
        $pass = $db['pass'] ?? '';
        $charset = $db['charset'] ?? 'latin1';

        $dsn = "mysql:host={$host};port={$port};dbname={$name}";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            if (!empty($charset)) {
                $sql = "SET NAMES " . $charset;
                self::$pdo->exec($sql);
            }

            return self::$pdo;

        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}