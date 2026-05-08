<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - TPV</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        :root {
            --bg-tpv: #091e17; /* Verde oscuro elegante */
            --bg-panel: #0d261e;
            --dorado: #c5a059;
            --dorado-oscuro: #8b733d;
            --texto-claro: #e0e0e0;
        }

        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            font-family: serif;
        }

        /* Contenedor Principal */
        .tpv-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background-color: var(--bg-tpv);
            padding: 10px 20px 20px 20px;
            box-sizing: border-box;
        }

        .tpv-header {
            text-align: center;
            color: var(--dorado);
            font-size: 1.5rem;
            letter-spacing: 2px;
            padding: 10px 0;
            border-bottom: 1px solid var(--dorado-oscuro);
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tpv-body {
            display: flex;
            gap: 20px;
            flex-grow: 1;
            overflow: hidden;
        }

        /* Paneles con borde doble estilo vintage */
        .panel {
            background-color: var(--bg-panel);
            border: 2px solid var(--dorado);
            border-radius: 6px;
            position: relative;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .panel::before {
            content: '';
            position: absolute;
            top: 4px; left: 4px; right: 4px; bottom: 4px;
            border: 1px solid var(--dorado-oscuro);
            border-radius: 4px;
            pointer-events: none;
        }

        .panel-left { flex: 6; }
        .panel-right { flex: 4; }

        .panel-title {
            text-align: center;
            color: var(--dorado);
            font-size: 1.3rem;
            margin-top: 0;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        /* CATÁLOGO (Izquierda) */
        .search-bar {
            width: 100%;
            background: transparent;
            border: 1px solid var(--dorado-oscuro);
            color: var(--texto-claro);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            outline: none;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .tab-btn {
            flex: 1;
            background: transparent;
            border: 1px solid var(--dorado);
            color: var(--dorado);
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            text-transform: uppercase;
        }
        .tab-btn.active {
            background: rgba(197, 160, 89, 0.1);
        }

        .grid-catalogo {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            overflow-y: auto;
            flex-grow: 1;
            padding-right: 5px;
        }
        .item-btn {
            background: transparent;
            border: 1px solid var(--dorado-oscuro);
            border-radius: 6px;
            padding: 15px 5px;
            color: var(--texto-claro);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: 0.2s;
            min-height: 100px;
        }
        .item-btn:hover { background: rgba(197, 160, 89, 0.15); border-color: var(--dorado); }
        .item-icon { font-size: 2rem; margin-bottom: 10px; color: var(--dorado); }
        .item-name { font-size: 0.9rem; margin-bottom: 5px; }
        .item-price { font-weight: bold; color: var(--dorado); }

        /* TICKET (Derecha) */
        .cliente-select {
            width: 100%;
            background: #fff;
            border: 1px solid var(--dorado);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .cart-list {
            flex-grow: 1;
            overflow-y: auto;
            color: var(--texto-claro);
            margin-bottom: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed var(--dorado-oscuro);
        }
        .cart-item-delete {
            background: var(--dorado);
            color: #000;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            font-weight: bold;
        }

        .cart-totals {
            border-top: 1px solid var(--dorado);
            padding-top: 15px;
            color: var(--texto-claro);
        }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total-final {
            display: flex;
            justify-content: space-between;
            font-size: 1.8rem;
            color: var(--dorado);
            font-weight: bold;
            margin: 15px 0;
        }

        .payment-methods {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .btn-pay-method {
            flex: 1;
            background: transparent;
            border: 1px solid var(--dorado);
            color: var(--dorado);
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-pay-method.selected { background: var(--dorado); color: var(--bg-panel); font-weight: bold; }

        .action-buttons { display: flex; gap: 10px; }
        .btn-action {
            flex: 1;
            padding: 15px;
            border: 1px solid var(--dorado);
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
        }
        .btn-cobrar { background: var(--dorado); color: var(--bg-panel); flex: 2; }
        .btn-cancelar { background: transparent; color: var(--dorado); }

    </style>
</head>
<body>

<div class="tpv-container">
    <div class="tpv-header">
        <a href="index.php?accion=admin" style="color: var(--dorado); text-decoration: none; font-size: 1rem;">&#9664; VOLVER</a>
        <span>VENTA</span>
        <div style="width: 80px;"></div> </div>

    <div class="tpv-body">
        
        <div class="panel panel-left">
            <h2 class="panel-title">CATÁLOGO</h2>
            
            <input type="text" id="buscadorCatalogo" class="search-bar" placeholder="Buscar productos o servicios...">
            
            <div class="tabs">
                <button id="tabServicios" class="tab-btn active">SERVICIOS</button>
                <button id="tabProductos" class="tab-btn">PRODUCTOS</button>
            </div>

            <div class="grid-catalogo" id="gridCatalogo">
                <?php foreach($datos['servicios'] as $srv): ?>
                    <button class="item-btn" data-categoria="servicio" onclick="agregarAlCarrito(<?php echo $srv['id']; ?>, '<?php echo addslashes($srv['nombre']); ?>', <?php echo $srv['precio']; ?>)">
                        <div class="item-name"><?php echo htmlspecialchars($srv['nombre']); ?></div>
                        <div class="item-price"><?php echo number_format($srv['precio'], 2); ?>€</div>
                    </button>
                <?php endforeach; ?>
                
                <button class="item-btn" data-categoria="producto" style="display: none;" onclick="agregarAlCarrito('p1', 'Pomada Fijadora', 18.00)">
                    <div class="item-name">Pomada Fijadora</div>
                    <div class="item-price">18.00€</div>
                </button>
            </div>
        </div>

        <div class="panel panel-right">
            <h2 class="panel-title">RESUMEN DE VENTA</h2>
            
            <form id="formVenta" action="index.php?accion=procesar_cobro" method="POST" style="display: flex; flex-direction: column; height: 100%;">
                
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span style="color: var(--dorado);">Cliente:</span>
                    <select name="id_cita" class="cliente-select" style="margin-bottom: 0;" onchange="cargarCita(this)">
                        <option value="">-- Seleccionar Cita Pendiente --</option>
                        <?php foreach($datos['citas_pendientes'] as $cita): ?>
                            <option value="<?php echo $cita['id']; ?>" 
                                    data-servicios='<?php echo htmlspecialchars(json_encode([
                                        "nombre" => $cita['servicios_nombres'], 
                                        "precio" => $cita['precio_total']
                                    ])); ?>'>
                                <?php echo htmlspecialchars($cita['cliente_nombre'] . ' ' . $cita['cliente_apellido']); ?> 
                                (<?php echo date('H:i', strtotime($cita['fecha_cita'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="cart-list" id="listaCarrito">
                    </div>

                <div class="cart-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="txtSubtotal">0.00€</span>
                    </div>
                    <div class="total-row">
                        <span>IVA (21%):</span>
                        <span id="txtIva">0.00€</span>
                    </div>
                    <div class="total-final">
                        <span>TOTAL A PAGAR:</span>
                        <span id="txtTotal">0.00€</span>
                    </div>
                </div>

                <input type="hidden" name="metodo_pago" id="inputMetodoPago" value="Efectivo">

                <div class="payment-methods">
                    <button type="button" class="btn-pay-method selected" onclick="seleccionarMetodo(this, 'Efectivo')">EFECTIVO</button>
                    <button type="button" class="btn-pay-method" onclick="seleccionarMetodo(this, 'Tarjeta')">TARJETA</button>
                    <button type="button" class="btn-pay-method" onclick="seleccionarMetodo(this, 'Otros')">OTROS</button>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-action btn-cobrar" onclick="procesarCobro()">COBRAR</button>
                    <button type="button" class="btn-action btn-cancelar" onclick="limpiarCarrito()">CANCELAR</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    let carrito = [];

    // --- LÓGICA DE BÚSQUEDA Y PESTAÑAS ---
    const buscador = document.getElementById('buscadorCatalogo');
    const tabServicios = document.getElementById('tabServicios');
    const tabProductos = document.getElementById('tabProductos');
    let categoriaActiva = 'servicio'; // Empezamos mostrando servicios

    function aplicarFiltros() {
        const textoBusqueda = buscador.value.toLowerCase();
        const items = document.querySelectorAll('.item-btn');

        items.forEach(item => {
            const nombreItem = item.querySelector('.item-name').innerText.toLowerCase();
            const categoriaItem = item.getAttribute('data-categoria');

            // Comprobamos si coincide la pestaña y si el texto escrito está en el nombre
            const coincideCategoria = (categoriaItem === categoriaActiva);
            const coincideTexto = nombreItem.includes(textoBusqueda);

            if (coincideCategoria && coincideTexto) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Eventos al hacer clic en las pestañas
    tabServicios.addEventListener('click', function() {
        tabServicios.classList.add('active');
        tabProductos.classList.remove('active');
        categoriaActiva = 'servicio';
        aplicarFiltros();
    });

    tabProductos.addEventListener('click', function() {
        tabProductos.classList.add('active');
        tabServicios.classList.remove('active');
        categoriaActiva = 'producto';
        aplicarFiltros();
    });

    // Evento al escribir en el buscador
    buscador.addEventListener('input', aplicarFiltros);
    
    // Cuando se selecciona una cita del desplegable
    function cargarCita(selectObj) {
        // Vaciamos el carrito pero sin tocar el desplegable
        carrito = [];
        renderizarCarrito();

        if (selectObj.value === "") return;

        const option = selectObj.options[selectObj.selectedIndex];
        const data = JSON.parse(option.getAttribute('data-servicios'));
        
        // Añadimos el bloque de la cita al carrito
        if (data.precio > 0) {
            agregarAlCarrito('cita_' + selectObj.value, 'Servicios: ' + data.nombre, parseFloat(data.precio));
        }
    }

    // Botón Cancelar (Limpia todo, incluido el desplegable)
    function limpiarCarrito() {
        carrito = [];
        document.querySelector('.cliente-select').value = "";
        renderizarCarrito();
    }
    function agregarAlCarrito(id, nombre, precio) {
        carrito.push({ id, nombre, precio });
        renderizarCarrito();
    }

    function eliminarDelCarrito(index) {
        carrito.splice(index, 1);
        renderizarCarrito();
    }

    function renderizarCarrito() {
        const lista = document.getElementById('listaCarrito');
        lista.innerHTML = '';
        let subtotal = 0;

        carrito.forEach((item, index) => {
            subtotal += item.precio;
            lista.innerHTML += `
                <div class="cart-item">
                    <span>1x ${item.nombre} - ${item.precio.toFixed(2)}€</span>
                    <button type="button" class="cart-item-delete" onclick="eliminarDelCarrito(${index})">🗑️</button>
                </div>
            `;
        });

        // Cálculos (Asumiendo que el precio ya lleva IVA, desglosamos)
        // Si quieres que el IVA se sume aparte, cambia la lógica aquí.
        const iva = subtotal - (subtotal / 1.21); 
        const base = subtotal - iva;

        document.getElementById('txtSubtotal').innerText = base.toFixed(2) + '€';
        document.getElementById('txtIva').innerText = iva.toFixed(2) + '€';
        document.getElementById('txtTotal').innerText = subtotal.toFixed(2) + '€';
    }

    function seleccionarMetodo(btn, metodo) {
        document.querySelectorAll('.btn-pay-method').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('inputMetodoPago').value = metodo;
    }

    function procesarCobro() {
        if (carrito.length === 0) {
            alert("El carrito está vacío.");
            return;
        }
        
        const citaSeleccionada = document.querySelector('.cliente-select').value;
        if (citaSeleccionada === "") {
            // Si están comprando solo productos sin cita previa, aquí se haría otra lógica.
            // De momento forzamos a que seleccionen a un cliente para cerrar su cita.
            alert("Por favor, selecciona una cita para cobrar.");
            return;
        }

        if (confirm("¿Confirmar cobro de " + document.getElementById('txtTotal').innerText + "?")) {
            document.getElementById('formVenta').submit();
        }
    }
</script>

</body>
</html>