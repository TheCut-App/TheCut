<?php
// Validación estricta: Si no está logueado o no es admin, lo echamos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Acceso+denegado");
    exit;
}
// Los redirigimos al index pidiendo la acción admin.
if (!isset($datos)) {
    // Ajusta la ruta a tu index.php si es necesario
    header("Location: ../../index.php?accion=admin");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheCut - Panel de Administración</title>
    <link rel="stylesheet" href="../../public/assets/css/style_admin.css">
</head>
<body>
    <div class="admin-contenedor">
        
        <header class="admin-cabecera">
            <div class="cabecera-izq">
                <img src="../../public/assets/img/logo.png" alt="Logo" class="logo-pequeno">
                <h1 class="titulo-admin">ADMIN</h1>
            </div>
            
            <div class="cabecera-centro">
                <div class="caja-estadistica">MIS CITAS: 4</div>
                <div class="caja-estadistica">TOTALES: 24</div>
            </div>
            
            <div class="cabecera-der">
                <span class="fecha-actual">LUNES, 2 FEB 2026 - 19:45</span>
                <div class="avatar-admin">AC</div>
            </div>
        </header>

        <main class="admin-cuerpo">
            
            <section class="calendario-contenedor">
                <?php
                    // Los datos vienen inyectados desde el index.php
                    $barberos = $datos['barberos'];
                    $citas = $datos['citas_grid'];

                    // Función auxiliar para calcular la fila del grid según la hora (09:00 = fila 2)
                    function calcularFila($hora) {
                        $inicio = new DateTime('09:00');
                        $cita = new DateTime($hora);
                        $intervalo = $inicio->diff($cita);
                        $minutos = ($intervalo->h * 60) + $intervalo->i;
                        return ($minutos / 30) + 2; 
                    }
                    ?>

                    <div class="calendario-grid">
                        <div class="celda-cabecera"></div>
                        <?php foreach($barberos as $index => $nombre): ?>
                            <div class="celda-cabecera"><?php echo $nombre; ?></div>
                        <?php endforeach; ?>

                        <?php for($h=9; $h<=20; $h++): ?>
                            <div class="celda-hora" style="grid-row: <?php echo (($h-9)*2)+2; ?>"><?php echo "$h:00"; ?></div>
                            <div class="celda-hora" style="grid-row: <?php echo (($h-9)*2)+3; ?>"><?php echo "$h:30"; ?></div>
                        <?php endfor; ?>

 <?php foreach ($datos['citas_grid'] as $cita): ?>
    <div class="cita-bloque <?php echo $cita['color_clase']; ?>" 
         style="grid-column: <?php echo $cita['columna']; ?>; 
                grid-row: <?php echo $cita['fila']; ?> / span <?php echo $cita['duracion']; ?>;
                display: flex; flex-direction: column; justify-content: center; padding: 5px; overflow: hidden;">
        
        <div style="font-weight: bold; font-size: 0.85em; margin-bottom: 2px;">
            <?php echo $cita['hora_inicio']; ?> - <?php echo $cita['hora_fin']; ?>
        </div>
        
        <div style="font-weight: 800; text-transform: uppercase; font-size: 0.9em; line-height: 1;">
            <?php echo $cita['cliente']; ?>
        </div>
        
        <div style="font-style: italic; font-size: 0.75em; margin-top: 2px; line-height: 1.1;">
            <?php echo $cita['servicio']; ?>
        </div>
        
    </div>
<?php endforeach; ?>
                    </div>
            </section>

            <aside class="menu-lateral">
                <button class="boton-dorado btn-proxima">
                    <span class="icono">L</span> PRÓXIMA CITA DISPONIBLE
                </button>

                <div class="caja-menu">
                    <div class="caja-titulo">ACCIONES RÁPIDAS</div>
                    <ul class="caja-lista">
                        <li>Nueva Cita</li>
                        <li>Venta</li>
                        <li>Editar</li>
                    </ul>
                </div>

                <div class="caja-menu">
                    <div class="caja-titulo">GESTIÓN GLOBAL</div>
                    <ul class="caja-lista">
                        <li>Empleados</li>
                        <li>Clientes</li>
                        <li>Inventario</li>
                    </ul>
                </div>
            </aside>
            
        </main>
    </div>
</body>
</html>