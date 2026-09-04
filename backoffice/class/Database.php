<?php
class Database {

    private static $pdo = null;

    public static function get() {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            self::$pdo->exec("SET time_zone = '-03:00'");
        }
        return self::$pdo;
    }
}
