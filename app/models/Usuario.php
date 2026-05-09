<?php

class Usuario{
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    //Obligatorio parametros username, password, nombre, apellido_1
    public function crearUsuario($username, $password, $nombre, $apellido_1, $apellido_2 = null, $is_admin = false, $url_foto = null) {
        $sql = "INSERT INTO usuarios (
                    username, password, nombre, apellido_1, apellido_2, 
                    is_admin, is_active, url_foto, fecha_alta, rol
                ) VALUES (
                    :username, :password, :nombre, :apellido_1, :apellido_2, 
                    :is_admin, true, :url_foto, CURRENT_DATE, 'barbero'
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
    public function listarBarberosPorFecha($fecha) {
        $sql = "SELECT u.id, u.username, u.nombre 
                FROM usuarios u
                INNER JOIN horarios h ON u.id = h.id_usuario
                WHERE u.rol = 'barbero' 
                  AND u.is_active = true 
                  AND h.fecha = :fecha
                ORDER BY u.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerEstadisticasEquipo() {
        $inicioMes = date('Y-m-01 00:00:00');
        $finMes = date('Y-m-t 23:59:59');

        $sql = "SELECT 
                    u.id, 
                    u.nombre, 
                    u.url_foto,
                    COALESCE(SUM(s.precio), 0) as total_servicios
                FROM usuarios u
                LEFT JOIN citas c ON u.id = c.id_usuario 
                    AND c.fecha_cita BETWEEN :inicio AND :fin 
                    AND c.estado IN ('Pagado', 'Completada')
                LEFT JOIN citas_servicios cs ON c.id = cs.id_cita
                LEFT JOIN servicios s ON cs.id_servicio = s.id
                WHERE u.rol = 'barbero' AND u.is_active = true
                GROUP BY u.id, u.nombre, u.url_foto
                ORDER BY u.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['inicio' => $inicioMes, 'fin' => $finMes]);
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($empleados as &$emp) {
            $emp['total_productos'] = 0; // Pendiente de crear la tabla de ventas de productos
            $emp['total_mes'] = $emp['total_servicios'] + $emp['total_productos'];
        }

        return $empleados;
    }

    public function obtenerUsuarioPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuarioCompleto($id, $nombre, $apellido_1, $apellido_2, $username, $password, $is_admin, $is_active) {
        $sql = "UPDATE usuarios SET 
                    nombre = :nombre, 
                    apellido_1 = :apellido_1, 
                    apellido_2 = :apellido_2, 
                    username = :username, 
                    password = :password, 
                    is_admin = :is_admin, 
                    is_active = :is_active 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'apellido_1' => $apellido_1,
            'apellido_2' => $apellido_2,
            'username' => $username,
            'password' => $password,
            'is_admin' => $is_admin ? 1 : 0,
            'is_active' => $is_active ? 1 : 0
        ]);
    }

    public function obtenerFechasLaborables($id_usuario, $fecha_inicio, $fecha_fin) {
        $sql = "SELECT fecha FROM horarios WHERE id_usuario = :id AND fecha BETWEEN :inicio AND :fin";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_usuario, 'inicio' => $fecha_inicio, 'fin' => $fecha_fin]);
        $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN); 
        
        // Convertimos la fecha a formato estricto Y-m-d para que coincida perfectamente con los botones
        return array_map(function($f) {
            return date('Y-m-d', strtotime($f));
        }, $resultados);
    }

    public function actualizarFechasLaborables($id_usuario, $fechas_activas, $fecha_inicio, $fecha_fin) {
        try {
            $stmtDel = $this->db->prepare("DELETE FROM horarios WHERE id_usuario = :id AND fecha BETWEEN :inicio AND :fin");
            $stmtDel->execute(['id' => $id_usuario, 'inicio' => $fecha_inicio, 'fin' => $fecha_fin]);

            if (!empty($fechas_activas)) {
                $sqlInsert = "INSERT INTO horarios (id_usuario, fecha) VALUES (:id, :fecha)";
                $stmtInsert = $this->db->prepare($sqlInsert);
                
                foreach ($fechas_activas as $fecha) {
                    $stmtInsert->execute(['id' => $id_usuario, 'fecha' => $fecha]);
                }
            }
            return true;
        } catch (PDOException $e) {
            // Si hay un error, paralizamos la pantalla en blanco para que veas el fallo exacto
            die("ERROR AL GUARDAR EN SUPABASE: " . $e->getMessage());
        }
    }

}