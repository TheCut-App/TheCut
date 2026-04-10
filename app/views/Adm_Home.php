<?php
session_start();
// Validación estricta: Si no está logueado o no es admin, lo echamos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Acceso+denegado");
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

                        <?php foreach($citas as $cita): 
                            $columna = array_search(strtoupper($cita['barbero']), $barberos) + 2;
                            $filaInicio = calcularFila($cita['hora_inicio']);
                            $filaFin = calcularFila($cita['hora_fin']);
                            $colorClase = ($cita['servicio'] == 'Corte') ? 'cita-verde' : 'cita-granate';
                        ?>
                            <div class="cita-bloque <?php echo $colorClase; ?>" 
                                style="grid-column: <?php echo $columna; ?>; 
                                        grid-row: <?php echo $filaInicio; ?> / <?php echo $filaFin; ?>;">
                                <strong><?php echo substr($cita['hora_inicio'], 0, 5); ?></strong><br>
                                <?php echo $cita['cliente']; ?> (<?php echo $cita['servicio']; ?>)
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