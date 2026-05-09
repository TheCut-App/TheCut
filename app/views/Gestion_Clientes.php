<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Gestión de Clientes</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        :root { --verde-bg: #0b1f18; --dorado: #d4af37; --dorado-oc: #8b733d; --texto: #e0e0e0; }
        body { 
            background-color: #000; margin: 0; padding: 40px 20px; 
            min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; 
        }
        
        .contenedor-principal {
            width: 100%; max-width: 1200px; background: var(--verde-bg);
            border: 2px solid var(--dorado); border-radius: 8px; position: relative; padding: 40px;
            display: flex; flex-direction: column; height: auto; 
        }
        .contenedor-principal::before {
            content: ''; position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px solid var(--dorado-oc); border-radius: 4px; pointer-events: none;
        }

        .barra-busqueda { width: 100%; background: rgba(0,0,0,0.3); border: 1px solid #444; color: white; padding: 15px; border-radius: 30px; outline: none; margin-bottom: 30px; font-size: 1rem; box-sizing: border-box; }
        
        .fila-cliente { display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 1fr 1fr 2fr; gap: 15px; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid #333; border-radius: 40px; padding: 10px 20px; margin-bottom: 12px; }
        .avatar-circulo { width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--dorado); display: flex; align-items: center; justify-content: center; color: var(--dorado); font-weight: bold; }
        .badge-vip { background: var(--dorado); color: #000; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.8rem; }
        .badge-regular { border: 1px solid #666; color: #aaa; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; }
        .badge-nuevo { background: #1a7b3c; color: #fff; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.8rem; }
        .btn-accion { background: transparent; border: 1px solid var(--dorado); color: var(--dorado); padding: 5px 12px; border-radius: 20px; cursor: pointer; font-size: 0.7rem; text-transform: uppercase; margin-left: 5px; }

        /* MAGIA: Esto hace que el recuadro no se desborde y tenga scroll interno */
        #contenedorLista {
            max-height: 450px; 
            overflow-y: auto; 
            padding-right: 10px;
        }
        #contenedorLista::-webkit-scrollbar { width: 6px; }
        #contenedorLista::-webkit-scrollbar-thumb { background: var(--dorado); border-radius: 10px; }
    </style>
</head>
<body>

<div class="contenedor-principal">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: var(--dorado); margin: 0; letter-spacing: 3px;">GESTIÓN DE CLIENTES</h1>
        <a href="index.php?accion=admin" class="btn-accion" style="text-decoration: none; font-weight: bold; padding: 10px 20px;">VOLVER AL PANEL</a>
    </div>
    
    <input type="text" id="inputBuscador" class="barra-busqueda" placeholder="Buscar por nombre, apellido o teléfono..." autofocus>

    <div style="display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 1fr 1fr 2fr; gap: 15px; color: #888; font-size: 0.8rem; padding: 0 20px 10px 20px; border-bottom: 1px solid #333; margin-bottom: 15px;">
        <span>CLIENTE</span>
        <span>TELÉFONO</span>
        <span>ÚLTIMA VISITA</span>
        <span>TOTAL GASTADO</span>
        <span style="text-align: center;">ESTADO</span>
        <span style="text-align: right;">ACCIONES</span>
    </div>

    <div id="contenedorLista">
        </div>
</div>

<script>
    const buscador = document.getElementById('inputBuscador');
    const lista = document.getElementById('contenedorLista');

    // La función ahora recibe el número de página (por defecto la 1)
    async function buscar(pagina = 1) {
        const query = buscador.value;
        const response = await fetch(`index.php?accion=api_buscar_clientes&s=${query}&p=${pagina}`);
        lista.innerHTML = await response.text();
    }

    // Si el usuario escribe algo nuevo, siempre empezamos a buscar desde la página 1
    buscador.addEventListener('input', () => buscar(1));

    // Carga inicial al entrar en la sección
    buscar(1);
</script>

</body>
</html>