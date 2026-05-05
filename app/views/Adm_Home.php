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
// LÓGICA DE FECHAS PARA EL SELECTOR
$fechaActualStr = $datos['fecha_actual']; // Viene del controlador (ej: '2026-05-01')
$fechaObj = new DateTime($fechaActualStr);

$meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

$diaSemana = $dias[$fechaObj->format('w')];
$diaNum = $fechaObj->format('d');
$mes = $meses[$fechaObj->format('n') - 1];
$anio = $fechaObj->format('Y');

$fechaFormateada = strtoupper("$diaSemana, $diaNum $mes $anio");

// Calcular día anterior y siguiente para las flechas
$fechaActual = $datos['fecha_actual'];
$prevDate = date('Y-m-d', strtotime($fechaActual . ' - 1 day'));
$nextDate = date('Y-m-d', strtotime($fechaActual . ' + 1 day'));
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
            
            <div class="cabecera-der" style="display: flex; align-items: center; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Flecha Izquierda (Día anterior) -->
                    <a href="index.php?accion=admin&fecha=<?php echo $prevDate; ?>" 
                    style="color: #d4af37; text-decoration: none; font-size: 1.5rem; line-height: 1; cursor: pointer;">
                        &#9664;
                    </a>
                    
                    <!-- Fecha del Controlador -->
                    <span class="fecha-actual"><?php echo $datos['fecha_texto']; ?></span>
                    
                    <!-- Flecha Derecha (Día siguiente) -->
                    <a href="index.php?accion=admin&fecha=<?php echo $nextDate; ?>" 
                    style="color: #d4af37; text-decoration: none; font-size: 1.5rem; line-height: 1; cursor: pointer;">
                        &#9654;
                    </a>
                </div>
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
                <button id="btnAbrirModal" class="boton-dorado btn-proxima">
                    PRÓXIMA CITA DISPONIBLE
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
    <!-- Estructura del Popup (Modal) -->
    <div id="modalProximaCita" class="modal-oculto">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="btnCerrarModal">&times;</span>
            <h2 class="modal-titulo">Buscar Próxima Cita</h2>
            
            <div class="modal-cuerpo">
                <div id="resultadoBusqueda" style="text-align: center; color: #fff; font-size: 1.1rem; min-height: 100px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <p style="color: #ccc;">Buscando el mejor hueco en la agenda...</p>
                </div>
            </div>
            
        </div>
    </div>
    <!-- SEGUNDO POPUP: Confirmar Nueva Cita -->
    <div id="modalNuevaCita" class="modal-oculto">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="btnCerrarModalNueva">&times;</span>
            <h2 class="modal-titulo">AGENDAR CITA</h2>
            
            <div class="modal-cuerpo" style="color: #fff;">
                <div style="background: #2a2a2a; border-left: 4px solid #d4af37; padding: 15px; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0;"><strong>Barbero:</strong> <span id="txtNuevoBarbero" style="color: #d4af37;"></span></p>
                    <p style="margin: 0;"><strong>Hora:</strong> <span id="txtNuevaHora" style="color: #d4af37;"></span></p>
                </div>
                
                <!-- FORMULARIO DE NUEVA CITA -->
                <form id="formNuevaCita" action="index.php?accion=guardar_cita" method="POST">
                    <!-- Campos ocultos para enviar al servidor -->
                    <input type="hidden" id="inputNuevoBarbero" name="id_barbero">
                    <input type="hidden" id="inputNuevaHora" name="hora_cita">
                    <input type="hidden" name="fecha_cita" value="<?php echo $datos['fecha_actual']; ?>">

                    <!-- Selector de Cliente -->
                    <div style="margin-bottom: 15px;">
                        <label style="color: #d4af37; display: block; margin-bottom: 5px;">Cliente:</label>
                        <select name="id_cliente" required style="width: 100%; padding: 10px; background: #1a1a1a; color: #fff; border: 1px solid #d4af37; border-radius: 4px;">
                            <option value="">-- Selecciona un cliente --</option>
                            <?php foreach($datos['clientes'] as $cli): ?>
                                <option value="<?php echo $cli['id']; ?>">
                                    <?php echo htmlspecialchars($cli['nombre'] . ' ' . $cli['apellido_1']); ?> 
                                    (<?php echo htmlspecialchars($cli['telefono']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div style="text-align: right; margin-top: 5px;">
                            <a href="index.php?accion=nuevo_cliente" style="color: #d4af37; font-size: 0.85rem; text-decoration: none;">+ Crear nuevo cliente</a>
                        </div>
                    </div>

                    <!-- Selector de Servicios (Checkboxes) -->
                    <div style="margin-bottom: 20px;">
                        <label style="color: #d4af37; display: block; margin-bottom: 5px;">Servicios (puedes marcar varios):</label>
                        <div style="background: #2a2a2a; border: 1px solid #d4af37; border-radius: 4px; padding: 10px; max-height: 150px; overflow-y: auto;">
                            <?php foreach($datos['servicios'] as $srv): ?>
                                <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="servicios[]" value="<?php echo $srv['id']; ?>" id="srv_<?php echo $srv['id']; ?>">
                                    <label for="srv_<?php echo $srv['id']; ?>" style="cursor: pointer;">
                                        <?php echo htmlspecialchars($srv['nombre']); ?> 
                                        <span style="color: #d4af37; font-size: 0.9em;">(<?php echo $srv['precio']; ?>€)</span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="boton-dorado" style="width: 100%;">GUARDAR CITA EN AGENDA</button>
                </form>                
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalProxima = document.getElementById('modalProximaCita');
            const modalNueva = document.getElementById('modalNuevaCita');
            const divResultado = document.getElementById('resultadoBusqueda');

            // Función 1: Buscar hueco
            function buscarProximoHueco() {
                divResultado.innerHTML = '<p style="color: #ccc;">Buscando el mejor hueco en la agenda...</p>';
                
                fetch('index.php?accion=api_proxima_cita')
                    .then(response => response.json())
                    .then(data => {
                        if(data.encontrado) {
                            divResultado.innerHTML = `
                                <span style="color: #28a745; font-size: 1.4rem; font-weight: bold; margin-bottom: 15px;">¡Hueco Libre Encontrado!</span>
                                <div style="background: #2a2a2a; border: 1px solid #d4af37; padding: 15px; border-radius: 8px; width: 100%; margin-bottom: 20px;">
                                    <strong style="color: #d4af37;">BARBERO:</strong> ${data.barbero}<br>
                                    <strong style="color: #d4af37;">HORA:</strong> ${data.hora}
                                </div>
                                <!-- AHORA LLAMA A UNA FUNCIÓN JS PASANDO LOS DATOS -->
                                    <button onclick="abrirModalGestion(${data.id_barbero}, '${data.barbero}', '${data.hora}')" class="boton-dorado" style="width: 100%;">                                    GESTIONAR ESTE HUECO
                                </button>
                            `;
                        } else {
                            divResultado.innerHTML = '<span style="color: #dc3545; font-size: 1.2rem;">No hay huecos disponibles hoy.</span>';
                        }
                    });
            }

            // Función 2: Transición entre popups
            window.abrirModalGestion = function(idBarbero, nombreBarbero, horaAsignada) {
                // Cerramos el popup 1
                modalProxima.classList.remove('modal-activo');
                modalProxima.classList.add('modal-oculto');
                
                // Textos visuales para el usuario
                document.getElementById('txtNuevoBarbero').innerText = nombreBarbero;
                document.getElementById('txtNuevaHora').innerText = horaAsignada;

                // Datos ocultos para el formulario que va a la base de datos
                document.getElementById('inputNuevoBarbero').value = idBarbero;
                document.getElementById('inputNuevaHora').value = horaAsignada;
                
                // Abrimos el popup 2
                modalNueva.classList.remove('modal-oculto');
                modalNueva.classList.add('modal-activo');
            };
            // Lógica de apertura y cierre del Modal 1 (Búsqueda)
            document.getElementById('btnAbrirModal').addEventListener('click', function() {
                modalProxima.classList.remove('modal-oculto');
                modalProxima.classList.add('modal-activo');
                buscarProximoHueco();
            });
            document.getElementById('btnCerrarModal').addEventListener('click', function() {
                modalProxima.classList.remove('modal-activo');
                modalProxima.classList.add('modal-oculto');
            });

            // Lógica de cierre del Modal 2 (Gestión)
            document.getElementById('btnCerrarModalNueva').addEventListener('click', function() {
                modalNueva.classList.remove('modal-activo');
                modalNueva.classList.add('modal-oculto');
            });
        });
    </script>
</body>
</html>