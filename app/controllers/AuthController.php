<?php

class AuthController {

    private $usuarioModel;
    private $databaseConnection;

    public function __construct($databaseConnection) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->usuarioModel = new Usuario($databaseConnection);
        $this->databaseConnection = $databaseConnection;
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $botonPulsado = $_POST['btn_login'] ?? 'normal';

            if ($botonPulsado === 'invitado') {
                $nombreUsuario = 'invitado';
                $contrasena = '1234';
            } else {
                $nombreUsuario = trim($_POST['usuario'] ?? '');
                $contrasena = trim($_POST['contrasena'] ?? '');
            }

            if (!empty($nombreUsuario) && !empty($contrasena)) {
                $datosUsuario = $this->usuarioModel->buscarPorUsername($nombreUsuario);

                if ($datosUsuario) {
                    if ($datosUsuario['is_active']) {
                        if ($contrasena === $datosUsuario['password']) {
                            
                            $_SESSION['user_id'] = $datosUsuario['id'];
                            $_SESSION['username'] = $datosUsuario['username'];
                            $_SESSION['is_admin'] = $datosUsuario['is_admin'];

                            if ($datosUsuario['is_admin']) {
                                header("Location: index.php?accion=admin"); 
                            } else if ($datosUsuario['username'] === 'invitado') {
                                header("Location: index.php?accion=invitado"); 
                            } else {
                                header("Location: index.php?accion=empleado"); 
                            }
                            exit;
                            
                        } else {
                            $this->redirigirConError("Contraseña incorrecta.", $nombreUsuario);
                        }
                    } else {
                        $this->redirigirConError("Cuenta de usuario desactivada.");
                    }
                } else {
                    $this->redirigirConError("El usuario no existe.");
                }
            } else {
                $this->redirigirConError("Por favor, rellena todos los campos.");
            }
        }
    }

    private function redirigirConError($mensajeError, $usuarioIntentado = '') {
        $_SESSION['error_login'] = $mensajeError;

        if (!empty($usuarioIntentado)) {
            $_SESSION['login_username'] = $usuarioIntentado;
        }

        header("Location: index.php");
        exit;
    }
}
?>