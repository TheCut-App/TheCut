<?php
header('Content-Type: text/html; charset=utf-8');

// 1. Cargamos la configuración y el modelo
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/Usuario.php';

echo "<h1>🧪 Suite de Pruebas: CRUD Usuarios (Singleton Mode)</h1><hr>";

try {
    // 2. Instanciamos el modelo. 
    // Recuerda que ahora el constructor de Usuario() ya llama a Database::getConnection() solo.
    $usuarioModel = new Usuario();

    // --- 1. CREAR ---
    $username = "barbero_" . rand(100, 999);
    echo "<b>1. crearUsuario:</b> Creando '$username'... ";
    
    $usuarioModel->crearUsuario($username, '123', 'Luis', 'García'); 
    echo "✅ Hecho.<br>";

    // --- 2. BUSCAR ---
    $user = $usuarioModel->buscarPorUsername($username);
    if (!$user) {
        throw new Exception("No se pudo encontrar el usuario recién creado.");
    }
    $id = $user['id'];
    echo "<b>2. buscarPorUsername:</b> Encontrado con ID: $id<br>";

    // --- 3. ACTUALIZAR ---
    echo "<b>3. actualizarUsuario:</b> Modificando apellidos y foto... ";
    $nuevoApellido2 = "López";
    $nuevaFoto = "img/perfil_luis.jpg";

    $exitoUpdate = $usuarioModel->actualizarUsuario(
        $id, 
        $user['nombre'], 
        $user['apellido_1'], 
        $nuevoApellido2, 
        $user['is_admin'] === 't' || $user['is_admin'] === true, // Pequeño fix para tipos de datos
        $nuevaFoto
    );

    if ($exitoUpdate) {
        $userEditado = $usuarioModel->buscarPorUsername($username);
        echo "✅ Editado correctamente.<br>";
        echo "<i>Nuevo estado en DB: {$userEditado['nombre']} {$userEditado['apellido_1']} {$userEditado['apellido_2']}</i><br>";
    }

    // --- 4. TOGGLE ACTIVIDAD ---
    echo "<b>4. toggleActivo:</b> ";
    $usuarioModel->toggleActivo($id);
    
    // Volvemos a consultar para ver el cambio
    $estadoActual = $usuarioModel->esActivo($id);
    echo ($estadoActual ? "🟢 ACTIVO" : "🔴 INACTIVO") . " (Cambiado)<br>";

    // --- 5. ROL ---
    $adminStatus = $usuarioModel->esAdmin($id);
    echo "<b>5. esAdmin:</b> " . ($adminStatus ? "👑 Admin" : "💈 Empleado") . "<br>";

    echo "<hr><h2>✅ ¡Felicidades! Tu arquitectura Singleton y el Modelo funcionan.</h2>";

} catch (Exception $e) {
    echo "<div style='color:red; border:1px solid red; padding:10px;'>";
    echo "<b>❌ Error en el test:</b> " . $e->getMessage();
    echo "</div>";
    
    echo "<p><b>Sugerencia:</b> Revisa que las variables de entorno (DB_HOST, DB_USER, etc.) estén bien configuradas en tu servidor o archivo de entorno.</p>";
}