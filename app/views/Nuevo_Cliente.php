<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Nuevo Cliente</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body class="body-centrado-completo">

<div class="formulario-contenedor">
    <div class="cabecera-formulario">
        <h1>NUEVO CLIENTE</h1>
    </div>

    <form action="index.php?accion=guardar_cliente" method="POST" class="formulario-edicion-cliente">
        
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