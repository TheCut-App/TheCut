<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Nuevo Empleado</title>
    <link rel="stylesheet" href="public/assets/css/style_nuevo_empleado.css">
</head>
<body>

<div class="contenedor-form-empleado">
    <h1 class="titulo-seccion">NUEVO EMPLEADO</h1>

    <form action="index.php?accion=guardar_empleado" method="POST">
        
        <div class="fila-doble">
            <div class="grupo-entrada">
                <label class="etiqueta-dorada">Nombre *</label>
                <input type="text" name="nombre" class="input-premium" required autofocus>
            </div>
            <div class="grupo-entrada">
                <label class="etiqueta-dorada">Apellido</label>
                <input type="text" name="apellido" class="input-premium">
            </div>
        </div>

        <div class="grupo-entrada">
            <label class="etiqueta-dorada">Usuario (Login) *</label>
            <input type="text" name="username" class="input-premium" required>
        </div>

        <div class="grupo-entrada">
            <label class="etiqueta-dorada">Contraseña *</label>
            <div class="grupo-password">
                <input type="password" name="password" id="passInput" class="input-premium" required>
                <span class="ojo-password" onclick="togglePass('passInput', this)">👁️</span>
            </div>
        </div>

        <div class="footer-formulario">
            <a href="index.php?accion=gestion_equipo" class="btn-volver">CANCELAR</a>
            <button type="submit" class="btn-crear">CREAR EMPLEADO</button>
        </div>
    </form>
</div>

<script>
    function togglePass(inputId, icono) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icono.innerText = "🔒"; // Cambia el icono al cerrar
        } else {
            input.type = "password";
            icono.innerText = "👁️";
        }
    }
</script>
</body>
</html>