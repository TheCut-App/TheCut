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
                <input type="password" name="password" id="entradaContrasenaNueva" class="input-premium" required>
                <span class="ojo-password" onclick="alternarVisibilidadContrasena('entradaContrasenaNueva', this)">&#128065;</span>
            </div>
        </div>

        <div class="footer-formulario">
            <a href="index.php?accion=gestion_equipo" class="btn-volver">CANCELAR</a>
            <button type="submit" class="btn-crear">CREAR EMPLEADO</button>
        </div>
    </form>
</div>

<script>
    function alternarVisibilidadContrasena(identificadorEntrada, elementoIcono) {
        const campoEntrada = document.getElementById(identificadorEntrada);
        
        if (campoEntrada.type === "password") {
            campoEntrada.type = "text";
            elementoIcono.innerHTML = "&#128274;"; 
        } else {
            campoEntrada.type = "password";
            elementoIcono.innerHTML = "&#128065;"; 
        }
    }
</script>
</body>
</html>