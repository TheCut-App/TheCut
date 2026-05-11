<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Nueva Cita</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body>
    <div class="admin-contenedor">
        <header class="admin-cabecera">
            <div class="cabecera-izq">
                <img src="../../public/assets/img/logo.png" alt="Logo" class="logo-pequeno">
                <h1 class="titulo-admin">NUEVA CITA</h1>
            </div>
            <a href="index.php?accion=admin" class="boton-dorado btn-volver-nueva-cita">VOLVER</a>
        </header>

        <form action="index.php?accion=guardar_cita" method="POST" id="formularioNuevaCita">
            <div class="nueva-cita-layout">
                
                <div class="col-seccion">
                    <div class="seccion-titulo">1. CLIENTE</div>
                    <input type="text" class="buscador-input" placeholder="Buscar cliente..." id="entradaBuscarCliente">
                    <div class="lista-seleccionable" id="contenedorListaClientes">
                        <?php foreach($datos['clientes'] as $clienteActual): ?>
                            <div class="item-seleccionable item-cliente" data-id="<?= $clienteActual['id'] ?>">
                                <?= strtoupper($clienteActual['nombre'] . ' ' . $clienteActual['apellido_1']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="boton-dorado btn-nuevo-cliente-cuadrado" onclick="window.location.href='index.php?accion=nuevo_cliente'">+ NUEVO CLIENTE</button>
                </div>

                <div class="col-seccion">
                    <div class="seccion-titulo">2. SERVICIO</div>
                    <div class="grid-servicios">
                        <?php foreach($datos['servicios'] as $servicioActual): ?>
                            <div class="btn-servicio" 
                                 data-id="<?= $servicioActual['id'] ?>" 
                                 data-duracion="<?= $servicioActual['duracion'] ?>"
                                 data-precio="<?= $servicioActual['precio'] ?>">
                                <?= $servicioActual['nombre'] ?> <br>
                                <small>(<?= $servicioActual['duracion'] ?> min)</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="contenedorInputsServiciosOcultos"></div>
                </div>

                <div class="col-seccion">
                    <div class="seccion-titulo">3. AGENDA DISPONIBLE</div>
                    <input type="date" name="fecha_cita" class="buscador-input" value="<?= date('Y-m-d') ?>" id="entradaFechaCita">
                    
                    <div class="contenedor-pestanas-barberos">
                        <?php foreach($datos['barberos_datos'] as $barberoActual): ?>
                            <div class="item-seleccionable barbero-tab tab-barbero-estilizado" data-id-barbero="<?= $barberoActual['id'] ?>">
                                <?= strtoupper($barberoActual['nombre']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="lista-seleccionable" id="contenedorListaHoras">
                        <p class="mensaje-estado-horas">Selecciona barbero y servicios para ver horas...</p>
                    </div>
                </div>
            </div>

            <div class="barra-resumen">
                <div id="contenedorTextoResumen">
                    Resumen: <span class="texto-resumen-destacado">Selecciona los datos de la cita...</span>
                </div>
                <input type="hidden" name="id_cliente" id="entradaIdClienteOculto">
                <input type="hidden" name="id_barbero" id="entradaIdBarberoOculto">
                <input type="hidden" name="hora_cita" id="entradaHoraCitaOculta">
                <button type="submit" class="btn-agendar btn-agendar-cita">AGENDAR CITA</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let datosClienteSeleccionado = null;
            let datosBarberoSeleccionado = null;
            let listaServiciosSeleccionados = [];
            
            const elementoTextoResumen = document.querySelector('#contenedorTextoResumen span');
            const elementoListaHoras = document.querySelector('#contenedorListaHoras');

            const elementoBuscarCliente = document.getElementById('entradaBuscarCliente');
            if(elementoBuscarCliente) {
                elementoBuscarCliente.addEventListener('input', function() {
                    const textoBusquedaActual = this.value.toLowerCase();
                    const nodosClientes = document.querySelectorAll('.item-cliente');

                    nodosClientes.forEach(nodoActual => {
                        const nombreCompletoCliente = nodoActual.innerText.toLowerCase();
                        if (nombreCompletoCliente.includes(textoBusquedaActual)) {
                            nodoActual.style.display = 'block';
                        } else {
                            nodoActual.style.display = 'none';
                        }
                    });
                });
            }

            document.querySelectorAll('.item-cliente').forEach(nodoCliente => {
                nodoCliente.addEventListener('click', function() {
                    document.querySelectorAll('.item-cliente').forEach(nodo => nodo.classList.remove('activo'));
                    this.classList.add('activo');
                    datosClienteSeleccionado = { id: this.dataset.id, nombre: this.innerText };
                    document.getElementById('entradaIdClienteOculto').value = this.dataset.id;
                    actualizarTextoResumen();
                });
            });

            document.querySelectorAll('.btn-servicio').forEach(botonServicio => {
                botonServicio.addEventListener('click', function() {
                    this.classList.toggle('activo');
                    const identificadorServicio = this.dataset.id;
                    
                    if (this.classList.contains('activo')) {
                        listaServiciosSeleccionados.push({
                            id: identificadorServicio,
                            nombre: this.innerText.split('\n')[0],
                            duracion: parseInt(this.dataset.duracion),
                            precio: parseFloat(this.dataset.precio) 
                        });
                    } else {
                        listaServiciosSeleccionados = listaServiciosSeleccionados.filter(servicio => servicio.id !== identificadorServicio);
                    }
                    actualizarCamposServiciosOcultos();
                    actualizarTextoResumen();
                    ejecutarBusquedaHuecosDisponibles();
                });
            });

            document.querySelectorAll('.barbero-tab').forEach(pestanaBarbero => {
                pestanaBarbero.addEventListener('click', function() {
                    document.querySelectorAll('.barbero-tab').forEach(pestana => pestana.classList.remove('activo'));
                    this.classList.add('activo');
                    datosBarberoSeleccionado = { id: this.dataset.idBarbero, nombre: this.innerText };
                    document.getElementById('entradaIdBarberoOculto').value = this.dataset.idBarbero;
                    actualizarTextoResumen();
                    ejecutarBusquedaHuecosDisponibles();
                });
            });

            function actualizarCamposServiciosOcultos() {
                const contenedorInputs = document.getElementById('contenedorInputsServiciosOcultos');
                contenedorInputs.innerHTML = '';
                listaServiciosSeleccionados.forEach(servicioActual => {
                    contenedorInputs.innerHTML += `<input type="hidden" name="servicios[]" value="${servicioActual.id}">`;
                });
            }

            function actualizarTextoResumen() {
                let textoResumenGenerado = "";
                if (datosClienteSeleccionado) {
                    textoResumenGenerado += `<b>${datosClienteSeleccionado.nombre}</b> | `;
                }
                
                if (listaServiciosSeleccionados.length > 0) {
                    const totalMinutosCalculado = listaServiciosSeleccionados.reduce((acumulador, servicio) => acumulador + servicio.duracion, 0);
                    const totalPrecioCalculado = listaServiciosSeleccionados.reduce((acumulador, servicio) => acumulador + servicio.precio, 0);
                    const nombresServiciosUnidos = listaServiciosSeleccionados.map(servicio => servicio.nombre).join(' + ');
                    
                    textoResumenGenerado += `${nombresServiciosUnidos} (${totalMinutosCalculado} min) - <span class="texto-precio-resumen">${totalPrecioCalculado.toFixed(2)}€</span> | `;
                }

                if (datosBarberoSeleccionado) {
                    textoResumenGenerado += `Barbero: ${datosBarberoSeleccionado.nombre}`;
                }
                
                elementoTextoResumen.innerHTML = textoResumenGenerado || "Selecciona los datos de la cita...";
            }

            async function ejecutarBusquedaHuecosDisponibles() {
                const fechaSeleccionada = document.getElementById('entradaFechaCita').value;
                const duracionTotalCalculada = listaServiciosSeleccionados.reduce((acumulador, servicio) => acumulador + servicio.duracion, 0);

                if (!datosBarberoSeleccionado || duracionTotalCalculada === 0 || !fechaSeleccionada) return;

                elementoListaHoras.innerHTML = '<p class="mensaje-estado-horas">Buscando huecos...</p>';

                try {
                    const respuestaServidor = await fetch(`index.php?accion=api_huecos_disponibles&id_barbero=${datosBarberoSeleccionado.id}&fecha=${fechaSeleccionada}&duracion=${duracionTotalCalculada}`);
                    const arrayHorasDisponibles = await respuestaServidor.json();

                    elementoListaHoras.innerHTML = '';
                    if (arrayHorasDisponibles.length === 0) {
                        elementoListaHoras.innerHTML = '<p class="mensaje-error-horas">No hay huecos seguidos para esa duración.</p>';
                        return;
                    }

                    arrayHorasDisponibles.forEach(horaActual => {
                        const elementoDivHora = document.createElement('div');
                        elementoDivHora.className = 'item-seleccionable item-hora-disponible';
                        elementoDivHora.innerText = horaActual;
                        elementoDivHora.onclick = function() {
                            document.querySelectorAll('.item-hora-disponible').forEach(nodo => nodo.classList.remove('activo'));
                            this.classList.add('activo');
                            document.getElementById('entradaHoraCitaOculta').value = horaActual;
                        };
                        elementoListaHoras.appendChild(elementoDivHora);
                    });
                } catch (errorPeticion) {
                    console.error("Error buscando huecos:", errorPeticion);
                }
            }

            document.getElementById('entradaFechaCita').addEventListener('change', ejecutarBusquedaHuecosDisponibles);

            const botonAgendarCita = document.querySelector('.btn-agendar-cita');

            botonAgendarCita.addEventListener('click', async (eventoClick) => {
                eventoClick.preventDefault();
                
                const valorIdCliente = document.getElementById('entradaIdClienteOculto').value;
                const valorIdBarbero = document.getElementById('entradaIdBarberoOculto').value;
                const valorFecha = document.getElementById('entradaFechaCita').value;
                const valorHora = document.getElementById('entradaHoraCitaOculta').value;
                
                const arrayIdsServicios = Array.from(document.querySelectorAll('input[name="servicios[]"]')).map(inputActual => inputActual.value);

                if (!valorIdCliente || !valorIdBarbero || !valorHora || arrayIdsServicios.length === 0) {
                    alert("Por favor, completa todos los pasos: Cliente, Servicio, Barbero y Hora.");
                    return;
                }

                const objetoFormData = new FormData();
                objetoFormData.append('id_cliente', valorIdCliente);
                objetoFormData.append('id_usuario', valorIdBarbero); 
                objetoFormData.append('fecha_cita', `${valorFecha} ${valorHora}`);
                arrayIdsServicios.forEach(idServicioActual => objetoFormData.append('servicios[]', idServicioActual));

                try {
                    const respuestaPeticion = await fetch('index.php?accion=agendar_cita', {
                        method: 'POST',
                        body: objetoFormData
                    });
                    
                    const resultadoJson = await respuestaPeticion.json();
                    if (resultadoJson.success) {
                        alert("¡Cita agendada con éxito!");
                        window.location.href = 'index.php?accion=admin'; 
                    } else {
                        alert("Error: " + resultadoJson.error);
                    }
                } catch (errorConexion) {
                    console.error(errorConexion);
                    alert("Hubo un problema al conectar con el servidor.");
                }
            });
        });
    </script>
</body>
</html>