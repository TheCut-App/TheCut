<?php

class Database {
    
    private static $dbInstance = null;

    public static function getConnection(): PDO {
        if (self::$dbInstance === null) {
            $dbHost = getenv('DB_HOST');
            $dbPort = getenv('DB_PORT');
            $dbName = getenv('DB_NAME');
            $dbUser = getenv('DB_USER');
            $dbPassword = getenv('DB_PASS');

            try {
                $connectionString = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName;sslmode=require";
                
                self::$dbInstance = new PDO($connectionString, $dbUser, $dbPassword, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $connectionError) {
                die("Error crítico de conexión: " . $connectionError->getMessage());
            }
        }
        
        return self::$dbInstance;
    }
}