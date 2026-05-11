<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Inventario</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body class="body-inventario">

<div class="inv-contenedor">
    <a href="index.php?accion=admin" class="btn-volver-inv">&lt; VOLVER</a>
    <div class="header-inv">
        <h1>INVENTARIO</h1>
    </div>

    <div class="layout-grid-inv">
        <div class="panel-izq">
            <h2 class="titulo-panel-inv">INVENTARIO</h2>
            <input type="text" id="entradaBuscadorInventario" class="buscador-inv" placeholder="Buscar productos...">
            
            <div class="tabs-inv">
                <button id="botonPestanaTodos" class="tab-btn activo">TODOS LOS PRODUCTOS</button>
                <button id="botonPestanaBajo" class="tab-btn">BAJO STOCK</button>
            </div>

            <div class="grid-productos-inv" id="contenedorProductos">
                <?php foreach ($datos['productos'] as $productoActual): 
                    $tieneBajoStock = $productoActual['stock'] <= $productoActual['stock_minimo'];
                ?>
                <div class="tarjeta-prod" data-nombre="<?= strtolower($productoActual['nombre']) ?>" data-bajo="<?= $tieneBajoStock ? 'si' : 'no' ?>">
                    <a href="javascript:void(0)" onclick="confirmarEliminarProducto(<?= $productoActual['id'] ?>, '<?= addslashes($productoActual['nombre']) ?>')" class="btn-eliminar-producto">&times;</a>
                    
                    <div class="indicador-estado <?= $tieneBajoStock ? 'estado-mal' : 'estado-ok' ?>"></div>
                    <div class="prod-nombre"><?= htmlspecialchars($productoActual['nombre']) ?></div>
                    <div class="prod-stock <?= $tieneBajoStock ? 'alerta' : '' ?>">Stock: <?= $productoActual['stock'] ?> u.</div>
                    <button class="btn-reponer" onclick="abrirModalReponerStock('<?= $productoActual['id'] ?>', '<?= addslashes($productoActual['nombre']) ?>', <?= $productoActual['stock'] ?>)">REPONER</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel-der">
            <h2 class="titulo-panel-inv">ACCIONES RÁPIDAS</h2>
            <div class="acciones-lista">
                <button class="btn-accion-rap" onclick="abrirModalNuevoProducto()">Nuevo Producto</button>
                <button class="btn-accion-rap" onclick="abrirModalAjustarStock()">Ajustar Stock</button>
            </div>
            <div class="resumen-footer">
                <div class="resumen-fila">
                    <span>Total Productos:</span>
                    <span id="textoTotalProductos"><?= $datos['total_productos'] ?></span>
                </div>
                <div class="resumen-fila">
                    <span>Valor Total:</span>
                    <span class="texto-valor-total"><?= number_format($datos['valor_total'], 2, ',', '.') ?>€</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalNuevoProducto" class="modal-oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarTodosLosModales()">&times;</span>
        <h2 class="titulo-modal-inventario">NUEVO PRODUCTO</h2>
        
        <form action="index.php?accion=guardar_producto" method="POST">
            <span class="form-label-inv">Nombre del Producto</span>
            <input type="text" name="nombre" class="input-modal-inv" required>

            <div class="cuadricula-campos-modal">
                <div>
                    <span class="form-label-inv">Stock Inicial</span>
                    <input type="number" name="stock" class="input-modal-inv" value="0" required>
                </div>
                <div>
                    <span class="form-label-inv">Alarma Mínima</span>
                    <input type="number" name="stock_minimo" class="input-modal-inv" value="5" required>
                </div>
            </div>

            <div class="cuadricula-campos-modal">
                <div>
                    <span class="form-label-inv">Precio Coste (€)</span>
                    <input type="number" step="0.01" name="precio_coste" class="input-modal-inv" value="0.00" required>
                </div>
                <div>
                    <span class="form-label-inv">Precio Venta (€)</span>
                    <input type="number" step="0.01" name="precio" class="input-modal-inv" required>
                </div>
            </div>

            <button type="submit" class="btn-modal-guardar-inv">GUARDAR PRODUCTO</button>
        </form>
    </div>
</div>

<div id="modalAjustarStock" class="modal-oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarTodosLosModales()">&times;</span>
        <h2 class="titulo-modal-inventario">AJUSTAR STOCK</h2>
        
        <form action="index.php?accion=ajustar_stock" method="POST">
            <span class="form-label-inv">Seleccionar Producto</span>
            <select name="id_producto" id="selectorAjustarStock" class="input-modal-inv select-inventario" required>
                <option value="" data-stock="">-- Selecciona --</option>
                <?php foreach ($datos['productos'] as $productoActual): ?>
                    <option value="<?= $productoActual['id'] ?>" data-stock="<?= $productoActual['stock'] ?>">
                        <?= htmlspecialchars($productoActual['nombre']) ?> (Actual: <?= $productoActual['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <span class="form-label-inv">Nuevo Stock Exacto</span>
            <input type="number" name="nuevo_stock" id="entradaNuevoStock" class="input-modal-inv" required>

            <button type="submit" class="btn-modal-guardar-inv">ACTUALIZAR</button>
        </form>
    </div>
</div>

<div id="modalReponerStock" class="modal-oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarTodosLosModales()">&times;</span>
        <h2 class="titulo-modal-inventario">ENTRADA MERCANCÍA</h2>
        <p id="textoProductoReponer" class="texto-info-reponer"></p>
        
        <form action="index.php?accion=sumar_stock" method="POST">
            <input type="hidden" name="id_producto" id="entradaIdProductoReponer">
            
            <span class="form-label-inv">Unidades a Sumar</span>
            <input type="number" name="cantidad_sumar" class="input-modal-inv" min="1" value="1" required>

            <button type="submit" class="btn-modal-guardar-inv">AÑADIR AL INVENTARIO</button>
        </form>
    </div>
</div>

<script>
    const entradaBuscador = document.getElementById('entradaBuscadorInventario');
    const listaTarjetasProductos = document.querySelectorAll('.tarjeta-prod');
    const botonPestanaTodos = document.getElementById('botonPestanaTodos');
    const botonPestanaBajo = document.getElementById('botonPestanaBajo');
    let estadoFiltroBajoStock = false;

    function aplicarFiltrosInventario() {
        const textoBusqueda = entradaBuscador.value.toLowerCase();
        let contadorProductosVisibles = 0;

        listaTarjetasProductos.forEach(tarjetaActual => {
            const nombreProducto = tarjetaActual.dataset.nombre;
            const indicadorBajoStock = tarjetaActual.dataset.bajo === 'si';
            
            const cumpleFiltroTexto = nombreProducto.includes(textoBusqueda);
            const cumpleFiltroStock = !estadoFiltroBajoStock || indicadorBajoStock;

            if (cumpleFiltroTexto && cumpleFiltroStock) {
                tarjetaActual.style.display = 'flex';
                contadorProductosVisibles++;
            } else {
                tarjetaActual.style.display = 'none';
            }
        });

        document.getElementById('textoTotalProductos').innerText = contadorProductosVisibles;
    }

    entradaBuscador.addEventListener('input', aplicarFiltrosInventario);

    botonPestanaTodos.addEventListener('click', () => {
        estadoFiltroBajoStock = false;
        botonPestanaTodos.classList.add('activo');
        botonPestanaBajo.classList.remove('activo');
        aplicarFiltrosInventario();
    });

    botonPestanaBajo.addEventListener('click', () => {
        estadoFiltroBajoStock = true;
        botonPestanaBajo.classList.add('activo');
        botonPestanaTodos.classList.remove('activo');
        aplicarFiltrosInventario();
    });

    const ventanaModalNuevo = document.getElementById('modalNuevoProducto');
    const ventanaModalAjustar = document.getElementById('modalAjustarStock');
    const ventanaModalReponer = document.getElementById('modalReponerStock');

    function cerrarTodosLosModales() {
        ventanaModalNuevo.classList.replace('modal-activo', 'modal-oculto');
        ventanaModalAjustar.classList.replace('modal-activo', 'modal-oculto');
        ventanaModalReponer.classList.replace('modal-activo', 'modal-oculto');
    }

    function abrirModalNuevoProducto() {
        cerrarTodosLosModales();
        ventanaModalNuevo.classList.replace('modal-oculto', 'modal-activo');
    }

    function abrirModalAjustarStock() {
        cerrarTodosLosModales();
        ventanaModalAjustar.classList.replace('modal-oculto', 'modal-activo');
    }

    function abrirModalReponerStock(identificadorProducto, nombreProducto, stockActualProducto) {
        cerrarTodosLosModales();
        document.getElementById('entradaIdProductoReponer').value = identificadorProducto;
        document.getElementById('textoProductoReponer').innerHTML = `<strong class="nombre-producto-reponer">${nombreProducto}</strong><br><span class="texto-stock-actual">Stock actual: ${stockActualProducto}</span>`;
        ventanaModalReponer.classList.replace('modal-oculto', 'modal-activo');
    }

    const selectorAjusteManual = document.getElementById('selectorAjustarStock');
    const entradaNuevaCantidadStock = document.getElementById('entradaNuevoStock');

    if (selectorAjusteManual && entradaNuevaCantidadStock) {
        selectorAjusteManual.addEventListener('change', function() {
            const opcionMarcada = this.options[this.selectedIndex];
            const cantidadStockOculta = opcionMarcada.getAttribute('data-stock');

            if (cantidadStockOculta !== null && cantidadStockOculta !== "") {
                entradaNuevaCantidadStock.value = cantidadStockOculta;
            } else {
                entradaNuevaCantidadStock.value = "";
            }
        });
    }

    function confirmarEliminarProducto(identificadorProducto, nombreProducto) {
        if (confirm(`¿Estás seguro de que deseas ELIMINAR el producto "${nombreProducto}"?\nEsta acción lo quitará permanentemente del inventario.`)) {
            window.location.href = `index.php?accion=eliminar_producto&id=${identificadorProducto}`;
        }
    }
</script>

</body>
</html>