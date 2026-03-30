<?php

class AuthController {

    private $usuarioModel;

    public function __construct($conexion_db) {
        //Si no esta con sesion iniciada que la incie 
        if (!isset($_SESSION['user_id'])) {
            session_start();
            exit;
        }

        // Instanciamos el modelo con la conexión que nos llega
        $this->usuarioModel = new Usuario($conexion_db);
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $username = trim($_POST['usuario'] ?? '');
            $password = trim($_POST['contrasena'] ?? '');

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
                            $this->redirigirConError("Contraseña incorrecta.");
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

    private function redirigirConError($mensaje) {
        // Guardamos el error en la sesión temporalmente
        $_SESSION['error_login'] = $mensaje;
        
        // Redirigimos al index con una URL totalmente limpia
        header("Location: index.php");
        exit;
    }
}
?>