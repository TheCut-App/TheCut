<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/Usuario.php';

$usuarioModel = new Usuario($pdo);

echo "<h1>🧪 Suite de Pruebas: CRUD Completo Usuarios</h1><hr>";

// 1. CREAR
$username = "barbero_" . rand(100, 999);
echo "<b>1. crearUsuario:</b> Creando '$username'... ";
$usuarioModel->crearUsuario($username, '123', 'Luis', 'García'); // Sin 2º apellido
echo "✅<br>";

// 2. BUSCAR
$user = $usuarioModel->buscarPorUsername($username);
$id = $user['id'];
echo "<b>2. buscarPorUsername:</b> Encontrado ID: $id<br>";

// 3. ACTUALIZAR (Simulamos que ahora sí sabemos su 2º apellido y su foto)
echo "<b>3. actualizarUsuario:</b> Modificando apellidos y foto... ";
$nuevoApellido2 = "López";
$nuevaFoto = "img/perfil_luis.jpg";

$exitoUpdate = $usuarioModel->actualizarUsuario(
    $id, 
    $user['nombre'], 
    $user['apellido_1'], 
    $nuevoApellido2, 
    $user['is_admin'], 
    $nuevaFoto
);

if ($exitoUpdate) {
    $userEditado = $usuarioModel->buscarPorUsername($username);
    echo "✅ Editado correctamente.<br>";
    echo "<i>Nuevo estado en DB: {$userEditado['nombre']} {$userEditado['apellido_1']} {$userEditado['apellido_2']}</i><br>";
}

// 4. TOGGLE ACTIVIDAD
echo "<b>4. toggleActivo:</b> ";
$usuarioModel->toggleActivo($id);
echo ($usuarioModel->esActivo($id) ? "🟢 ACTIVO" : "🔴 INACTIVO") . " (Cambiado)<br>";

// 5. ROL
echo "<b>5. esAdmin:</b> " . ($usuarioModel->esAdmin($id) ? "👑 Admin" : "💈 Empleado") . "<br>";

echo "<hr><h2>✅ ¡Felicidades! Tienes el CRUD de Usuarios terminado.</h2>";