<?php

class AuthController {

    private $usuarioModel;
    private $db;

    public function __construct($conexion_db) {
        //Si no esta con sesion iniciada que la incie 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Instanciamos el modelo con la conexión que nos llega
        $this->usuarioModel = new Usuario($conexion_db);
        $this->db = $conexion_db;
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

            if (!empty($username) && !empty($password)) {
                
                $usuario = $this->usuarioModel->buscarPorUsername($username);

                if ($usuario) {
                    if ($usuario['is_active']) {
                        
                        if ($password === $usuario['password']) {
                            
                            $_SESSION['user_id'] = $usuario['id'];
                            $_SESSION['username'] = $usuario['username'];
                            $_SESSION['is_admin'] = $usuario['is_admin'];

                            if ($usuario['is_admin']) {
                                // Redirigimos al semáforo pidiendo la acción admin
                                header("Location: index.php?accion=admin"); 
                            } else if ($usuario['username'] === 'invitado') {
                                header("Location: index.php?accion=invitado"); 
                            } else {
                                header("Location: index.php?accion=empleado"); 
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