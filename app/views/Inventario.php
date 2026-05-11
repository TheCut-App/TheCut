<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Inventario</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        :root { --verde-bg: #0b1f18; --dorado: #d4af37; --dorado-oc: #8b733d; --texto: #e0e0e0; }
        body { 
            background-color: #000; color: var(--texto); font-family: 'Segoe UI', serif; 
            margin: 0; padding: 40px 20px; box-sizing: border-box; 
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        
        .inv-contenedor {
            width: 100%; max-width: 1200px; background: var(--verde-bg);
            border: 2px solid var(--dorado); border-radius: 8px; position: relative; padding: 30px;
            display: flex; flex-direction: column; box-sizing: border-box;
        }
        .inv-contenedor::before {
            content: ''; position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px solid var(--dorado-oc); border-radius: 4px; pointer-events: none;
        }

        .header-inv { text-align: center; border-bottom: 1px solid var(--dorado-oc); padding-bottom: 20px; margin-bottom: 30px; }
        .header-inv h1 { color: var(--dorado); font-size: 2.2rem; letter-spacing: 4px; margin: 0; text-transform: uppercase; }

        .layout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }

        /* Panel Izquierdo */
        .panel-izq { border: 1px solid var(--dorado-oc); border-radius: 6px; padding: 20px; display: flex; flex-direction: column; }
        .titulo-panel { color: var(--dorado); text-align: center; margin-top: 0; margin-bottom: 20px; font-weight: normal; letter-spacing: 2px; text-transform: uppercase; }
        
        .buscador-inv { width: 100%; background: transparent; border: 1px solid #444; color: white; padding: 12px 15px; border-radius: 4px; margin-bottom: 20px; box-sizing: border-box; font-size: 1rem; }
        .buscador-inv:focus { border-color: var(--dorado); outline: none; }

        .tabs-inv { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { flex: 1; background: transparent; color: var(--dorado); border: 1px solid var(--dorado-oc); padding: 10px; border-radius: 4px; cursor: pointer; text-transform: uppercase; font-weight: bold; transition: 0.2s; }
        .tab-btn.activo { background: var(--dorado); color: black; border-color: var(--dorado); }

        .grid-productos { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; overflow-y: auto; max-height: 400px; padding-right: 5px; }
        .grid-productos::-webkit-scrollbar { width: 6px; }
        .grid-productos::-webkit-scrollbar-thumb { background: var(--dorado); border-radius: 4px; }

        .tarjeta-prod { border: 1px solid var(--dorado-oc); border-radius: 6px; padding: 15px; position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; background: rgba(255,255,255,0.02); transition: 0.2s; }
        .prod-nombre { font-size: 1.1rem; color: white; margin-bottom: 5px; }
        .prod-stock { font-size: 0.9rem; color: #aaa; margin-bottom: 15px; }
        .prod-stock.alerta { color: #ff4d4d; font-weight: bold; }
        
        .btn-reponer { background: var(--dorado); color: black; border: none; padding: 8px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; text-transform: uppercase; width: 80%; }
        .btn-reponer:hover { background: #e6c875; }

        .indicador-estado { position: absolute; top: 10px; right: 10px; width: 12px; height: 12px; border-radius: 50%; }
        .estado-ok { background-color: #28a745; box-shadow: 0 0 5px #28a745; }
        .estado-mal { background-color: #ff4d4d; box-shadow: 0 0 5px #ff4d4d; }

        /* Panel Derecho */
        .panel-der { border: 1px solid var(--dorado-oc); border-radius: 6px; padding: 20px; display: flex; flex-direction: column; }
        .acciones-lista { display: flex; flex-direction: column; gap: 15px; flex-grow: 1; }
        .btn-accion-rap { background: transparent; color: white; border: 1px solid var(--dorado-oc); padding: 15px; border-radius: 4px; cursor: pointer; font-size: 1rem; transition: 0.2s; }
        .btn-accion-rap:hover { background: rgba(212, 175, 55, 0.1); border-color: var(--dorado); }

        .resumen-footer { border-top: 1px solid rgba(139, 115, 61, 0.5); padding-top: 20px; margin-top: auto; }
        .resumen-fila { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1.1rem; }
        .resumen-fila span:last-child { color: var(--dorado); font-weight: bold; }

        .btn-volver { position: absolute; top: 30px; left: 30px; color: var(--dorado); text-decoration: none; font-weight: bold; border: 1px solid var(--dorado); padding: 5px 15px; border-radius: 4px; }

        /* Estilos de Modales */
        .modal-oculto { display: none; }
        .modal-activo { display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(3px); }
        .modal-contenido { background: var(--verde-bg); border: 2px solid var(--dorado); border-radius: 8px; width: 90%; max-width: 450px; padding: 30px; position: relative; }
        .cerrar-modal { position: absolute; top: 15px; right: 20px; color: var(--dorado); font-size: 1.8rem; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .cerrar-modal:hover { color: #fff; }
        
        .form-label { color: var(--dorado); font-size: 0.9rem; font-weight: bold; margin-bottom: 8px; display: block; text-transform: uppercase; }
        .input-modal { 
            width: 100%; 
            background: transparent; 
            border: 1px solid #444; 
            color: white; 
            padding: 12px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
            box-sizing: border-box; 
            font-size: 1rem; 
            color-scheme: dark; /* ESTA ES LA LÍNEA MÁGICA */
        }
        .input-modal:focus { border-color: var(--dorado); outline: none; }
        .btn-modal-guardar { width: 100%; background: var(--dorado); color: black; border: none; padding: 15px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; text-transform: uppercase; }
        select.input-modal {
            background-color: var(--verde-bg) !important;
        }
        
        select.input-modal option {
            background-color: #0b1f18 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<div class="inv-contenedor">
    <a href="index.php?accion=admin" class="btn-volver">&lt; VOLVER</a>
    <div class="header-inv"><h1>INVENTARIO</h1></div>

    <div class="layout-grid">
        <div class="panel-izq">
            <h2 class="titulo-panel">INVENTARIO</h2>
            <input type="text" id="buscadorInv" class="buscador-inv" placeholder="Buscar productos...">
            
            <div class="tabs-inv">
                <button id="btnTabTodos" class="tab-btn activo">TODOS LOS PRODUCTOS</button>
                <button id="btnTabBajo" class="tab-btn">BAJO STOCK</button>
            </div>

            <div class="grid-productos" id="contenedorProductos">
                <?php foreach ($datos['productos'] as $prod): 
                    $bajoStock = $prod['stock'] <= $prod['stock_minimo'];
                ?>
                <div class="tarjeta-prod" data-nombre="<?= strtolower($prod['nombre']) ?>" data-bajo="<?= $bajoStock ? 'si' : 'no' ?>">
                    <a href="javascript:void(0)" onclick="confirmarEliminarProducto(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>')" style="position: absolute; top: 8px; left: 12px; color: #ff4d4d; text-decoration: none; font-size: 1.4rem; font-weight: bold; transition: 0.2s;" onmouseover="this.style.color='#ff1a1a'" onmouseout="this.style.color='#ff4d4d'">&times;</a>
                    
                    <div class="indicador-estado <?= $bajoStock ? 'estado-mal' : 'estado-ok' ?>"></div>
                    <div class="prod-nombre"><?= htmlspecialchars($prod['nombre']) ?></div>
                    <div class="prod-stock <?= $bajoStock ? 'alerta' : '' ?>">Stock: <?= $prod['stock'] ?> u.</div>
                    <button class="btn-reponer" onclick="abrirModalReponer('<?= $prod['id'] ?>', '<?= addslashes($prod['nombre']) ?>', <?= $prod['stock'] ?>)">REPONER</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel-der">
            <h2 class="titulo-panel">ACCIONES RÁPIDAS</h2>
            <div class="acciones-lista">
                <button class="btn-accion-rap" onclick="abrirModalNuevo()">Nuevo Producto</button>
                <button class="btn-accion-rap" onclick="abrirModalAjustar()">Ajustar Stock</button>
            </div>
            <div class="resumen-footer">
                <div class="resumen-fila">
                    <span>Total Productos:</span>
                    <span id="txtTotalProds"><?= $datos['total_productos'] ?></span>
                </div>
                <div class="resumen-fila">
                    <span>Valor Total:</span>
                    <span><?= number_format($datos['valor_total'], 2, ',', '.') ?>€</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalNuevo" class="modal-oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarModales()">&times;</span>
        <h2 style="color: var(--dorado); text-align: center; margin-top: 0; text-transform: uppercase;">NUEVO PRODUCTO</h2>
        
        <form action="index.php?accion=guardar_producto" method="POST">
            <span class="form-label">Nombre del Producto</span>
            <input type="text" name="nombre" class="input-modal" required>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <span class="form-label">Stock Inicial</span>
                    <input type="number" name="stock" class="input-modal" value="0" required>
                </div>
                <div>
                    <span class="form-label">Alarma Mínima</span>
                    <input type="number" name="stock_minimo" class="input-modal" value="5" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <span class="form-label">Precio Coste (€)</span>
                    <input type="number" step="0.01" name="precio_coste" class="input-modal" value="0.00" required>
                </div>
                <div>
                    <span class="form-label">Precio Venta (€)</span>
                    <input type="number" step="0.01" name="precio" class="input-modal" required>
                </div>
            </div>

            <button type="submit" class="btn-modal-guardar">GUARDAR PRODUCTO</button>
        </form>
    </div>
</div>

<div id="modalAjustar" class="modal-oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarModales()">&times;</span>
        <h2 style="color: var(--dorado); text-align: center; margin-top: 0; text-transform: uppercase;">AJUSTAR STOCK</h2>
        
        <form action="index.php?accion=ajustar_stock" method="POST">
            <span class="form-label">Seleccionar Producto</span>
            <select name="id_producto" id="selectAjustarStock" class="input-modal" required>
                <option value="" data-stock="">-- Selecciona --</option>
                <?php foreach ($datos['productos'] as $prod): ?>
                    <option value="<?= $prod['id'] ?>" data-stock="<?= $prod['stock'] ?>">
                        <?= htmlspecialchars($prod['nombre']) ?> (Actual: <?= $prod['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <span class="form-label">Nuevo Stock Exacto</span>
            <input type="number" name="nuevo_stock" id="inputNuevoStock" class="input-modal" required>

            <button type="submit" class="btn-modal-guardar">ACTUALIZAR</button>
        </form>
    </div>
</div>

<div id="modalReponer" class="modal-oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarModales()">&times;</span>
        <h2 style="color: var(--dorado); text-align: center; margin-top: 0; text-transform: uppercase;">ENTRADA MERCANCÍA</h2>
        <p id="txtProdReponer" style="text-align: center; color: #fff; font-size: 1.1rem; margin-bottom: 20px;"></p>
        
        <form action="index.php?accion=sumar_stock" method="POST">
            <input type="hidden" name="id_producto" id="inputIdReponer">
            
            <span class="form-label">Unidades a Sumar</span>
            <input type="number" name="cantidad_sumar" class="input-modal" min="1" value="1" required>

            <button type="submit" class="btn-modal-guardar">AÑADIR AL INVENTARIO</button>
        </form>
    </div>
</div>

<script>
    // --- LÓGICA DE PESTAÑAS Y BUSCADOR ---
    const buscador = document.getElementById('buscadorInv');
    const tarjetas = document.querySelectorAll('.tarjeta-prod');
    const btnTodos = document.getElementById('btnTabTodos');
    const btnBajo = document.getElementById('btnTabBajo');
    let filtroBajoStock = false;

    function filtrarInventario() {
        const query = buscador.value.toLowerCase();
        let visibles = 0;

        tarjetas.forEach(t => {
            const nombre = t.dataset.nombre;
            const esBajo = t.dataset.bajo === 'si';
            
            const coincideTexto = nombre.includes(query);
            const coincidePestana = !filtroBajoStock || esBajo;

            if (coincideTexto && coincidePestana) {
                t.style.display = 'flex';
                visibles++;
            } else {
                t.style.display = 'none';
            }
        });

        // Actualizamos el contador visual rápidamente
        document.getElementById('txtTotalProds').innerText = visibles;
    }

    buscador.addEventListener('input', filtrarInventario);

    btnTodos.addEventListener('click', () => {
        filtroBajoStock = false;
        btnTodos.classList.add('activo');
        btnBajo.classList.remove('activo');
        filtrarInventario();
    });

    btnBajo.addEventListener('click', () => {
        filtroBajoStock = true;
        btnBajo.classList.add('activo');
        btnTodos.classList.remove('activo');
        filtrarInventario();
    });

    // --- LÓGICA DE MODALES ---
    const modalNuevo = document.getElementById('modalNuevo');
    const modalAjustar = document.getElementById('modalAjustar');
    const modalReponer = document.getElementById('modalReponer');

    function cerrarModales() {
        modalNuevo.classList.replace('modal-activo', 'modal-oculto');
        modalAjustar.classList.replace('modal-activo', 'modal-oculto');
        modalReponer.classList.replace('modal-activo', 'modal-oculto');
    }

    function abrirModalNuevo() {
        cerrarModales();
        modalNuevo.classList.replace('modal-oculto', 'modal-activo');
    }

    function abrirModalAjustar() {
        cerrarModales();
        modalAjustar.classList.replace('modal-oculto', 'modal-activo');
    }

    function abrirModalReponer(id, nombre, stockActual) {
        cerrarModales();
        document.getElementById('inputIdReponer').value = id;
        document.getElementById('txtProdReponer').innerHTML = `<strong>${nombre}</strong><br><span style="color:#aaa; font-size:0.9rem;">Stock actual: ${stockActual}</span>`;
        modalReponer.classList.replace('modal-oculto', 'modal-activo');
    }

    // Autorrellenar el stock actual al seleccionar producto en Ajustar Stock
    const selectAjustar = document.getElementById('selectAjustarStock');
    const inputNuevoStock = document.getElementById('inputNuevoStock');

    if (selectAjustar && inputNuevoStock) {
        selectAjustar.addEventListener('change', function() {
            const opcionSeleccionada = this.options[this.selectedIndex];
            const stockActual = opcionSeleccionada.getAttribute('data-stock');

            // Si hay un stock válido, lo pone en el input. Si vuelve a "-- Selecciona --", lo vacía.
            if (stockActual !== null && stockActual !== "") {
                inputNuevoStock.value = stockActual;
            } else {
                inputNuevoStock.value = "";
            }
        });
    }

    // Confirmación para eliminar producto
    function confirmarEliminarProducto(id, nombre) {
        if (confirm(`¿Estás seguro de que deseas ELIMINAR el producto "${nombre}"?\nEsta acción lo quitará permanentemente del inventario.`)) {
            window.location.href = `index.php?accion=eliminar_producto&id=${id}`;
        }
    }
</script>

</body>
</html>