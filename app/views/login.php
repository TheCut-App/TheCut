<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheCut - Login</title>
    <link rel="stylesheet" href="../../public/assets/css/login.css">
</head>
<body>
    <div class="login-contenedor">
        <div class="seccion-logo">
            <div class="contenedor-logo">
                <img src="../../public/assets/img/logo.png" alt="Logo de TheCut" class="logo-principal">
            </div>
        </div>

        <div class="seccion-formulario">
            <h1 class="titulo-principal">Domina tu barbería</h1>
            <form id="formularioLogin" class="formulario-login" action="../../index.php?accion=login" method="POST">     
                <div class="grupo-campo">
                    <label for="usuario">USUARIO</label>
                    <input type="text" id="usuario" name="usuario" autocomplete="off">
                </div>
                <div class="grupo-campo">
                    <label for="contrasena">CONTRASEÑA</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>
                <button type="submit" class="boton-enviar">ACEPTAR</button>
            </form>
        </div>
    </div>
<?php 
    // Comprobamos si existe un error guardado en la sesión
    if (isset($_SESSION['error_login'])): 
    ?>
        <script>
            // Lanzamos el alert de JavaScript con el texto del error
            alert("<?php echo $_SESSION['error_login']; ?>");
        </script>
    <?php 
        // Destruimos la variable para que el alert no vuelva a salir al actualizar la página
        unset($_SESSION['error_login']); 
    endif; 
    ?>
</body>
</html>