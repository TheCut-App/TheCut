<?php

class Cita {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    //Hace conteo de las citas que tiene ese barbero hoy
    public function citasHoy(){
        $sql = "SELECT COUNT(*) as total FROM citas WHERE fecha = CURRENT_DATE AND barbero_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $barbero_id]);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    //Hace conteo de las citas totales de todos los barberos hoy
    public function citasTotalesHoy(){
        $sql = "SELECT COUNT(*) as total FROM citas WHERE DATE(fecha_cita) = CURRENT_DATE";
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    //Todas las citas de todos los barberos, si no se pasa fecha es la de hoy, si se pasa fecha la de ese dia
    public function citasTodosLosBarberosPorFecha($fecha = null) {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        }

        $sql = "SELECT 
                    c.id_usuario, 
                    c.fecha_cita, 
                    c.color, 
                    cl.nombre AS cliente_nombre,
                    s.nombre AS servicio_nombre
                FROM citas c
                JOIN clientes cl ON c.id_cliente = cl.id
                LEFT JOIN citas_servicios cs ON c.id = cs.id_cita
                LEFT JOIN servicios s ON cs.id_servicio = s.id
                WHERE DATE(c.fecha_cita) = :fecha
                ORDER BY c.fecha_cita ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}