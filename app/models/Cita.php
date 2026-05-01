<?php

class Cita {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    //Hace conteo de las citas que tiene ese barbero hoy
    public function citasHoy($id_usuario) {
        $sql = "SELECT COUNT(*) as total 
                FROM citas 
                WHERE DATE(fecha_cita) = CURRENT_DATE 
                AND id_usuario = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_usuario]);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    //Hace conteo de las citas totales de todos los barberos hoy
   public function citasTotalesHoy() {
        $sql = "SELECT COUNT(*) as total 
                FROM citas 
                WHERE DATE(fecha_cita) = CURRENT_DATE";
        
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    //Todas las citas de todos los barberos, si no se pasa fecha es la de hoy, si se pasa fecha la de ese dia
    public function citasTodosLosBarberosPorFecha($fecha) {

    $sql = "SELECT 
                c.id,
                c.id_usuario, 
                c.fecha_cita, 
                cl.nombre AS cliente_nombre,
                COALESCE(STRING_AGG(s.nombre, ' + '), 'Sin servicio') AS servicios_nombres,
                COALESCE(SUM(s.duracion), 30) AS duracion_total
            FROM public.citas c
            LEFT JOIN public.clientes cl ON c.id_cliente = cl.id
            LEFT JOIN public.citas_servicios cs ON c.id = cs.id_cita
            LEFT JOIN public.servicios s ON cs.id_servicio = s.id
            WHERE c.fecha_cita::date = :fecha 
            GROUP BY c.id, c.id_usuario, c.fecha_cita, cl.nombre
            ORDER BY c.fecha_cita ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['fecha' => $fecha]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}