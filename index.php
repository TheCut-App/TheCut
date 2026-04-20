<?php
/*
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
*/
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargamos las clases esenciales 
require_once 'app/config/db.php';
require_once 'app/models/Usuario.php';
require_once 'app/controllers/AuthController.php';

// Obtenemos la conexión a la base de datos
$conexion = Database::getConnection();

// Sistema de enrutamiento básico
$accion = $_GET['accion'] ?? 'mostrar_login';

if ($accion === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si el formulario se envía, instanciamos el controlador inyectando la conexión
    $auth = new AuthController($conexion);
    $auth->procesarLogin();
} elseif ($accion === 'admin') {
    // Si la URL pide 'admin', cargamos el controlador y la vista
    require_once 'app/models/Cita.php';
    require_once 'app/controllers/UsuarioController.php';

    $usuarioCtrl = new UsuarioController($conexion);
    $datos = $usuarioCtrl->mostrarPanelAdmin();
    
    require_once 'app/views/Adm_Home.php';

} elseif ($accion === 'invitado') {
    
    require_once 'app/views/Inv_Home.php';

} elseif ($accion === 'empleado') {
    
    require_once 'app/views/Emp_Home.php';
    
} else {
    // Si no se está enviando el formulario ni se pide admin, mostramos el login
    require_once 'app/views/login.php';
}
?>
