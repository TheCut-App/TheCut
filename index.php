<?php
require_once 'app/config/db.php';

$db = Database::getConnection();

// Consultamos el usuario que creamos antes
$sql = "SELECT * FROM usuarios WHERE username = :user";
$stmt = $db->prepare($sql); // Usamos $db en lugar de $pdo
$stmt->execute(['user' => 'admin']);
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Test Conexión</title>
    <style>
        body { background: #1a1a1a; color: #d4af37; font-family: serif; text-align: center; padding-top: 50px; }
        .card { border: 2px solid #d4af37; display: inline-block; padding: 20px; border-radius: 10px; }
    </style>
</head>
<body>
    <h1>Bienvenido a TheCut</h1>
    <div class="card">
        <?php if ($usuario): ?>
            <p>Conexión establecida con éxito.</p>
            <p>Usuario detectado: <strong><?php echo $usuario['nombre']; ?></strong></p>
            <p>Rol: <?php echo $usuario['is_admin'] ? 'Administrador' : 'Empleado'; ?></p>
            <a>
                <button onclick="window.location.href='app/views/login.php'">Ir a Login</button>
            </a>
        <?php else: ?>
            <p>No se encontró el usuario en la base de datos.</p>
        <?php endif; ?>
    </div>
</body>
</html>