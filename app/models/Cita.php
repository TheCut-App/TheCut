<?php

class Cita {
    
    private $dbConnection;

    public function __construct() {
        $this->dbConnection = Database::getConnection();
    }

    public function citasHoy($idUsuario, $fechaActual) {
        $sqlConsulta = "SELECT COUNT(*) as total 
                        FROM citas 
                        WHERE DATE(fecha_cita) = :fecha 
                        AND id_usuario = :id";
        
        $statement = $this->dbConnection->prepare($sqlConsulta);
        $statement->execute([
            'id'    => $idUsuario, 
            'fecha' => $fechaActual
        ]);
        
        $resultado = $statement->fetch();
        return $resultado['total'] ?? 0;
    }

    public function citasTotalesHoy($fechaActual) {
        $sqlConsulta = "SELECT COUNT(*) as total 
                        FROM citas 
                        WHERE DATE(fecha_cita) = :fecha";
        
        $statement = $this->dbConnection->prepare($sqlConsulta);
        $statement->execute(['fecha' => $fechaActual]);
        
        $resultado = $statement->fetch();
        return $resultado['total'] ?? 0;
    }

    public function citasTodosLosBarberosPorFecha($fechaActual) {
        $sqlConsulta = "SELECT 
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

        $statement = $this->dbConnection->prepare($sqlConsulta);
        $statement->execute(['fecha' => $fechaActual]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarClientes() {
        $sqlConsulta = "SELECT id, nombre, apellido_1, telefono 
                        FROM clientes 
                        ORDER BY nombre ASC";
        
        $statement = $this->dbConnection->query($sqlConsulta);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarServicios() {
        $sqlConsulta = "SELECT id, nombre, precio, duracion 
                        FROM servicios 
                        WHERE is_active = true 
                        ORDER BY nombre ASC";
        
        $statement = $this->dbConnection->query($sqlConsulta);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agendarNuevaCita($idUsuario, $idCliente, $fechaHoraCompleta, $listaServicios) {
        try {
            $this->dbConnection->beginTransaction();

            $sqlInsertarCita = "INSERT INTO public.citas (id_usuario, id_cliente, fecha_cita, estado, color) 
                                VALUES (:id_usr, :id_cli, :fecha, 'Pendiente', 'cita-verde') RETURNING id";
            
            $statementCita = $this->dbConnection->prepare($sqlInsertarCita);
            $statementCita->execute([
                'id_usr' => $idUsuario,
                'id_cli' => $idCliente,
                'fecha'  => $fechaHoraCompleta
            ]);
            
            $resultadoInsercion = $statementCita->fetch(PDO::FETCH_ASSOC);
            $idCitaRecienCreada = $resultadoInsercion['id'];

            if (!empty($listaServicios)) {
                $sqlInsertarServicio = "INSERT INTO public.citas_servicios (id_cita, id_servicio) 
                                        VALUES (:id_cita, :id_servicio)";
                $statementServicio = $this->dbConnection->prepare($sqlInsertarServicio);
                
                foreach ($listaServicios as $idServicioActual) {
                    $statementServicio->execute([
                        'id_cita'     => $idCitaRecienCreada,
                        'id_servicio' => $idServicioActual
                    ]);
                }
            }

            $this->dbConnection->commit();
            return true;
            
        } catch (Exception $errorTransaccion) {
            $this->dbConnection->rollBack();
            return false;
        }
    }

    public function obtenerCitasPendientes($fechaActual) {
        $sqlConsulta = "SELECT 
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

        $statement = $this->dbConnection->prepare($sqlConsulta);
        $statement->execute(['fecha' => $fechaActual]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoPagada($idCita) {
        $sqlActualizacion = "UPDATE public.citas 
                             SET estado = 'Pagado', color = 'cita-pagada' 
                             WHERE id = :id";
                             
        $statement = $this->dbConnection->prepare($sqlActualizacion);
        return $statement->execute(['id' => $idCita]);
    }

    public function confirmarCita($idCita) {
        $sqlActualizacion = "UPDATE public.citas 
                             SET estado = 'Completada', color = 'cita-verde' 
                             WHERE id = :id";
                             
        $statement = $this->dbConnection->prepare($sqlActualizacion);
        return $statement->execute(['id' => $idCita]);
    }

    public function eliminarCita($idCita) {
        try {
            $this->dbConnection->beginTransaction();
            
            $statementServicios = $this->dbConnection->prepare("DELETE FROM public.citas_servicios WHERE id_cita = :id");
            $statementServicios->execute(['id' => $idCita]);
            
            $statementCita = $this->dbConnection->prepare("DELETE FROM public.citas WHERE id = :id");
            $statementCita->execute(['id' => $idCita]);
            
            $this->dbConnection->commit();
            return true;
            
        } catch (Exception $errorTransaccion) {
            $this->dbConnection->rollBack();
            return false;
        }
    }

    public function obtenerDetalleCita($idCita) {
        $sqlConsultaCita = "SELECT 
                                c.*, 
                                cl.nombre AS cliente_nombre, 
                                cl.apellido_1 AS cliente_apellido, 
                                cl.telefono AS cliente_tlf,
                                COALESCE(SUM(s.duracion), 0) AS duracion_total
                            FROM public.citas c
                            JOIN public.clientes cl ON c.id_cliente = cl.id
                            LEFT JOIN public.citas_servicios cs ON c.id = cs.id_cita
                            LEFT JOIN public.servicios s ON cs.id_servicio = s.id
                            WHERE c.id = :id
                            GROUP BY c.id, cl.id";
        
        $statementCita = $this->dbConnection->prepare($sqlConsultaCita);
        $statementCita->execute(['id' => $idCita]);
        $detalleCita = $statementCita->fetch(PDO::FETCH_ASSOC);

        if ($detalleCita) {
            $sqlConsultaServicios = "SELECT s.* FROM servicios s 
                                     JOIN citas_servicios cs ON s.id = cs.id_servicio 
                                     WHERE cs.id_cita = :id";
                                     
            $statementServicios = $this->dbConnection->prepare($sqlConsultaServicios);
            $statementServicios->execute(['id' => $idCita]);
            $detalleCita['servicios_contratados'] = $statementServicios->fetchAll(PDO::FETCH_ASSOC);
        }

        return $detalleCita;
    }

    public function actualizarCitaCompleta($idCita, $idBarbero, $fechaHoraCompleta, $notasAdicionales, $listaServicios) {
        try {
            $this->dbConnection->beginTransaction();

            $sqlActualizarCita = "UPDATE public.citas 
                                  SET id_usuario = :id_usuario, fecha_cita = :fecha, notas = :notas 
                                  WHERE id = :id";
                                  
            $statementCita = $this->dbConnection->prepare($sqlActualizarCita);
            $statementCita->execute([
                'id_usuario' => $idBarbero,
                'fecha'      => $fechaHoraCompleta,
                'notas'      => $notasAdicionales,
                'id'         => $idCita
            ]);

            $statementEliminarServicios = $this->dbConnection->prepare("DELETE FROM public.citas_servicios WHERE id_cita = :id");
            $statementEliminarServicios->execute(['id' => $idCita]);

            if (!empty($listaServicios)) {
                $sqlInsertarServicio = "INSERT INTO public.citas_servicios (id_cita, id_servicio) 
                                        VALUES (:id_cita, :id_servicio)";
                $statementNuevosServicios = $this->dbConnection->prepare($sqlInsertarServicio);
                
                foreach ($listaServicios as $idServicioActual) {
                    $statementNuevosServicios->execute([
                        'id_cita'     => $idCita,
                        'id_servicio' => $idServicioActual
                    ]);
                }
            }

            $this->dbConnection->commit();
            return true;
            
        } catch (Exception $errorTransaccion) {
            $this->dbConnection->rollBack();
            return false;
        }
    }

    public function registrarClienteBasico($nombreCliente, $apellidoCliente, $telefonoCliente) {
        $sqlInsercion = "INSERT INTO public.clientes (nombre, apellido_1, telefono, fecha_alta) 
                         VALUES (:nombre, :apellido, :telefono, CURRENT_DATE)";
                
        $statement = $this->dbConnection->prepare($sqlInsercion);
        return $statement->execute([
            'nombre'   => $nombreCliente,
            'apellido' => $apellidoCliente,
            'telefono' => $telefonoCliente
        ]);
    }

    public function obtenerEstadisticasClientes($terminoBusqueda = '', $limiteResultados = 10, $desplazamientoOffset = 0) {
        $condicionFiltro = "";
        
        if (!empty($terminoBusqueda)) {
            $condicionFiltro = "WHERE c.nombre ILIKE :termino 
                                OR c.apellido_1 ILIKE :termino 
                                OR c.telefono ILIKE :termino";
        }

        $sqlConsulta = "SELECT 
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
                        $condicionFiltro
                        GROUP BY c.id, c.nombre, c.apellido_1, c.telefono
                        ORDER BY total_gastado DESC
                        LIMIT :limite OFFSET :desplazamiento";
                
        $statement = $this->dbConnection->prepare($sqlConsulta);
        
        if (!empty($terminoBusqueda)) {
            $statement->bindValue(':termino', "%$terminoBusqueda%");
        }
        
        $statement->bindValue(':limite', (int)$limiteResultados, PDO::PARAM_INT);
        $statement->bindValue(':desplazamiento', (int)$desplazamientoOffset, PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarTotalClientes($terminoBusqueda = '') {
        $condicionFiltro = !empty($terminoBusqueda) ? "WHERE nombre ILIKE :termino OR apellido_1 ILIKE :termino OR telefono ILIKE :termino" : "";
        $sqlConsulta = "SELECT COUNT(*) FROM clientes $condicionFiltro";
        
        $statement = $this->dbConnection->prepare($sqlConsulta);
        
        if (!empty($terminoBusqueda)) {
            $statement->bindValue(':termino', "%$terminoBusqueda%");
        }
        
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function obtenerClientePorId($idCliente) {
        $sqlConsulta = "SELECT * FROM clientes WHERE id = :id";
        $statement = $this->dbConnection->prepare($sqlConsulta);
        $statement->execute(['id' => $idCliente]);
        
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarCliente($idCliente, $nombreCliente, $apellidoCliente, $telefonoCliente, $notasAdicionales) {
        $sqlActualizacion = "UPDATE clientes 
                             SET nombre = :nombre, 
                                 apellido_1 = :apellido_1, 
                                 telefono = :telefono, 
                                 notas = :notas 
                             WHERE id = :id";
                             
        $statement = $this->dbConnection->prepare($sqlActualizacion);
        return $statement->execute([
            'id'         => $idCliente, 
            'nombre'     => $nombreCliente, 
            'apellido_1' => $apellidoCliente, 
            'telefono'   => $telefonoCliente, 
            'notas'      => $notasAdicionales
        ]);
    }

    public function obtenerHistorialCliente($idCliente) {
        $sqlConsulta = "SELECT 
                            c.fecha_cita, 
                            b.nombre AS barbero,
                            COALESCE(STRING_AGG(s.nombre, ' + '), 'Sin servicio') AS servicios,
                            COALESCE(SUM(s.precio), 0) AS total
                        FROM citas c
                        JOIN usuarios b ON c.id_usuario = b.id
                        LEFT JOIN citas_servicios cs ON c.id = cs.id_cita
                        LEFT JOIN servicios s ON cs.id_servicio = s.id
                        WHERE c.id_cliente = :id AND c.estado IN ('Pagado', 'Completada')
                        GROUP BY c.id, c.fecha_cita, b.nombre
                        ORDER BY c.fecha_cita DESC";
                
        $statement = $this->dbConnection->prepare($sqlConsulta);
        $statement->execute(['id' => $idCliente]);
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>