<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Nuevo Cliente</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        :root { --verde-bg: #0b1f18; --dorado: #d4af37; --dorado-oc: #8b733d; --texto: #e0e0e0; }
        body { 
            background-color: #000; color: var(--texto); font-family: 'Segoe UI', serif; 
            margin: 0; padding: 40px 20px; box-sizing: border-box; 
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        
        .formulario-contenedor {
            width: 100%; max-width: 650px; background: var(--verde-bg);
            border: 2px solid var(--dorado); border-radius: 8px; position: relative; padding: 40px;
            display: flex; flex-direction: column; box-sizing: border-box;
        }
        .formulario-contenedor::before {
            content: ''; position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px solid var(--dorado-oc); border-radius: 4px; pointer-events: none;
        }

        .cabecera-formulario { text-align: center; border-bottom: 1px solid var(--dorado-oc); padding-bottom: 20px; margin-bottom: 40px; }
        .cabecera-formulario h1 { color: var(--dorado); font-size: 2.2rem; letter-spacing: 4px; margin: 0; text-transform: uppercase; }

        .etiqueta-campo { color: var(--dorado); font-size: 0.9rem; font-weight: bold; margin-bottom: 10px; display: block; text-transform: uppercase; letter-spacing: 1px; }
        
        .entrada-texto { 
            width: 100%; background: transparent; border: 1px solid #444; color: white; 
            padding: 15px; border-radius: 4px; outline: none; font-size: 1.1rem; box-sizing: border-box;
            margin-bottom: 25px; transition: 0.3s;
        }
        .entrada-texto:focus { border-color: var(--dorado); background: rgba(212, 175, 55, 0.05); }

        .rejilla-nombres { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        .pie-formulario { 
            display: flex; justify-content: space-between; align-items: center; 
            border-top: 1px solid rgba(139, 115, 61, 0.3); padding-top: 30px; margin-top: 10px; 
        }
        .boton-cancelar { color: #aaa; text-decoration: none; font-size: 1rem; text-transform: uppercase; font-weight: bold; transition: 0.2s; }
        .boton-cancelar:hover { color: white; }
        
        .boton-guardar { 
            background: var(--dorado); color: black; border: none; padding: 15px 40px; 
            border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; text-transform: uppercase; transition: 0.2s;
        }
        .boton-guardar:hover { background: #e6c875; }
    </style>
</head>
<body>

<div class="formulario-contenedor">
    <div class="cabecera-formulario">
        <h1>NUEVO CLIENTE</h1>
    </div>

    <form action="index.php?accion=guardar_cliente" method="POST" style="display: flex; flex-direction: column; flex-grow: 1;">
        
        <div class="rejilla-nombres">
            <div>
                <span class="etiqueta-campo">Nombre *</span>
                <input type="text" name="nombre" class="entrada-texto" required autofocus>
            </div>
            <div>
                <span class="etiqueta-campo">Apellido</span>
                <input type="text" name="apellido" class="entrada-texto">
            </div>
        </div>

        <div>
            <span class="etiqueta-campo">Teléfono *</span>
            <input type="tel" 
                name="telefono" 
                class="entrada-texto" 
                required 
                pattern="[0-9]{9}" 
                maxlength="9" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                title="El teléfono debe tener exactamente 9 números">
        </div>

        <div class="pie-formulario">
            <a href="index.php?accion=nueva_cita" class="boton-cancelar">CANCELAR</a>
            <button type="submit" class="boton-guardar">GUARDAR Y SEGUIR</button>
        </div>
    </form>
</div>

</body>
</html>