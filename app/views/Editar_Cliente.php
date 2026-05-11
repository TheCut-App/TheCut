<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Editar Cliente</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
</head>
<body class="body-centrado-completo">

<div class="formulario-contenedor">
    <div class="cabecera-formulario">
        <h1>EDITAR CLIENTE</h1>
    </div>

    <form action="index.php?accion=guardar_edicion_cliente" method="POST" class="formulario-edicion-cliente">
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
        
        <div class="rejilla-nombres">
            <div>
                <span class="etiqueta-campo">Nombre *</span>
                <input type="text" name="nombre" class="entrada-texto" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
            </div>
            <div>
                <span class="etiqueta-campo">Apellido</span>
                <input type="text" name="apellido" class="entrada-texto" value="<?= htmlspecialchars($cliente['apellido_1']) ?>">
            </div>
        </div>

        <div>
            <span class="etiqueta-campo">Teléfono *</span>
            <input type="tel" 
                name="telefono" 
                class="entrada-texto" 
                value="<?= htmlspecialchars($cliente['telefono']) ?>" 
                pattern="[0-9]{9}" 
                maxlength="9" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                title="El teléfono debe tener exactamente 9 dígitos numéricos"
                required>
        </div>

        <div>
            <span class="etiqueta-campo">Notas del Cliente</span>
            <textarea name="notas" class="entrada-texto area-texto-redimensionable" placeholder="Fórmulas de tinte, preferencias..."><?= htmlspecialchars($cliente['notas'] ?? '') ?></textarea>
        </div>

        <div class="pie-formulario">
            <a href="index.php?accion=gestion_clientes" class="boton-cancelar">CANCELAR</a>
            <button type="submit" class="boton-guardar">GUARDAR CAMBIOS</button>
        </div>
    </form>
</div>

</body>
</html>