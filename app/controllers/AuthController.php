<?php

class AuthController {

    private $usuarioModel;

    public function __construct($conexion_db) {
        //Si no esta con sesion iniciada que la incie 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Instanciamos el modelo con la conexión que nos llega
        $this->usuarioModel = new Usuario($conexion_db);
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $boton_pulsado = $_POST['btn_login'] ?? 'normal';

            if ($boton_pulsado === 'invitado') {
                // Si pulsó INVITADO, ignoramos los inputs y forzamos estas credenciales
                $username = 'invitado';
                $password = '1234';
            } else {
                // Si pulsó ACEPTAR, recogemos lo que haya escrito en el formulario
                $username = trim($_POST['usuario'] ?? '');
                $password = trim($_POST['contrasena'] ?? '');
            }

            if (isset($username) && isset($password)) {
                
                $usuario = $this->usuarioModel->buscarPorUsername($username);

                if ($usuario) {
                    if ($usuario['is_active']) {
                        
                        if ($password === $usuario['password']) {
                            
                            $_SESSION['user_id'] = $usuario['id'];
                            $_SESSION['username'] = $usuario['username'];
                            $_SESSION['is_admin'] = $usuario['is_admin'];

                            if ($usuario['is_admin']) {
                                header("Location: app/views/Adm_Home.php");
                            } else if ($usuario['username'] === 'invitado') {
                                header("Location: app/views/Inv_Home.php");
                            } else {
                                header("Location: app/views/Emp_Home.php");
                            }
                            exit;
                            
                        } else {
                            $this->redirigirConError("Contraseña incorrecta.", $username);
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

    private function redirigirConError($mensaje, $usuario_intentado = '') {
        $_SESSION['error_login'] = $mensaje;

        // Si se ha pasado un usuario, lo guardamos en la sesión
        if (!empty($usuario_intentado)) {
            $_SESSION['login_username'] = $usuario_intentado;
        }

        header("Location: index.php");
        exit;
    }
}
?>