<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Nueva Cita</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        /* Estilos específicos para esta página (puedes moverlos a style_admin.css luego) */
        .nueva-cita-layout {
            display: grid;
            grid-template-columns: 350px 1fr 400px;
            gap: 20px;
            height: 75vh;
            margin-top: 20px;
        }
        .col-seccion {
            background-color: rgba(26, 29, 32, 0.8);
            border: 1px solid var(--dorado-artdeco);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .seccion-titulo {
            background: var(--gris-carbon);
            color: var(--dorado-artdeco);
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--dorado-artdeco);
        }
        .buscador-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--dorado-artdeco);
            color: white;
            padding: 15px;
            width: 100%;
            outline: none;
        }
        .lista-seleccionable {
            overflow-y: auto;
            flex-grow: 1;
        }
        .item-seleccionable {
            padding: 12px 20px;
            border-bottom: 1px solid rgba(197, 160, 89, 0.2);
            cursor: pointer;
            transition: 0.2s;
        }
        .item-seleccionable:hover { background: rgba(197, 160, 89, 0.1); }
        .item-seleccionable.activo { background: var(--dorado-artdeco); color: var(--azul-profundo); }

        /* Servicios como botones */
        .grid-servicios { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 15px; }
        .btn-servicio {
            border: 1px solid var(--dorado-artdeco);
            padding: 15px;
            text-align: center;
            cursor: pointer;
            color: var(--champan);
            border-radius: 4px;
        }
        .btn-servicio.activo { background: var(--dorado-artdeco); color: var(--azul-profundo); }

        /* Barra Resumen */
        .barra-resumen {
            background: var(--azul-profundo);
            border: 2px solid var(--dorado-artdeco);
            margin-top: 20px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-agendar {
            background: #0f664a;
            color: white;
            border: none;
            padding: 15px 30px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-contenedor">
        <header class="admin-cabecera">
            <div class="cabecera-izq">
                <img src="../../public/assets/img/logo.png" alt="Logo" class="logo-pequeno">
                <h1 class="titulo-admin">NUEVA CITA</h1>
            </div>
            <a href="index.php?accion=admin" class="boton-dorado" style="text-decoration: none; padding: 10px 20px;">VOLVER</a>
        </header>

        <form action="index.php?accion=guardar_cita" method="POST" id="formNuevaCita">
            <div class="nueva-cita-layout">
                
                <!-- 1. CLIENTE -->
                <div class="col-seccion">
                    <div class="seccion-titulo">1. CLIENTE</div>
                    <input type="text" class="buscador-input" placeholder="Buscar cliente..." id="buscarCliente">
                    <div class="lista-seleccionable" id="listaClientes">
                        <?php foreach($datos['clientes'] as $cliente): ?>
                            <div class="item-seleccionable" data-id="<?= $cliente['id'] ?>">
                                <?= strtoupper($cliente['nombre'] . ' ' . $cliente['apellido_1']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="boton-dorado" style="border-radius: 0;" onclick="window.location.href='index.php?accion=nuevo_cliente'">+ NUEVO CLIENTE</button>
                </div>

                <!-- 2. SERVICIO -->
                <div class="col-seccion">
                    <div class="seccion-titulo">2. SERVICIO</div>
                    <div class="grid-servicios">
                        <?php foreach($datos['servicios'] as $servicio): ?>
                            <div class="btn-servicio" 
                                 data-id="<?= $servicio['id'] ?>" 
                                 data-duracion="<?= $servicio['duracion'] ?>"
                                 data-precio="<?= $servicio['precio'] ?>">
                                <?= $servicio['nombre'] ?> <br>
                                <small>(<?= $servicio['duracion'] ?> min)</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Inputs ocultos que se llenarán con JS -->
                    <div id="serviciosSeleccionadosInputs"></div>
                </div>

                <!-- 3. AGENDA -->
                <div class="col-seccion">
                    <div class="seccion-titulo">3. AGENDA DISPONIBLE</div>
                    <input type="date" name="fecha_cita" class="buscador-input" value="<?= date('Y-m-d') ?>" id="fechaCita">
                    
                    <!-- Pestañas Barberos -->
                    <div style="display: flex; background: var(--gris-carbon);">
                        <?php foreach($datos['barberos_datos'] as $barbero): ?>
                            <div class="item-seleccionable barbero-tab" style="flex:1; text-align:center; font-size: 0.8rem;" data-id-barbero="<?= $barbero['id'] ?>">
                                <?= strtoupper($barbero['nombre']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="lista-seleccionable" id="listaHoras">
                        <!-- Aquí se cargarán las horas vía AJAX -->
                        <p style="padding: 20px; color: gray; text-align: center;">Selecciona barbero y servicios para ver horas...</p>
                    </div>
                </div>
            </div>

            <!-- BARRA RESUMEN -->
            <div class="barra-resumen">
                <div id="resumenTexto">
                    Resumen: <span style="color: var(--dorado-artdeco)">Selecciona los datos de la cita...</span>
                </div>
                <input type="hidden" name="id_cliente" id="inputCliente">
                <input type="hidden" name="id_barbero" id="inputBarbero">
                <input type="hidden" name="hora_cita" id="inputHora">
                <button type="submit" class="btn-agendar">AGENDAR CITA</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
    let clienteSeleccionado = null;
    let barberoSeleccionado = null;
    let serviciosSeleccionados = [];
    
    const resumenTexto = document.querySelector('#resumenTexto span');
    const listaHoras = document.querySelector('#listaHoras');

    // 1. LÓGICA DE CLIENTES
    
    // A) Buscador de clientes en tiempo real
    const buscadorCliente = document.getElementById('buscarCliente');
    if(buscadorCliente) {
        buscadorCliente.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase();
            const itemsClientes = document.querySelectorAll('#listaClientes .item-seleccionable');

            itemsClientes.forEach(item => {
                const nombreCliente = item.innerText.toLowerCase();
                if (nombreCliente.includes(textoBusqueda)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // B) Seleccionar cliente al hacer clic
    document.querySelectorAll('#listaClientes .item-seleccionable').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('#listaClientes .item-seleccionable').forEach(i => i.classList.remove('activo'));
            this.classList.add('activo');
            clienteSeleccionado = { id: this.dataset.id, nombre: this.innerText };
            document.getElementById('inputCliente').value = this.dataset.id;
            actualizarResumen();
        });
    });

    // 2. LÓGICA DE SERVICIOS (Múltiple)
    document.querySelectorAll('.btn-servicio').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('activo');
            const id = this.dataset.id;
            
            if (this.classList.contains('activo')) {
                serviciosSeleccionados.push({
                    id: id,
                    nombre: this.innerText.split('\n')[0],
                    duracion: parseInt(this.dataset.duracion),
                    precio: parseFloat(this.dataset.precio) // Guardamos el precio
                });
            } else {
                serviciosSeleccionados = serviciosSeleccionados.filter(s => s.id !== id);
            }
            actualizarInputsServicios();
            actualizarResumen();
            buscarHuecosDisponibles();
        });
    });

    // 3. LÓGICA DE BARBEROS
    document.querySelectorAll('.barbero-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.barbero-tab').forEach(t => t.classList.remove('activo'));
            this.classList.add('activo');
            barberoSeleccionado = { id: this.dataset.idBarbero, nombre: this.innerText };
            document.getElementById('inputBarbero').value = this.dataset.idBarbero;
            actualizarResumen();
            buscarHuecosDisponibles();
        });
    });

    function actualizarInputsServicios() {
        const contenedor = document.getElementById('serviciosSeleccionadosInputs');
        contenedor.innerHTML = '';
        serviciosSeleccionados.forEach(s => {
            contenedor.innerHTML += `<input type="hidden" name="servicios[]" value="${s.id}">`;
        });
    }

    function actualizarResumen() {
        let texto = "";
        if (clienteSeleccionado) texto += `<b>${clienteSeleccionado.nombre}</b> | `;
        
        if (serviciosSeleccionados.length > 0) {
            const totalMin = serviciosSeleccionados.reduce((acc, s) => acc + s.duracion, 0);
            const totalPrecio = serviciosSeleccionados.reduce((acc, s) => acc + s.precio, 0); // Sumamos precios
            const nombres = serviciosSeleccionados.map(s => s.nombre).join(' + ');
            
            // Añadimos el precio al texto del resumen
            texto += `${nombres} (${totalMin} min) - <span style="color: var(--dorado-artdeco); font-weight: bold;">${totalPrecio.toFixed(2)}€</span> | `;
        }

        if (barberoSeleccionado) texto += `Barbero: ${barberoSeleccionado.nombre}`;
        
        resumenTexto.innerHTML = texto || "Selecciona los datos de la cita...";
    }

    // 4. EL BUSCADOR DE HUECOS (AJAX)
    async function buscarHuecosDisponibles() {
        const fecha = document.getElementById('fechaCita').value;
        const duracionTotal = serviciosSeleccionados.reduce((acc, s) => acc + s.duracion, 0);

        if (!barberoSeleccionado || duracionTotal === 0 || !fecha) return;

        listaHoras.innerHTML = '<p style="text-align:center; padding:20px;">Buscando huecos...</p>';

        try {
            const response = await fetch(`index.php?accion=api_huecos_disponibles&id_barbero=${barberoSeleccionado.id}&fecha=${fecha}&duracion=${duracionTotal}`);
            const horas = await response.json();

            listaHoras.innerHTML = '';
            if (horas.length === 0) {
                listaHoras.innerHTML = '<p style="text-align:center; padding:20px; color: #ff6b6b;">No hay huecos seguidos para esa duración.</p>';
                return;
            }

            horas.forEach(h => {
                const div = document.createElement('div');
                div.className = 'item-seleccionable';
                div.innerText = h;
                div.onclick = function() {
                    document.querySelectorAll('#listaHoras .item-seleccionable').forEach(i => i.classList.remove('activo'));
                    this.classList.add('activo');
                    document.getElementById('inputHora').value = h;
                };
                listaHoras.appendChild(div);
            });
        } catch (error) {
            console.error("Error buscando huecos:", error);
        }
    }

    // Escuchar cambio de fecha
    document.getElementById('fechaCita').addEventListener('change', buscarHuecosDisponibles);
});

// Dentro del DOMContentLoaded que ya tienes:
const btnAgendar = document.querySelector('.btn-agendar'); // O el ID que tenga tu botón verde

btnAgendar.addEventListener('click', async () => {
    event.preventDefault();
    // Recogemos los datos de los inputs ocultos que el JS ya ha ido rellenando
    const idCliente = document.getElementById('inputCliente').value;
    const idBarbero = document.getElementById('inputBarbero').value;
    const fecha = document.getElementById('fechaCita').value;
    const hora = document.getElementById('inputHora').value;
    
    // Obtenemos los IDs de servicios de los checkboxes o inputs ocultos
    const servicios = Array.from(document.querySelectorAll('input[name="servicios[]"]')).map(i => i.value);

    if (!idCliente || !idBarbero || !hora || servicios.length === 0) {
        alert("Por favor, completa todos los pasos: Cliente, Servicio, Barbero y Hora.");
        return;
    }

    // Enviamos por POST
    const formData = new FormData();
    formData.append('id_cliente', idCliente);
    formData.append('id_usuario', idBarbero); // ID del barbero
    formData.append('fecha_cita', `${fecha} ${hora}`);
    servicios.forEach(id => formData.append('servicios[]', id));

    try {
        const response = await fetch('index.php?accion=agendar_cita', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        if (resultado.success) {
            alert("¡Cita agendada con éxito!");
            window.location.href = 'index.php?accion=admin'; // Volvemos al panel principal
        } else {
            alert("Error: " + resultado.error);
        }
    } catch (e) {
        console.error(e);
        alert("Hubo un problema al conectar con el servidor.");
    }
});
    </script>
</body>
</html>