<?php
$host     = 'stkgkgveehgewxzfyccg.supabase.co';
$port     = '5432';
$dbname   = 'postgres';
$user     = 'postgres';
$password = 'x8YHtCCc8Y2joiBB';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Si falla, nos dirá por qué (muy útil en desarrollo)
    die("Error de conexión: " . $e->getMessage());
}