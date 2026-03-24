<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheCut - Login</title>
    <link rel="stylesheet" href="Login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-wrapper">
                <img src="logo.png" alt="TheCut Logo" class="main-logo">
                <h2 class="brand-name">TheCut</h2>
            </div>
        </div>

        <div class="form-section">
            <h1 class="main-title">Domina tu barbería</h1>
            <form id="loginForm" class="login-form">
                <div class="input-group">
                    <label for="user">USUARIO</label>
                    <input type="text" id="user" name="user" autocomplete="off" required>
                </div>
                <div class="input-group">
                    <label for="password">CONTRASEÑA</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-submit">ACEPTAR</button>
            </form>
        </div>
    </div>
</body>
</html>