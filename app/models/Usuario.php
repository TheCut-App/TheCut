<?php

class Usuario{
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    //Obligatorio parametros username, password, nombre, apellido_1
    public function crearUsuario($username, $password, $nombre, $apellido_1, $apellido_2 = null, $is_admin = false, $url_foto = null) {
    
        $sql = "INSERT INTO usuarios (
                    username, 
                    password, 
                    nombre, 
                    apellido_1, 
                    apellido_2, 
                    is_admin, 
                    is_active, 
                    url_foto, 
                    fecha_alta
                ) VALUES (
                    :username, 
                    :password, 
                    :nombre, 
                    :apellido_1, 
                    :apellido_2, 
                    :is_admin, 
                    true, 
                    :url_foto, 
                    CURRENT_DATE
                )";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
    'username'   => $username,
    'password'   => $password,
    'nombre'     => $nombre,
    'apellido_1' => $apellido_1,
    'apellido_2' => $apellido_2,
    'is_admin'   => $is_admin ? 1 : 0, 
    'url_foto'   => $url_foto
]);
    }

    //Obligatorio parametros id, username, password, nombre, apellido_1
    public function actualizarUsuario($id, $nombre, $apellido_1, $apellido_2 = null, $is_admin = false, $url_foto = null) {
    
    $sql = "UPDATE usuarios SET 
                nombre = :nombre, 
                apellido_1 = :apellido_1, 
                apellido_2 = :apellido_2, 
                is_admin = :is_admin, 
                url_foto = :url_foto
            WHERE id = :id";
    
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute([
        'id'         => $id,
        'nombre'     => $nombre,
        'apellido_1' => $apellido_1,
        'apellido_2' => $apellido_2,
        'is_admin'   => $is_admin ? 1 : 0,
        'url_foto'   => $url_foto
    ]);
}

    public function buscarPorUsername($username){
        $sql = "SELECT * FROM usuarios WHERE username = :username LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $usuario = $stmt->fetch();

        return $usuario;
    }

    public function esAdmin($id){
        $sql = "SELECT is_admin FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $esAdmin = $stmt->fetch();

        //Mira que exista y que sea igual a true
        return $esAdmin && $esAdmin['is_admin'] == true;

    }

    public function esActivo($id){
        $sql = "SELECT is_active FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $esActivo = $stmt->fetch();

        //Mira que exista y que sea igual a true
        return $esActivo && $esActivo['is_active'] == true;

    }

    //Si esta activo pasa a inactivo y viceversa
    public function toggleActivo($id) {
        $sql = "UPDATE usuarios SET is_active = NOT is_active WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute(['id' => $id]);
    }

    //Muestra el id, username y nombre de todos los barberos activos
    public function listarBarberos() {
        $sql = "SELECT id, username, nombre 
                FROM usuarios 
                WHERE rol = 'barbero' AND is_active = true 
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}