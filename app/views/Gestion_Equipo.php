<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Gestión de Equipo</title>
    <link rel="stylesheet" href="public/assets/css/style_equipo.css">
</head>
<body>

<div class="panel-equipo">
    <div class="cabecera-equipo">
        <h1>GESTIÓN DE EQUIPO</h1>
        <div class="cabecera-botones">
            <a href="index.php?accion=admin" class="btn-volver">VOLVER</a>
            <button class="btn-nuevo" onclick="window.location.href='index.php?accion=nuevo_empleado'">+ Nuevo Empleado</button>
        </div>
    </div>

    <div class="grid-empleados">
        <?php foreach ($datos['empleados'] as $emp): ?>
            <div class="tarjeta-empleado">
                
                <img src="<?= $emp['url_foto'] ?: 'public/assets/img/logo.png' ?>" class="foto-perfil" alt="Perfil">
                
                <h2 class="nombre-empleado"><?= htmlspecialchars($emp['nombre']) ?></h2>
                <div class="etiqueta-activo">ACTIVO</div>

                <div class="estadisticas">
                    Corte: <span><?= number_format($emp['total_servicios'], 2) ?>€</span><br>
                    Productos: <span><?= number_format($emp['total_productos'], 2) ?>€</span><br>
                    Total Mes: <span><?= number_format($emp['total_mes'], 2) ?>€</span>
                </div>

                <div class="contenedor-botones">
                    <button class="btn-accion" onclick="window.location.href='index.php?accion=editar_empleado&id=<?= $emp['id'] ?>'">Editar</button>
                    <button class="btn-accion" onclick="window.location.href='index.php?accion=horario_empleado&id=<?= $emp['id'] ?>'">Horario</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>