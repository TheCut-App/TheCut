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

}