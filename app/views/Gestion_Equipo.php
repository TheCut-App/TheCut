<?php
// Validación de sesión (Siguiendo tu estándar de Adm_Home.php)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Acceso+denegado");
    exit;
}

// Los datos vendrán del controlador en la variable $datos
// $datos['empleados'] será un array con: nombre, url_foto, total_servicios, total_productos, etc.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheCut - Gestión de Equipo</title>
    <link rel="stylesheet" href="../../public/assets/css/style_admin.css">
    <link rel="stylesheet" href="../../public/assets/css/style_equipo.css">
</head>
<body>
    <div class="admin-contenedor">
        
        <header class="admin-cabecera">
            <div class="cabecera-izq">
                <img src="../../public/assets/img/logo.png" alt="Logo" class="logo-pequeno">
                <h1 class="titulo-admin">GESTIÓN DE EQUIPO</h1>
            </div>
            
            <div class="cabecera-der">
                <button class="boton-artdeco" onclick="window.location.href='index.php?accion=nuevo_empleado'">
                    + Nuevo Empleado
                </button>
                <button class="boton-dorado" onclick="window.location.href='index.php?accion=admin'" style="padding: 10px 20px;">
                    VOLVER
                </button>
            </div>
        </header>

        <main class="equipo-grid">
            <?php if (isset($datos['empleados']) && !empty($datos['empleados'])): ?>
                <?php foreach ($datos['empleados'] as $emp): ?>
                    <div class="card-empleado">
                        <div class="marco-foto">
                            <img src="<?php echo $emp['url_foto'] ?: '../../public/assets/img/default-barber.png'; ?>" alt="Barbero">
                        </div>
                        
                        <h2 class="nombre-empleado"><?php echo strtoupper($emp['nombre']); ?></h2>
                        <div class="badge-estado">ACTIVO</div>

                        <div class="stats-contenedor">
                            <div class="stat-line">
                                <span>Servicios:</span>
                                <span><?php echo number_format($emp['total_servicios'], 2); ?>€</span>
                            </div>
                            <div class="stat-line">
                                <span>Productos:</span>
                                <span><?php echo number_format($emp['total_productos'], 2); ?>€</span>
                            </div>
                            <div class="total-mes">
                                Total Mes: <?php echo number_format($emp['total_servicios'] + $emp['total_productos'], 2); ?>€
                            </div>
                        </div>

                        <div class="acciones-equipo">
                            <button class="btn-accion" onclick="window.location.href='index.php?accion=editar_empleado&id=<?php echo $emp['id']; ?>'">Editar</button>
                            <button class="btn-accion" onclick="window.location.href='index.php?accion=horario_empleado&id=<?php echo $emp['id']; ?>'">Horario</button>
                            <button class="btn-accion btn-comisiones">Comisiones</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: white; text-align: center; grid-column: 1/-1;">No hay empleados registrados.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>