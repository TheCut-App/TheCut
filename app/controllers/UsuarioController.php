<?php

class UsuarioController{

    private $usuario;

    public function __construct() {
            $this->usuario = new Usuario();

            //Si no esta con sesion iniciada, a login
            if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    }

    //Verifica los datos que deberian llegar, si no se rellenan vacios
    public function guardar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido_1 = $_POST['apellido_1'] ?? '';
            $apellido_2 = $_POST['apellido_2'] ?? null;
            $is_admin   = isset($_POST['is_admin']) ? true : false; //Si no envia nada, falso por defecto
            $url_foto   = $_POST['url_foto'] ?? null;

        

            //Si detecta el id, pasa a actualizarlo, si no pasa a crearlo
            if ($id) {
                $this->usuario->actualizarUsuario($id, $nombre, $apellido_1, $apellido_2, $is_admin, $url_foto);
                return 'Usuario Actualizado';
            }
                $this->usuario->crearUsuario($username, $password, $nombre, $apellido_1, $apellido_2, $is_admin, $url_foto);
                return 'Usuario Creado';


        }
        
    } 
}