<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Horarios Globales</title>
    <link rel="stylesheet" href="public/assets/css/style_horario_global.css">
</head>
<body>

<div class="contenedor-global">
    <div class="cabecera-global">
        <div>
            <a href="index.php?accion=admin_home" class="btn-volver">&lt; Volver al Panel</a>
            <h1 class="titulo titulo-ajustado">CUADRANTE DE HORARIOS</h1>
        </div>
        
        <div class="nav-semana">
            <a href="index.php?accion=horarios_globales&semana=<?= $semanaAnterior ?>" class="btn-nav">&lt;</a>
            <span class="texto-rango-semana"><?= date('d/m/Y', strtotime($lunes)) ?> - <?= date('d/m/Y', strtotime($domingo)) ?></span>
            <a href="index.php?accion=horarios_globales&semana=<?= $semanaSiguiente ?>" class="btn-nav">&gt;</a>
        </div>
    </div>

    <table class="tabla-horarios">
        <thead>
            <tr>
                <th class="celda-alineacion-izquierda">Empleado</th>
                <?php 
                $nombresDias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
                $fechaCabecera = new DateTime($lunes);
                
                for ($indiceDia = 0; $indiceDia < 7; $indiceDia++): 
                ?>
                    <th><?= $nombresDias[$indiceDia] ?><br><span class="texto-fecha-columna"><?= $fechaCabecera->format('d/m') ?></span></th>
                <?php 
                    $fechaCabecera->modify('+1 day');
                endfor; 
                ?>
                <th>Ajustes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipoSemanalo as $empleadoActual): ?>
            <tr>
                <td>
                    <div class="perfil-empleado">
                        <img src="<?= $empleadoActual['url_foto'] ?: 'public/assets/img/logo.png' ?>" class="foto-mini">
                        <strong><?= strtoupper($empleadoActual['nombre']) ?></strong>
                    </div>
                </td>
                
                <?php 
                $iteradorFecha = new DateTime($lunes);
                
                for ($indiceDia = 0; $indiceDia < 7; $indiceDia++): 
                    $fechaActualTexto = $iteradorFecha->format('Y-m-d');
                    $estaTrabajando = in_array($fechaActualTexto, $empleadoActual['fechas']);
                ?>
                    <td>
                        <span class="punto-dia <?= $estaTrabajando ? 'dia-trabaja' : 'dia-libre' ?>" 
                              title="<?= $estaTrabajando ? 'Trabaja' : 'Libre' ?>"></span>
                    </td>
                <?php 
                    $iteradorFecha->modify('+1 day');
                endfor; 
                ?>
                
                <td>
                    <a href="index.php?accion=horario_empleado&id=<?= $empleadoActual['id'] ?>&semana=<?= $lunes ?>" class="btn-editar-mini">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>