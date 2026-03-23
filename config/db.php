<?php
$host     = getenv('DB_HOST');
$port     = getenv('DB_PORT');
$dbname   = getenv('DB_NAME');
$user     = getenv('DB_USER');
$password = getenv('DB_PASS');

try {
    // La conexión mágica para Supabase
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Si quieres comprobar que funciona, puedes descomentar la siguiente línea:
    // echo "¡Conexión establecida con éxito!";
} catch (PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}