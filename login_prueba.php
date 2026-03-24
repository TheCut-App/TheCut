<?php
session_start();
require_once 'config/db.php';

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameIngresado = $_POST['username'];
    $passwordIngresada = $_POST['password'];

    $consulta = "SELECT id, username, password, is_admin FROM usuarios WHERE username = :username LIMIT 1";
    $sentencia = $pdo->prepare($consulta);
    $sentencia->execute(['username' => $usernameIngresado]);
    $usuarioEncontrado = $sentencia->fetch();

    if ($usuarioEncontrado && $usuarioEncontrado['password'] === $passwordIngresada) {
        
        $_SESSION['idUsuario'] = $usuarioEncontrado['id'];
        $_SESSION['nombreUsuario'] = $usuarioEncontrado['username'];
        $_SESSION['esAdmin'] = $usuarioEncontrado['is_admin'];

        $paginaDestino = $usuarioEncontrado['is_admin'] ? 'adm_home.php' : 'emp_home.php';
        
        header("Location: $paginaDestino");
        exit;
        
    } else {
        $mensajeError = 'Usuario o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - TheCut</title>
    <link rel="stylesheet" href="login_prueba.css">
</head>
<body>

    <div class="pantallaCompleta">
    <div class="tarjetaLogin">
        
        <h2 class="formTitle">Domina tu barbería</h2>

        <div class="contenidoCentral">
            <div class="logoContainer">
                <img src="logo.png" alt="Logo TheCut" class="logoImage">
            </div>

            <form method="POST" action="login.php" class="formularioSection">
                <?php if (!empty($mensajeError)): ?>
                    <p class="errorText"><?= $mensajeError ?></p>
                <?php endif; ?>

                <div class="grupoInput">
                    <label class="inputLabel">Usuario</label>
                    <input type="text" name="username" class="textInput" required autocomplete="off">
                </div>

                <div class="grupoInput">
                    <label class="inputLabel">Contraseña</label>
                    <input type="password" name="password" class="textInput" required>
                </div>

                <button type="submit" class="submitButton">Aceptar</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>