<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Editar Cita</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body class="body-centrado-superior">

<div class="contenedor-edicion">
    <div class="cabecera-edicion">
        <h1>EDITAR CITA</h1>
    </div>

    <form action="index.php?accion=actualizar_cita" method="POST">
        <input type="hidden" name="id_cita" value="<?= $datos['cita']['id'] ?>">
        
        <div class="cuadricula-layout">
            <div class="columna-izquierda">
                <span class="etiqueta-seccion">Cliente</span>
                
                <div class="avatar-cliente-edicion avatar-circulo">
                    <?= strtoupper(substr($datos['cita']['cliente_nombre'], 0, 1) . substr($datos['cita']['cliente_apellido'], 0, 1)) ?>
                </div>
                
                <div class="info-cliente-centrada">
                    <h2 class="nombre-cliente-texto"><?= $datos['cita']['cliente_nombre'] ?></h2>
                    <p class="telefono-cliente-texto"><?= $datos['cita']['cliente_tlf'] ?></p>
                </div>
                
                <span class="etiqueta-seccion etiqueta-separada">Notas Internas</span>
                <textarea name="notas" class="area-notas"><?= htmlspecialchars($datos['cita']['notas'] ?? '') ?></textarea>
            </div>

            <div class="columna-derecha">
                <span class="etiqueta-seccion">Servicios</span>
                <div class="lista-servicios-edicion">
                    <?php 
                    $idsServiciosContratados = array_column($datos['cita']['servicios_contratados'], 'id');
                    
                    foreach($datos['servicios'] as $servicioFila): 
                        $estaMarcado = in_array($servicioFila['id'], $idsServiciosContratados);
                    ?>
                        <label class="fila-servicio <?= $estaMarcado ? 'servicio-principal' : '' ?>">
                            <input type="checkbox" name="servicios[]" value="<?= $servicioFila['id'] ?>" data-duracion="<?= $servicioFila['duracion'] ?>" <?= $estaMarcado ? 'checked' : '' ?> class="checkbox-oculto check-servicio">
                            <span><?= $servicioFila['nombre'] ?></span>
                            <span><?= $servicioFila['precio'] ?>€</span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <span class="etiqueta-seccion">Profesional</span>
                <div class="pestanas-barberos" id="contenedorBarberosDisponibles">
                    <?php foreach($datos['barberos'] as $barberoOpcion): ?>
                        <div class="pestana-barbero <?= $barberoOpcion['id'] == $datos['cita']['id_usuario'] ? 'pestana-activa' : '' ?>" onclick="seleccionarBarbero(<?= $barberoOpcion['id'] ?>, this)">
                            <?= strtoupper($barberoOpcion['nombre']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="id_usuario" id="inputIdentificadorBarbero" value="<?= $datos['cita']['id_usuario'] ?>">

                <span class="etiqueta-seccion">Fecha y Hora</span>
                <div class="cuadricula-fecha-hora">
                    <input type="date" name="fecha" id="inputFechaEdicion" class="entrada-edicion" value="<?= date('Y-m-d', strtotime($datos['cita']['fecha_cita'])) ?>">
                    <div>
                        <input type="time" name="hora" id="inputHoraInicioEdicion" class="entrada-edicion" value="<?= date('H:i', strtotime($datos['cita']['fecha_cita'])) ?>">
                        <div class="texto-fin-estimado">Fin est.: <span id="textoHoraFinEstimada" class="hora-fin-resaltada">--:--</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="botones-pie">
            <button type="button" class="boton-eliminar-enlace" onclick="eliminarCitaSeleccionada()">ELIMINAR CITA</button>
            <div>
                <a href="index.php?accion=admin" class="enlace-cancelar">CANCELAR</a>
                <button type="submit" class="boton-guardar-edicion">GUARDAR CAMBIOS</button>
            </div>
        </div>
    </form>
</div>

<script>
    function seleccionarBarbero(idBarbero, elementoPestana) {
        document.querySelectorAll('.pestana-barbero').forEach(pestanaActual => pestanaActual.classList.remove('pestana-activa'));
        elementoPestana.classList.add('pestana-activa');
        document.getElementById('inputIdentificadorBarbero').value = idBarbero;
    }

    document.getElementById('inputFechaEdicion').addEventListener('change', async function() {
        const respuestaServidor = await fetch(`index.php?accion=api_barberos_fecha&fecha=${this.value}`);
        const listaBarberosDisponibles = await respuestaServidor.json();
        const contenedorPestanas = document.getElementById('contenedorBarberosDisponibles');
        const inputBarberoSeleccionado = document.getElementById('inputIdentificadorBarbero');
        
        contenedorPestanas.innerHTML = '';
        
        listaBarberosDisponibles.forEach(barberoActual => {
            const nuevaPestana = document.createElement('div');
            nuevaPestana.className = 'pestana-barbero' + (barberoActual.id == inputBarberoSeleccionado.value ? ' pestana-activa' : '');
            nuevaPestana.innerText = barberoActual.nombre.toUpperCase();
            nuevaPestana.onclick = () => seleccionarBarbero(barberoActual.id, nuevaPestana);
            contenedorPestanas.appendChild(nuevaPestana);
        });
    });

    function calcularHoraFinEstimada() {
        let totalMinutosServicios = 0;
        
        document.querySelectorAll('.check-servicio:checked').forEach(checkboxActual => {
            totalMinutosServicios += parseInt(checkboxActual.dataset.duracion);
        });
        
        const horaInicioSeleccionada = document.getElementById('inputHoraInicioEdicion').value;
        
        if (!horaInicioSeleccionada || totalMinutosServicios === 0) {
            return;
        }
        
        let objetoFechaCalculo = new Date();
        objetoFechaCalculo.setHours(...horaInicioSeleccionada.split(':'));
        objetoFechaCalculo.setMinutes(objetoFechaCalculo.getMinutes() + totalMinutosServicios);
        
        const horasFormateadas = objetoFechaCalculo.getHours().toString().padStart(2, '0');
        const minutosFormateados = objetoFechaCalculo.getMinutes().toString().padStart(2, '0');
        
        document.getElementById('textoHoraFinEstimada').innerText = horasFormateadas + ':' + minutosFormateados;
    }

    document.querySelectorAll('.check-servicio').forEach(checkboxActual => {
        checkboxActual.addEventListener('change', function() {
            this.parentElement.classList.toggle('servicio-principal', this.checked);
            calcularHoraFinEstimada();
        });
    });
    
    document.getElementById('inputHoraInicioEdicion').addEventListener('input', calcularHoraFinEstimada);
    
    calcularHoraFinEstimada();

    function eliminarCitaSeleccionada() {
        if (confirm('¿Borrar cita?')) {
            window.location.href = 'index.php?accion=eliminar_cita&id=<?= $datos['cita']['id'] ?>';
        }
    }
</script>
</body>
</html>