<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Gestión de Clientes</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body class="body-gestion-clientes">

<div class="contenedor-principal-clientes">
    <div class="cabecera-gestion-clientes">
        <h1 class="titulo-gestion-clientes">GESTIÓN DE CLIENTES</h1>
        <a href="index.php?accion=admin" class="btn-accion btn-volver-panel">VOLVER AL PANEL</a>
    </div>
    
    <input type="text" id="inputBuscador" class="barra-busqueda" placeholder="Buscar por nombre, apellido o teléfono..." autofocus>

    <div class="cabecera-grid-clientes">
        <span>CLIENTE</span>
        <span>TELÉFONO</span>
        <span>ÚLTIMA VISITA</span>
        <span>TOTAL GASTADO</span>
        <span class="alineacion-centro">ESTADO</span>
        <span class="alineacion-derecha">ACCIONES</span>
    </div>

    <div id="contenedorLista" class="lista-clientes-scroll">
    </div>
</div>

<div id="modalHistorial" class="modal-oculto">
    <div class="modal-contenido modal-historial-contenido">
        <span class="cerrar-modal" onclick="cerrarHistorial()">&times;</span>
        <h2 class="titulo-modal-historial">HISTORIAL DE CITAS</h2>
        <p id="nombreClienteHistorial" class="subtitulo-modal-historial"></p>
        
        <div id="listaHistorial" class="lista-historial-scroll">
        </div>
    </div>
</div>

<script>
    const entradaBuscador = document.getElementById('inputBuscador');
    const contenedorResultados = document.getElementById('contenedorLista');

    async function buscar(paginaRequerida = 1) {
        const terminoBusqueda = entradaBuscador.value;
        const respuestaServidor = await fetch(`index.php?accion=api_buscar_clientes&s=${terminoBusqueda}&p=${paginaRequerida}`);
        contenedorResultados.innerHTML = await respuestaServidor.text();
    }

    entradaBuscador.addEventListener('input', () => buscar(1));

    buscar(1);

    async function abrirHistorial(idCliente, nombreCompleto) {
        const elementoNombre = document.getElementById('nombreClienteHistorial');
        const elementoLista = document.getElementById('listaHistorial');
        const elementoModal = document.getElementById('modalHistorial');

        elementoNombre.innerText = nombreCompleto;
        elementoLista.innerHTML = '<div class="mensaje-carga-historial">Cargando historial...</div>';
        
        elementoModal.classList.remove('modal-oculto');
        elementoModal.classList.add('modal-activo');

        const respuestaHistorial = await fetch(`index.php?accion=api_historial_cliente&id=${idCliente}`);
        elementoLista.innerHTML = await respuestaHistorial.text();
    }

    function cerrarHistorial() {
        const elementoModal = document.getElementById('modalHistorial');
        elementoModal.classList.remove('modal-activo');
        elementoModal.classList.add('modal-oculto');
    }
</script>

</body>
</html>