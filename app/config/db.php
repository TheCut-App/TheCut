<?php

class Database {
    private static $instancia = null;

    public static function getConnection() {
        if (self::$instancia === null) {
            // Recogemos tus variables de entorno
            $host     = getenv('DB_HOST');
            $port     = getenv('DB_PORT');
            $dbname   = getenv('DB_NAME');
            $user     = getenv('DB_USER');
            $password = getenv('DB_PASS');

            try {
                // Tu DSN con sslmode=require para Supabase
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
                
                self::$instancia = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

            } catch (PDOException $e) {
                // Si falla, detenemos todo con un mensaje claro
                die("Error crítico de conexión: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}