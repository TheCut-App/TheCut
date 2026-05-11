<?php

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Acceso+denegado");
    exit;
}

if (!isset($datos)) {
    header("Location: ../../index.php?accion=admin");
    exit;
}

$fechaSeleccionada = $datos['fecha_actual']; 
$objetoFecha = new DateTime($fechaSeleccionada);

$nombresMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$nombresDias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

$nombreDiaSemana = $nombresDias[$objetoFecha->format('w')];
$numeroDiaMes = $objetoFecha->format('d');
$nombreMes = $nombresMeses[$objetoFecha->format('n') - 1];
$numeroAnio = $objetoFecha->format('Y');

$fechaTextoCabecera = strtoupper("$nombreDiaSemana, $numeroDiaMes $nombreMes $numeroAnio");

$fechaNavegacionActual = $datos['fecha_actual'];
$fechaDiaAnterior = date('Y-m-d', strtotime($fechaNavegacionActual . ' - 1 day'));
$fechaDiaSiguiente = date('Y-m-d', strtotime($fechaNavegacionActual . ' + 1 day'));

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
                <div class="caja-estadistica">MIS CITAS: <?php echo $datos['mis_citas']; ?></div>
                <div class="caja-estadistica">TOTALES: <?php echo $datos['totales']; ?></div>
            </div>
            
            <div class="cabecera-der contenedor-navegacion-fecha">
                <div class="contenedor-navegacion-fecha">
                    <a href="index.php?accion=admin&fecha=<?php echo $fechaDiaAnterior; ?>" class="flecha-navegacion">&#9664;</a>
                    <span class="fecha-actual"><?php echo $datos['fecha_texto']; ?></span>
                    <a href="index.php?accion=admin&fecha=<?php echo $fechaDiaSiguiente; ?>" class="flecha-navegacion">&#9654;</a>
                </div>
            </div>
        </header>

        <main class="admin-cuerpo">
            <section class="calendario-contenedor">
                <?php
                    $listaBarberos = $datos['barberos'];
                    $listaCitas = $datos['citas_grid'];

                    function calcularFilaGrid($horaEvaluacion) {
                        $horaInicioJornada = new DateTime('09:00');
                        $horaCita = new DateTime($horaEvaluacion);
                        $diferenciaIntervalo = $horaInicioJornada->diff($horaCita);
                        $totalMinutos = ($diferenciaIntervalo->h * 60) + $diferenciaIntervalo->i;
                        return ($totalMinutos / 30) + 2; 
                    }
                ?>

                <div class="calendario-grid" style="grid-template-columns: 60px repeat(<?php echo count($listaBarberos); ?>, 1fr);">
                    <div class="celda-cabecera"></div>
                    <?php foreach($listaBarberos as $indiceBarbero => $nombreBarbero): ?>
                        <div class="celda-cabecera"><?php echo $nombreBarbero; ?></div>
                    <?php endforeach; ?>

                    <?php for($horaBucle = 9; $horaBucle <= 20; $horaBucle++): ?>
                        <div class="celda-hora" style="grid-row: <?php echo (($horaBucle - 9) * 2) + 2; ?>"><?php echo "$horaBucle:00"; ?></div>
                        <div class="celda-hora" style="grid-row: <?php echo (($horaBucle - 9) * 2) + 3; ?>"><?php echo "$horaBucle:30"; ?></div>
                    <?php endfor; ?>

                    <?php foreach ($listaCitas as $citaActual): ?>
                        <div class="cita-bloque <?php echo $citaActual['color_clase']; ?> estilo-interior-cita" 
                            onclick="abrirModalDetalleEdicion(<?php echo $citaActual['id']; ?>, '<?php echo addslashes($citaActual['cliente']); ?>', '<?php echo addslashes($citaActual['servicio']); ?>', '<?php echo $citaActual['hora_inicio'] . ' - ' . $citaActual['hora_fin']; ?>', '<?php echo $citaActual['estado']; ?>')"
                            style="grid-column: <?php echo $citaActual['columna']; ?>; grid-row: <?php echo $citaActual['fila']; ?> / span <?php echo $citaActual['duracion']; ?>;">

                            <div class="texto-cita-hora">
                                <?php echo $citaActual['hora_inicio']; ?> - <?php echo $citaActual['hora_fin']; ?>
                            </div>
                            
                            <div class="texto-cita-cliente">
                                <?php echo $citaActual['cliente']; ?>
                            </div>
                            
                            <div class="texto-cita-servicio">
                                <?php echo $citaActual['servicio']; ?>
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <aside class="menu-lateral">
                <button id="btnAbrirModal" class="boton-dorado btn-proxima">PRÓXIMA CITA DISPONIBLE</button>

                <div class="caja-menu">
                    <div class="caja-titulo">ACCIONES RÁPIDAS</div>
                    <ul class="caja-lista">
                        <li class="enlace-menu-lateral" onclick="window.location.href='index.php?accion=nueva_cita'">Nueva Cita</li>
                        <li class="enlace-menu-lateral" onclick="window.location.href='index.php?accion=venta'">Venta</li>
                    </ul>
                </div>

                <div class="caja-menu">
                    <div class="caja-titulo">GESTIÓN GLOBAL</div>
                    <ul class="caja-lista">
                        <li class="enlace-menu-lateral" onclick="window.location.href='index.php?accion=gestion_equipo'">Gestión de Equipo</li>
                        <li class="enlace-menu-lateral" onclick="window.location.href='index.php?accion=horarios_globales'">Horarios Globales</li>
                        <li class="enlace-menu-lateral" onclick="window.location.href='index.php?accion=gestion_clientes'">Clientes</li>
                        <li class="enlace-menu-lateral" onclick="window.location.href='index.php?accion=inventario'">Inventario</li>
                    </ul>
                </div>
            </aside>
            
        </main>
    </div>

    <div id="modalProximaCita" class="modal-oculto">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="btnCerrarModal">&times;</span>
            <h2 class="modal-titulo">Buscar Próxima Cita</h2>
            
            <div class="modal-cuerpo">
                <div id="resultadoBusqueda" class="contenedor-resultado-busqueda">
                    <p class="texto-buscando">Buscando el mejor hueco en la agenda...</p>
                </div>
            </div>
            
        </div>
    </div>

    <div id="modalNuevaCita" class="modal-oculto">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="btnCerrarModalNueva">&times;</span>
            <h2 class="modal-titulo">AGENDAR CITA</h2>
            
            <div class="modal-cuerpo cuerpo-modal-blanco">
                <div class="caja-resumen-cita">
                    <p class="texto-resumen-cita"><strong>Barbero:</strong> <span id="txtNuevoBarbero" class="texto-dorado-resumen"></span></p>
                    <p class="texto-resumen-cita-final"><strong>Hora:</strong> <span id="txtNuevaHora" class="texto-dorado-resumen"></span></p>
                </div>
                
                <form id="formNuevaCita" action="index.php?accion=guardar_cita" method="POST">
                    <input type="hidden" id="inputNuevoBarbero" name="id_barbero">
                    <input type="hidden" id="inputNuevaHora" name="hora_cita">
                    <input type="hidden" name="fecha_cita" value="<?php echo $datos['fecha_actual']; ?>">

                    <div class="espaciado-formulario">
                        <label class="etiqueta-formulario">Cliente:</label>
                        <select name="id_cliente" required class="selector-formulario">
                            <option value="">-- Selecciona un cliente --</option>
                            <?php foreach($datos['clientes'] as $clienteActual): ?>
                                <option value="<?php echo $clienteActual['id']; ?>">
                                    <?php echo htmlspecialchars($clienteActual['nombre'] . ' ' . $clienteActual['apellido_1']); ?> 
                                    (<?php echo htmlspecialchars($clienteActual['telefono']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="contenedor-enlace-nuevo">
                            <a href="index.php?accion=nuevo_cliente" class="enlace-crear-cliente">+ Crear nuevo cliente</a>
                        </div>
                    </div>

                    <div class="espaciado-formulario-amplio">
                        <label class="etiqueta-formulario">Servicios (puedes marcar varios):</label>
                        <div class="contenedor-lista-servicios">
                            <?php foreach($datos['servicios'] as $servicioActual): ?>
                                <div class="fila-checkbox-servicio">
                                    <input type="checkbox" name="servicios[]" value="<?php echo $servicioActual['id']; ?>" id="srv_<?php echo $servicioActual['id']; ?>">
                                    <label for="srv_<?php echo $servicioActual['id']; ?>" class="etiqueta-checkbox">
                                        <?php echo htmlspecialchars($servicioActual['nombre']); ?> 
                                        <span class="precio-checkbox">(<?php echo $servicioActual['precio']; ?>€)</span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="boton-dorado boton-ancho-total">GUARDAR CITA EN AGENDA</button>
                </form>                
            </div>
        </div>
    </div>

    <div id="modalEditarCita" class="modal-oculto">
        <div class="modal-contenido modal-edicion-avanzada">
            
            <div class="borde-interior-dorado"></div>
            
            <span class="cerrar-modal cerrar-modal-edicion" id="btnCerrarModalEdicion">&times;</span>
            
            <h2 class="titulo-modal-edicion">EDITAR CITA</h2>
            
            <div class="cuerpo-modal-edicion">
                <p class="texto-detalles-edicion">
                    CLIENTE: <span id="modCli" class="valor-detalle-edicion"></span> | 
                    SERVICIO: <span id="modSrv" class="valor-detalle-edicion"></span> | 
                    HORA: <span id="modHora" class="valor-detalle-edicion"></span>
                </p>

                <div id="avisoPagada" class="aviso-cita-pagada">
                    ESTA CITA YA HA SIDO COBRADA Y NO SE PUEDE MODIFICAR
                </div>

                <div id="contenedorBotonesEdicion" class="contenedor-botones-accion">
                    
                    <div class="boton-accion-cita" onclick="ejecutarProcesoCita('confirmar')">
                        <div class="circulo-accion-cita">
                            <span class="icono-accion-cita color-confirmar">&#10004;</span>
                        </div>
                        <span class="texto-accion-cita">CONFIRMAR</span>
                    </div>

                    <div class="boton-accion-cita" onclick="ejecutarProcesoCita('editar')">
                        <div class="circulo-accion-cita">
                            <span class="icono-accion-cita color-editar">&#128398;</span>
                        </div>
                        <span class="texto-accion-cita">EDITAR</span>
                    </div>

                    <div class="boton-accion-cita" onclick="ejecutarProcesoCita('eliminar')">
                        <div class="circulo-accion-cita">
                            <span class="icono-accion-cita color-eliminar">&times;</span>
                        </div>
                        <span class="texto-accion-cita">ELIMINAR</span>
                    </div>

                </div>
            </div>
            
            <input type="hidden" id="modalIdCita">
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalBusquedaHueco = document.getElementById('modalProximaCita');
            const modalConfirmacionCita = document.getElementById('modalNuevaCita');
            const contenedorResultadosBusqueda = document.getElementById('resultadoBusqueda');

            function buscarHuecoDisponible() {
                contenedorResultadosBusqueda.innerHTML = '<p class="texto-buscando">Buscando el mejor hueco en la agenda...</p>';
                
                fetch('index.php?accion=api_proxima_cita')
                    .then(respuestaServidor => respuestaServidor.json())
                    .then(datosRespuesta => {
                        if(datosRespuesta.encontrado) {
                            contenedorResultadosBusqueda.innerHTML = `
                                <span class="texto-exito-busqueda">¡Hueco Libre Encontrado!</span>
                                <div class="caja-resumen-busqueda">
                                    <strong class="etiqueta-dorada">BARBERO:</strong> ${datosRespuesta.barbero}<br>
                                    <strong class="etiqueta-dorada">HORA:</strong> ${datosRespuesta.hora}
                                </div>
                                <button onclick="abrirModalGestionCita(${datosRespuesta.id_barbero}, '${datosRespuesta.barbero}', '${datosRespuesta.hora}')" class="boton-dorado boton-ancho-total">GESTIONAR ESTE HUECO</button>
                            `;
                        } else {
                            contenedorResultadosBusqueda.innerHTML = '<span class="texto-error-busqueda">No hay huecos disponibles hoy.</span>';
                        }
                    });
            }

            window.abrirModalGestionCita = function(identificadorBarbero, nombreDelBarbero, horaAsignada) {
                modalBusquedaHueco.classList.remove('modal-activo');
                modalBusquedaHueco.classList.add('modal-oculto');
                
                document.getElementById('txtNuevoBarbero').innerText = nombreDelBarbero;
                document.getElementById('txtNuevaHora').innerText = horaAsignada;

                document.getElementById('inputNuevoBarbero').value = identificadorBarbero;
                document.getElementById('inputNuevaHora').value = horaAsignada;
                
                modalConfirmacionCita.classList.remove('modal-oculto');
                modalConfirmacionCita.classList.add('modal-activo');
            };

            document.getElementById('btnAbrirModal').addEventListener('click', function() {
                modalBusquedaHueco.classList.remove('modal-oculto');
                modalBusquedaHueco.classList.add('modal-activo');
                buscarHuecoDisponible();
            });
            
            document.getElementById('btnCerrarModal').addEventListener('click', function() {
                modalBusquedaHueco.classList.remove('modal-activo');
                modalBusquedaHueco.classList.add('modal-oculto');
            });

            document.getElementById('btnCerrarModalNueva').addEventListener('click', function() {
                modalConfirmacionCita.classList.remove('modal-activo');
                modalConfirmacionCita.classList.add('modal-oculto');
            });
        });

        const modalEdicionCita = document.getElementById('modalEditarCita');
        
        window.abrirModalDetalleEdicion = function(identificadorCita, nombreCliente, nombreServicio, franjaHoraria, estadoCita) {
            document.getElementById('modalIdCita').value = identificadorCita;
            document.getElementById('modCli').innerText = nombreCliente;
            document.getElementById('modSrv').innerText = nombreServicio;
            document.getElementById('modHora').innerText = franjaHoraria;
            
            const contenedorAvisoPago = document.getElementById('avisoPagada');
            const grupoBotonesEdicion = document.getElementById('contenedorBotonesEdicion');

            if (estadoCita === 'Pagado') {
                contenedorAvisoPago.style.display = 'block';
                grupoBotonesEdicion.style.opacity = '0.3';
                grupoBotonesEdicion.style.pointerEvents = 'none';
            } else {
                contenedorAvisoPago.style.display = 'none';
                grupoBotonesEdicion.style.opacity = '1';
                grupoBotonesEdicion.style.pointerEvents = 'auto';
            }
            
            modalEdicionCita.classList.remove('modal-oculto');
            modalEdicionCita.classList.add('modal-activo');
        }

        document.getElementById('btnCerrarModalEdicion').addEventListener('click', function() {
            modalEdicionCita.classList.remove('modal-activo');
            modalEdicionCita.classList.add('modal-oculto');
        });

        window.ejecutarProcesoCita = function(tipoAccion) {
            const identificadorCitaSeleccionada = document.getElementById('modalIdCita').value;
            
            if (tipoAccion === 'eliminar') {
                if(confirm('¿Estás seguro de que deseas ELIMINAR esta cita? Esta acción no se puede deshacer.')) {
                    window.location.href = 'index.php?accion=eliminar_cita&id=' + identificadorCitaSeleccionada;
                }
            } else if (tipoAccion === 'confirmar') {
                window.location.href = 'index.php?accion=confirmar_cita&id=' + identificadorCitaSeleccionada;
            } else if (tipoAccion === 'editar') {
                window.location.href = 'index.php?accion=editar_cita&id=' + identificadorCitaSeleccionada;
            }
        };

        const elementoEntradaHora = document.getElementById('inputHoraInicio');
        const elementoSalidaHoraFin = document.getElementById('horaFinEstimada');
        
        const calculoDuracionMinutos = <?= $datos['cita']['duracion_total'] ?? 0 ?>;

        function calcularEstimacionHoraFin() {
            if (!elementoEntradaHora || !elementoEntradaHora.value || calculoDuracionMinutos === 0) return;
            
            const [horasActuales, minutosActuales] = elementoEntradaHora.value.split(':').map(Number);
            
            let objetoFechaTemporal = new Date();
            objetoFechaTemporal.setHours(horasActuales, minutosActuales, 0);
            
            objetoFechaTemporal.setMinutes(objetoFechaTemporal.getMinutes() + calculoDuracionMinutos);
            
            const formatoHoras = String(objetoFechaTemporal.getHours()).padStart(2, '0');
            const formatoMinutos = String(objetoFechaTemporal.getMinutes()).padStart(2, '0');
            
            elementoSalidaHoraFin.innerText = formatoHoras + ':' + formatoMinutos;
        }

        if (elementoEntradaHora) {
            calcularEstimacionHoraFin();
            elementoEntradaHora.addEventListener('input', calcularEstimacionHoraFin);
        }
    </script>
</body>
</html>