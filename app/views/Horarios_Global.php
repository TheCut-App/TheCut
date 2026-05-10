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
            <h1 class="titulo" style="margin-top: 10px;">CUADRANTE DE HORARIOS</h1>
        </div>
        
        <div class="nav-semana">
            <a href="index.php?accion=horarios_globales&semana=<?= $semanaAnterior ?>" class="btn-nav">&lt;</a>
            <span style="font-weight: bold; letter-spacing: 1px;"><?= date('d/m/Y', strtotime($lunes)) ?> - <?= date('d/m/Y', strtotime($domingo)) ?></span>
            <a href="index.php?accion=horarios_globales&semana=<?= $semanaSiguiente ?>" class="btn-nav">&gt;</a>
        </div>
    </div>

    <table class="tabla-horarios">
        <thead>
            <tr>
                <th style="text-align: left;">Empleado</th>
                <?php 
                $nombresDias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
                $fecha_cabecera = new DateTime($lunes);
                for ($i = 0; $i < 7; $i++): 
                ?>
                    <th><?= $nombresDias[$i] ?><br><span style="font-size: 0.75rem; color: #888;"><?= $fecha_cabecera->format('d/m') ?></span></th>
                <?php 
                    $fecha_cabecera->modify('+1 day');
                endfor; 
                ?>
                <th>Ajustes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipoSemanalo as $empleado): ?>
            <tr>
                <td>
                    <div class="perfil-empleado">
                        <img src="<?= $empleado['url_foto'] ?: 'public/assets/img/logo.png' ?>" class="foto-mini">
                        <strong><?= strtoupper($empleado['nombre']) ?></strong>
                    </div>
                </td>
                
                <?php 
                $fecha_iterador = new DateTime($lunes);
                for ($i = 0; $i < 7; $i++): 
                    $fechaStr = $fecha_iterador->format('Y-m-d');
                    $trabaja = in_array($fechaStr, $empleado['fechas']);
                ?>
                    <td>
                        <span class="punto-dia <?= $trabaja ? 'dia-trabaja' : 'dia-libre' ?>" 
                              title="<?= $trabaja ? 'Trabaja' : 'Libre' ?>"></span>
                    </td>
                <?php 
                    $fecha_iterador->modify('+1 day');
                endfor; 
                ?>
                
                <td>
                    <a href="index.php?accion=horario_empleado&id=<?= $empleado['id'] ?>&semana=<?= $lunes ?>" class="btn-editar-mini">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>