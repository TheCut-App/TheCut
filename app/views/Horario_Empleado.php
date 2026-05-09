<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Horario Empleado</title>
    <link rel="stylesheet" href="public/assets/css/style_horario.css">
</head>
<body>

<div class="horario-contenedor">
    
    <img src="<?= $empleado['url_foto'] ?: 'public/assets/img/logo.png' ?>" class="avatar-mini" alt="Foto">
    <h1 class="titulo-horario">HORARIO DE <?= htmlspecialchars(strtoupper($empleado['nombre'])) ?></h1>

    <div class="nav-semana">
        <a href="index.php?accion=horario_empleado&id=<?= $empleado['id'] ?>&semana=<?= $semanaAnterior ?>" class="btn-nav-semana">&lt;</a>
        <span class="texto-semana"><?= date('d/m', strtotime($lunes)) ?> - <?= date('d/m', strtotime($domingo)) ?></span>
        <a href="index.php?accion=horario_empleado&id=<?= $empleado['id'] ?>&semana=<?= $semanaSiguiente ?>" class="btn-nav-semana">&gt;</a>
    </div>

    <form action="index.php?accion=guardar_horario" method="POST">
        <input type="hidden" name="id_usuario" value="<?= $empleado['id'] ?>">
        <input type="hidden" name="fecha_inicio" value="<?= $lunes ?>">
        <input type="hidden" name="fecha_fin" value="<?= $domingo ?>">
        
        <div class="lista-dias">
            <?php 
            $nombresDias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            $fecha_iterador = new DateTime($lunes);
            
            for ($i = 0; $i < 7; $i++): 
                $fechaActualStr = $fecha_iterador->format('Y-m-d');
                $trabaja = in_array($fechaActualStr, $fechasTrabajo);
            ?>
                <div class="fila-dia">
                    <span class="nombre-dia">
                        <?= $nombresDias[$i] ?> <span style="color:#888; font-size:0.8rem; margin-left:10px;"><?= $fecha_iterador->format('d/m') ?></span>
                    </span>
                    <label class="switch">
                        <input type="checkbox" name="fechas[]" value="<?= $fechaActualStr ?>" <?= $trabaja ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            <?php 
                $fecha_iterador->modify('+1 day');
            endfor; 
            ?>
        </div>

        <div class="footer-btns">
            <a href="index.php?accion=gestion_equipo" class="btn-cancelar">VOLVER</a>
            <button type="submit" class="btn-guardar">GUARDAR SEMANA</button>
        </div>
    </form>
</div>
<script>
    // Detectamos si en la URL existe el parámetro msg=ok
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('msg') === 'ok') {
        alert('Horario actualizado correctamente.');
        
        // Limpiamos la URL para que el mensaje no vuelva a salir si el usuario refresca la página
        const nuevaUrl = window.location.href.replace('&msg=ok', '');
        window.history.replaceState({}, document.title, nuevaUrl);
    }
</script>
</body>
</html>