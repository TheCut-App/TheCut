<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Nuevo Empleado</title>
    <link rel="stylesheet" href="public/assets/css/style_equipo.css">
    <style>
        .formulario-contenedor {
            width: 100%; max-width: 600px; margin: 0 auto;
        }
        .etiqueta-campo {
            color: var(--dorado-artdeco); font-size: 0.9rem; font-weight: bold; 
            margin-bottom: 10px; display: block; text-transform: uppercase;
        }
        .entrada-texto { 
            width: 100%; background: var(--azul-profundo); border: 1px solid rgba(197, 160, 89, 0.4); 
            color: var(--champan); padding: 15px; border-radius: 4px; outline: none; 
            font-size: 1.1rem; margin-bottom: 25px; transition: 0.3s;
        }
        .entrada-texto:focus { border-color: var(--dorado-brillante); }
        .rejilla-nombres { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .boton-guardar {
            background: var(--dorado-artdeco); color: var(--azul-profundo); border: none; 
            padding: 15px 40px; border-radius: 4px; font-weight: bold; cursor: pointer; 
            font-size: 1.1rem; text-transform: uppercase; width: 100%;
        }
    </style>
</head>
<body>

<div class="panel-equipo formulario-contenedor">
    <div class="cabecera-equipo" style="border-bottom: none; margin-bottom: 20px; justify-content: center;">
        <h1 style="font-size: 2rem;">NUEVO EMPLEADO</h1>
    </div>

    <form action="index.php?accion=guardar_empleado" method="POST">
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
            <span class="etiqueta-campo">Usuario (Login) *</span>
            <input type="text" name="username" class="entrada-texto" required>
        </div>

        <div>
            <span class="etiqueta-campo">Contraseña *</span>
            <input type="password" name="password" class="entrada-texto" required>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <a href="index.php?accion=gestion_equipo" class="btn-volver" style="margin-right: 20px;">CANCELAR</a>
            <button type="submit" class="boton-guardar">CREAR EMPLEADO</button>
        </div>
    </form>
</div>

</body>
</html>