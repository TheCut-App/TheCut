<?php

class Cita {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Hace conteo de las citas que tiene ese barbero en la fecha seleccionada
    public function citasHoy($id_usuario, $fecha) {
        $sql = "SELECT COUNT(*) as total 
                FROM citas 
                WHERE DATE(fecha_cita) = :fecha 
                AND id_usuario = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_usuario, 'fecha' => $fecha]);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    // Hace conteo de las citas totales de todos los barberos en la fecha seleccionada
    public function citasTotalesHoy($fecha) {
        $sql = "SELECT COUNT(*) as total 
                FROM citas 
                WHERE DATE(fecha_cita) = :fecha";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    //Todas las citas de todos los barberos, si no se pasa fecha es la de hoy, si se pasa fecha la de ese dia
    public function citasTodosLosBarberosPorFecha($fecha) {

    $sql = "SELECT 
                c.id,
                c.id_usuario, 
                c.fecha_cita, 
                c.estado,
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

    // Obtener todos los clientes ordenados alfabéticamente
    public function listarClientes() {
        $sql = "SELECT id, nombre, apellido_1, telefono FROM clientes ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los servicios activos
    public function listarServicios() {
        $sql = "SELECT id, nombre, precio, duracion FROM servicios WHERE is_active = true ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Guarda la cita y sus servicios en la base de datos
    public function agendarNuevaCita($id_usuario, $id_cliente, $fecha_hora, $servicios) {
        try {
            $this->db->beginTransaction();

            // 1. Insertamos la cita
            $sqlCita = "INSERT INTO public.citas (id_usuario, id_cliente, fecha_cita, estado, color) 
                        VALUES (:id_usr, :id_cli, :fecha, 'Pendiente', 'cita-verde') RETURNING id";
            
            $stmtCita = $this->db->prepare($sqlCita);
            $stmtCita->execute([
                'id_usr' => $id_usuario,
                'id_cli' => $id_cliente,
                'fecha'  => $fecha_hora
            ]);
            
            $resultado = $stmtCita->fetch(PDO::FETCH_ASSOC);
            $id_cita = $resultado['id'];

            // 2. Insertamos todos los servicios marcados
            if (!empty($servicios)) {
                $sqlServicio = "INSERT INTO public.citas_servicios (id_cita, id_servicio) VALUES (:id_cita, :id_servicio)";
                $stmtServicio = $this->db->prepare($sqlServicio);
                
                foreach ($servicios as $id_srv) {
                    $stmtServicio->execute([
                        'id_cita' => $id_cita,
                        'id_servicio' => $id_srv
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    // Obtener las citas pendientes de cobro para un día específico
    public function obtenerCitasPendientes($fecha) {
        $sql = "SELECT 
                    c.id, 
                    c.fecha_cita, 
                    cl.nombre AS cliente_nombre,
                    cl.apellido_1 AS cliente_apellido,
                    b.nombre AS barbero_nombre,
                    COALESCE(STRING_AGG(s.nombre, ' + '), 'Sin servicio') AS servicios_nombres,
                    COALESCE(SUM(s.precio), 0) AS precio_total
                FROM public.citas c
                JOIN public.clientes cl ON c.id_cliente = cl.id
                JOIN public.usuarios b ON c.id_usuario = b.id
                LEFT JOIN public.citas_servicios cs ON c.id = cs.id_cita
                LEFT JOIN public.servicios s ON cs.id_servicio = s.id
                WHERE c.fecha_cita::date = :fecha 
                AND c.estado = 'Pendiente'
                GROUP BY c.id, c.fecha_cita, cl.nombre, cl.apellido_1, b.nombre
                ORDER BY c.fecha_cita ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function marcarComoPagada($id_cita) {
    // Cambiamos el estado y el color para el grid
    $sql = "UPDATE public.citas 
            SET estado = 'Pagado', color = 'cita-pagada' 
            WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['id' => $id_cita]);
}
}