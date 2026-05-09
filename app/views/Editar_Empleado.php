<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Editar Empleado</title>
    <link rel="stylesheet" href="public/assets/css/style_editar_empleado.css">
</head>
<body>
<div class="editar-container">
    <div class="header-edit">
        <h1>Perfil del Empleado</h1>
    </div>
    
    <form action="index.php?accion=actualizar_empleado" method="POST">
        <input type="hidden" name="id_usuario" value="<?= $empleado['id'] ?>">
        
        <div class="layout-grid">
            <div class="col-perfil">
                <div class="avatar-circulo">
                    <img src="<?= $empleado['url_foto'] ?: 'public/assets/img/logo.png' ?>" alt="Foto Perfil">
                </div>
                <h2 style="margin: 0; color: white;"><?= strtoupper($empleado['nombre']) ?></h2>
                <p style="color: var(--dorado); margin-top: 5px;"><?= strtoupper($empleado['rol'] ?? 'BARBERO') ?></p>
                <p style="font-size: 0.8rem; color: #888;">Alta: <?= date('d/m/Y', strtotime($empleado['fecha_alta'])) ?></p>
                
                <div style="margin-top: 30px; text-align: left; padding-left: 10px;">
                    <span class="section-label">Estado Operativo</span>
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_active" <?= $empleado['is_active'] ? 'checked' : '' ?>> Empleado Activo
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_admin" <?= $empleado['is_admin'] ? 'checked' : '' ?>> Permisos de Admin
                    </label>
                </div>
            </div>
            
            <div class="col-datos">
                <span class="section-label">Información Personal</span>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <input type="text" name="nombre" class="entrada-texto" value="<?= $empleado['nombre'] ?>" placeholder="Nombre" required>
                    <input type="text" name="apellido_1" class="entrada-texto" value="<?= $empleado['apellido_1'] ?>" placeholder="Primer Apellido" required>
                </div>
                <input type="text" name="apellido_2" class="entrada-texto" value="<?= $empleado['apellido_2'] ?>" placeholder="Segundo Apellido">

                <span class="section-label">Credenciales de Acceso</span>
                <input type="text" name="username" class="entrada-texto" value="<?= $empleado['username'] ?>" placeholder="Usuario" required>
                
                <div class="grupo-entrada-password">
                    <input type="password" name="password" id="editPassInput" class="entrada-texto input-pass" value="<?= $empleado['password'] ?>" placeholder="Contraseña" required>
                    
                    <span class="ojo-interruptor" onclick="togglePass('editPassInput', this)">
                        <svg class="icon-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        
                        <svg class="icon-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 013.212-4.517M17.398 17.398L19.5 19.5M3 3l1.5 1.5M21 21l-1.5-1.5M10 10l.504.504A3 3 0 1113.875 13.875A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7zM9.88 9.88l-3.212-3.212m14.242 14.242l-3.212-3.212M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="footer-btns">
            <a href="index.php?accion=gestion_equipo" class="btn-cancelar">VOLVER ATRÁS</a>
            <button type="submit" class="btn-guardar">GUARDAR CAMBIOS</button>
        </div>
    </form>
</div>

<script>
function togglePass(inputId, iconoContenedor) {
    const input = document.getElementById(inputId);
    const iconOpen = iconoContenedor.querySelector('.icon-open');
    const iconClosed = iconoContenedor.querySelector('.icon-closed');

    if (input.type === "password") {
        input.type = "text";
        iconOpen.style.display = "none";
        iconClosed.style.display = "block";
    } else {
        input.type = "password";
        iconOpen.style.display = "block";
        iconClosed.style.display = "none";
    }
}
</script>
</body>
</html>