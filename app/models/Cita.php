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
    // Confirma la cita (puedes cambiar el color para que se vea distinta en el grid)
    public function confirmarCita($id_cita) {
        $sql = "UPDATE public.citas SET estado = 'Completada', color = 'cita-verde' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id_cita]);
    }

    // Elimina la cita liberando el hueco
    public function eliminarCita($id_cita) {
        try {
            $this->db->beginTransaction();
            // Primero borramos los servicios vinculados para no tener error de claves foráneas
            $stmt1 = $this->db->prepare("DELETE FROM public.citas_servicios WHERE id_cita = :id");
            $stmt1->execute(['id' => $id_cita]);
            
            // Luego borramos la cita
            $stmt2 = $this->db->prepare("DELETE FROM public.citas WHERE id = :id");
            $stmt2->execute(['id' => $id_cita]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    // Obtiene todos los detalles de una cita específica para editarla
    public function obtenerDetalleCita($id_cita) {
        // Hemos eliminado 'cl.email AS cliente_email' de la consulta
        $sql = "SELECT 
                    c.*, 
                    cl.nombre AS cliente_nombre, cl.apellido_1 AS cliente_apellido, 
                    cl.telefono AS cliente_tlf,
                    COALESCE(SUM(s.duracion), 0) AS duracion_total
                FROM public.citas c
                JOIN public.clientes cl ON c.id_cliente = cl.id
                LEFT JOIN public.citas_servicios cs ON c.id = cs.id_cita
                LEFT JOIN public.servicios s ON cs.id_servicio = s.id
                WHERE c.id = :id
                GROUP BY c.id, cl.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_cita]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cita) {
            $sqlSrv = "SELECT s.* FROM servicios s 
                       JOIN citas_servicios cs ON s.id = cs.id_servicio 
                       WHERE cs.id_cita = :id";
            $stmtSrv = $this->db->prepare($sqlSrv);
            $stmtSrv->execute(['id' => $id_cita]);
            $cita['servicios_contratados'] = $stmtSrv->fetchAll(PDO::FETCH_ASSOC);
        }

        return $cita;
    }
    // Actualiza todos los datos de la cita y sus servicios
    public function actualizarCitaCompleta($id_cita, $id_barbero, $fecha_hora, $notas, $servicios) {
        try {
            $this->db->beginTransaction();

            // 1. Actualizamos los datos principales (no tocamos el estado)
            $sql = "UPDATE public.citas SET id_usuario = :id_usuario, fecha_cita = :fecha, notas = :notas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id_usuario' => $id_barbero,
                'fecha' => $fecha_hora,
                'notas' => $notas,
                'id' => $id_cita
            ]);

            // 2. Borramos los servicios que tuviera antes
            $stmtDel = $this->db->prepare("DELETE FROM public.citas_servicios WHERE id_cita = :id");
            $stmtDel->execute(['id' => $id_cita]);

            // 3. Insertamos los nuevos servicios que haya marcado
            if (!empty($servicios)) {
                $sqlSrv = "INSERT INTO public.citas_servicios (id_cita, id_servicio) VALUES (:id_cita, :id_servicio)";
                $stmtSrv = $this->db->prepare($sqlSrv);
                foreach ($servicios as $id_srv) {
                    $stmtSrv->execute([
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

    public function registrarClienteBasico($nombre, $apellido, $telefono) {
        $sql = "INSERT INTO public.clientes (nombre, apellido_1, telefono, fecha_alta) 
                VALUES (:nombre, :apellido, :telefono, CURRENT_DATE)";
                
        return $this->db->prepare($sql)->execute(compact('nombre', 'apellido', 'telefono'));
    }
    public function obtenerEstadisticasClientes($busqueda = '', $limit = 10, $offset = 0) {
        $filtro = "";
        if (!empty($busqueda)) {
            $filtro = "WHERE c.nombre ILIKE :b OR c.apellido_1 ILIKE :b OR c.telefono ILIKE :b";
        }

        $sql = "SELECT 
                    c.id, c.nombre, c.apellido_1, c.telefono,
                    MAX(ci.fecha_cita) as ultima_visita,
                    COALESCE((
                        SELECT SUM(s.precio)
                        FROM citas c2
                        JOIN citas_servicios cs ON c2.id = cs.id_cita
                        JOIN servicios s ON cs.id_servicio = s.id
                        WHERE c2.id_cliente = c.id AND c2.estado = 'Pagado'
                    ), 0) as total_gastado
                FROM clientes c
                LEFT JOIN citas ci ON c.id = ci.id_cliente AND ci.estado IN ('Pagado', 'Completada')
                $filtro
                GROUP BY c.id, c.nombre, c.apellido_1, c.telefono
                ORDER BY total_gastado DESC
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        if (!empty($busqueda)) $stmt->bindValue(':b', "%$busqueda%");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarTotalClientes($busqueda = '') {
        $filtro = !empty($busqueda) ? "WHERE nombre ILIKE :b OR apellido_1 ILIKE :b OR telefono ILIKE :b" : "";
        $sql = "SELECT COUNT(*) FROM clientes $filtro";
        $stmt = $this->db->prepare($sql);
        if (!empty($busqueda)) $stmt->bindValue(':b', "%$busqueda%");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function obtenerClientePorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarCliente($id, $nombre, $apellido_1, $telefono, $notas) {
        $sql = "UPDATE clientes SET nombre = :nombre, apellido_1 = :apellido_1, telefono = :telefono, notas = :notas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id, 
            'nombre' => $nombre, 
            'apellido_1' => $apellido_1, 
            'telefono' => $telefono, 
            'notas' => $notas
        ]);
    }
}