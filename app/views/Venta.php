<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - TPV</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body class="body-tpv">

<div class="tpv-contenedor">
    <div class="tpv-cabecera">
        <a href="index.php?accion=admin" class="btn-volver-tpv">&#9664; VOLVER</a>
        <span>VENTA</span>
        <div class="espaciador-tpv"></div> 
    </div>

    <div class="tpv-cuerpo">
        
        <div class="panel-tpv panel-izquierdo">
            <h2 class="titulo-panel-tpv">CATÁLOGO</h2>
            
            <input type="text" id="buscadorCatalogo" class="barra-busqueda-tpv" placeholder="Buscar productos o servicios...">
            
            <div class="contenedor-pestanas-tpv">
                <button id="tabServicios" class="btn-pestana-tpv activo">SERVICIOS</button>
                <button id="tabProductos" class="btn-pestana-tpv">PRODUCTOS</button>
            </div>

            <div class="cuadricula-catalogo" id="gridCatalogo">
                <?php foreach($datos['servicios'] as $servicioActual): ?>
                    <button class="btn-item-catalogo" data-categoria="servicio" onclick="agregarElementoCarrito(<?= $servicioActual['id'] ?>, '<?= addslashes($servicioActual['nombre']) ?>', <?= $servicioActual['precio'] ?>)">
                        <div class="nombre-item-catalogo"><?= htmlspecialchars($servicioActual['nombre']) ?></div>
                        <div class="precio-item-catalogo"><?= number_format($servicioActual['precio'], 2) ?>€</div>
                    </button>
                <?php endforeach; ?>
                
                <?php if (isset($datos['productos'])): ?>
                    <?php foreach($datos['productos'] as $productoActual): ?>
                        <button class="btn-item-catalogo" data-categoria="producto" style="display: none;" onclick="agregarElementoCarrito('prod_<?= $productoActual['id'] ?>', '<?= addslashes($productoActual['nombre']) ?>', <?= $productoActual['precio'] ?>)">
                            <div class="nombre-item-catalogo"><?= htmlspecialchars($productoActual['nombre']) ?></div>
                            <div class="precio-item-catalogo"><?= number_format($productoActual['precio'], 2) ?>€</div>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel-tpv panel-derecho">
            <h2 class="titulo-panel-tpv">RESUMEN DE VENTA</h2>
            
            <form id="formularioProcesarVenta" action="index.php?accion=procesar_cobro" method="POST" class="formulario-tpv">
                
                <div class="contenedor-selector-cliente">
                    <span class="etiqueta-cliente-tpv">Cliente:</span>
                    <select name="id_cita" class="selector-cita-tpv" onchange="cargarDatosCita(this)">
                        <option value="">-- Seleccionar Cita Pendiente --</option>
                        <?php foreach($datos['citas_pendientes'] as $citaPendiente): ?>
                            <option value="<?= $citaPendiente['id'] ?>" 
                                    data-servicios='<?= htmlspecialchars(json_encode([
                                        "nombre" => $citaPendiente['servicios_nombres'], 
                                        "precio" => $citaPendiente['precio_total']
                                    ])) ?>'>
                                <?= htmlspecialchars($citaPendiente['cliente_nombre'] . ' ' . $citaPendiente['cliente_apellido']) ?> 
                                (<?= date('H:i', strtotime($citaPendiente['fecha_cita'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lista-carrito-tpv" id="listaCarritoHtml"></div>

                <div class="totales-carrito-tpv">
                    <div class="fila-total-tpv">
                        <span>Subtotal:</span>
                        <span id="textoMontoSubtotal">0.00€</span>
                    </div>
                    <div class="fila-total-tpv">
                        <span>IVA (21%):</span>
                        <span id="textoMontoIva">0.00€</span>
                    </div>
                    <div class="fila-total-final-tpv">
                        <span>TOTAL A PAGAR:</span>
                        <span id="textoMontoTotal">0.00€</span>
                    </div>
                </div>

                <input type="hidden" name="metodo_pago" id="inputMetodoPagoOculto" value="Efectivo">

                <div class="metodos-pago-tpv">
                    <button type="button" class="btn-metodo-pago seleccionado" onclick="seleccionarMetodoPago(this, 'Efectivo')">EFECTIVO</button>
                    <button type="button" class="btn-metodo-pago" onclick="seleccionarMetodoPago(this, 'Tarjeta')">TARJETA</button>
                    <button type="button" class="btn-metodo-pago" onclick="seleccionarMetodoPago(this, 'Otros')">OTROS</button>
                </div>

                <div class="botones-accion-tpv">
                    <button type="button" class="btn-accion-tpv btn-cobrar-tpv" onclick="procesarCobroFinal()">COBRAR</button>
                    <button type="button" class="btn-accion-tpv btn-cancelar-tpv" onclick="limpiarCarritoVenta()">CANCELAR</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    let listaCarrito = [];
    
    const buscadorCatalogo = document.getElementById('buscadorCatalogo');
    const pestanaServicios = document.getElementById('tabServicios');
    const pestanaProductos = document.getElementById('tabProductos');
    let categoriaActivaSeleccionada = 'servicio';

    function aplicarFiltrosCatalogo() {
        const textoBusqueda = buscadorCatalogo.value.toLowerCase();
        const listaItemsCatalogo = document.querySelectorAll('.btn-item-catalogo');

        listaItemsCatalogo.forEach(itemActual => {
            const nombreItemElemento = itemActual.querySelector('.nombre-item-catalogo').innerText.toLowerCase();
            const categoriaElemento = itemActual.getAttribute('data-categoria');

            const coincideCategoriaActiva = (categoriaElemento === categoriaActivaSeleccionada);
            const coincideTextoBuscado = nombreItemElemento.includes(textoBusqueda);

            if (coincideCategoriaActiva && coincideTextoBuscado) {
                itemActual.style.display = 'flex';
            } else {
                itemActual.style.display = 'none';
            }
        });
    }

    pestanaServicios.addEventListener('click', function() {
        pestanaServicios.classList.add('activo');
        pestanaProductos.classList.remove('activo');
        categoriaActivaSeleccionada = 'servicio';
        aplicarFiltrosCatalogo();
    });

    pestanaProductos.addEventListener('click', function() {
        pestanaProductos.classList.add('activo');
        pestanaServicios.classList.remove('activo');
        categoriaActivaSeleccionada = 'producto';
        aplicarFiltrosCatalogo();
    });

    buscadorCatalogo.addEventListener('input', aplicarFiltrosCatalogo);
    
    function cargarDatosCita(elementoSelector) {
        listaCarrito = [];
        actualizarVistaCarrito();

        if (elementoSelector.value === "") return;

        const opcionSeleccionada = elementoSelector.options[elementoSelector.selectedIndex];
        const datosServiciosCita = JSON.parse(opcionSeleccionada.getAttribute('data-servicios'));
        
        if (datosServiciosCita.precio > 0) {
            agregarElementoCarrito('cita_' + elementoSelector.value, 'Servicios: ' + datosServiciosCita.nombre, parseFloat(datosServiciosCita.precio));
        }
    }

    function limpiarCarritoVenta() {
        listaCarrito = [];
        document.querySelector('.selector-cita-tpv').value = "";
        actualizarVistaCarrito();
    }

    function agregarElementoCarrito(identificador, nombreElemento, precioElemento) {
        listaCarrito.push({ id: identificador, nombre: nombreElemento, precio: precioElemento });
        actualizarVistaCarrito();
    }

    function eliminarElementoCarrito(indiceElemento) {
        listaCarrito.splice(indiceElemento, 1);
        actualizarVistaCarrito();
    }

    function actualizarVistaCarrito() {
        const contenedorListaCarrito = document.getElementById('listaCarritoHtml');
        contenedorListaCarrito.innerHTML = '';
        let sumaSubtotal = 0;

        listaCarrito.forEach((itemCarrito, indiceActual) => {
            sumaSubtotal += itemCarrito.precio;
            contenedorListaCarrito.innerHTML += `
                <div class="item-carrito-fila">
                    <span>1x ${itemCarrito.nombre} - ${itemCarrito.precio.toFixed(2)}€</span>
                    <button type="button" class="btn-eliminar-item-carrito" onclick="eliminarElementoCarrito(${indiceActual})">&#128465;</button>
                </div>
            `;
        });

        const calculoIva = sumaSubtotal - (sumaSubtotal / 1.21); 
        const calculoBase = sumaSubtotal - calculoIva;

        document.getElementById('textoMontoSubtotal').innerText = calculoBase.toFixed(2) + '€';
        document.getElementById('textoMontoIva').innerText = calculoIva.toFixed(2) + '€';
        document.getElementById('textoMontoTotal').innerText = sumaSubtotal.toFixed(2) + '€';
    }

    function seleccionarMetodoPago(botonMetodo, nombreMetodo) {
        document.querySelectorAll('.btn-metodo-pago').forEach(botonActual => botonActual.classList.remove('seleccionado'));
        botonMetodo.classList.add('seleccionado');
        document.getElementById('inputMetodoPagoOculto').value = nombreMetodo;
    }

    function procesarCobroFinal() {
        if (listaCarrito.length === 0) return;
        
        const valorCitaSeleccionada = document.querySelector('.selector-cita-tpv').value;
        if (valorCitaSeleccionada === "") return;

        if (confirm("¿Confirmar cobro de " + document.getElementById('textoMontoTotal').innerText + "?")) {
            document.getElementById('formularioProcesarVenta').submit();
        }
    }
</script>

</body>
</html>