<?php

class Usuario {

    private $databaseConnection;

    public function __construct() {
        $this->databaseConnection = Database::getConnection();
    }

    public function crearUsuario(string $nombreUsuario, string $contrasena, string $nombre, string $apellido1, ?string $apellido2 = null, bool $esAdmin = false, ?string $urlFoto = null): bool {
        $sqlInsertar = "INSERT INTO usuarios (
                            username, password, nombre, apellido_1, apellido_2, 
                            is_admin, is_active, url_foto, fecha_alta, rol
                        ) VALUES (
                            :username, :password, :nombre, :apellido_1, :apellido_2, 
                            :is_admin, true, :url_foto, CURRENT_DATE, 'barbero'
                        )";
        
        $statement = $this->databaseConnection->prepare($sqlInsertar);
        
        return $statement->execute([
            'username'   => $nombreUsuario,
            'password'   => $contrasena,
            'nombre'     => $nombre,
            'apellido_1' => $apellido1,
            'apellido_2' => $apellido2,
            'is_admin'   => $esAdmin ? 1 : 0, 
            'url_foto'   => $urlFoto
        ]);
    }

    public function actualizarUsuario(int $idUsuario, string $nombre, string $apellido1, ?string $apellido2 = null, bool $esAdmin = false, ?string $urlFoto = null): bool {
        $sqlActualizar = "UPDATE usuarios SET 
                            nombre = :nombre, 
                            apellido_1 = :apellido_1, 
                            apellido_2 = :apellido_2, 
                            is_admin = :is_admin, 
                            url_foto = :url_foto
                        WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlActualizar);
        
        return $statement->execute([
            'id'         => $idUsuario,
            'nombre'     => $nombre,
            'apellido_1' => $apellido1,
            'apellido_2' => $apellido2,
            'is_admin'   => $esAdmin ? 1 : 0,
            'url_foto'   => $urlFoto
        ]);
    }

    public function buscarPorUsername(string $nombreUsuario) {
        $sqlConsulta = "SELECT * FROM usuarios WHERE username = :username LIMIT 1";

        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute(['username' => $nombreUsuario]);
        
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function esAdmin(int $idUsuario): bool {
        $sqlConsulta = "SELECT is_admin FROM usuarios WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute(['id' => $idUsuario]);
        $resultadoConsulta = $statement->fetch(PDO::FETCH_ASSOC);

        return $resultadoConsulta && $resultadoConsulta['is_admin'] == true;
    }

    public function esActivo(int $idUsuario): bool {
        $sqlConsulta = "SELECT is_active FROM usuarios WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute(['id' => $idUsuario]);
        $resultadoConsulta = $statement->fetch(PDO::FETCH_ASSOC);

        return $resultadoConsulta && $resultadoConsulta['is_active'] == true;
    }

    public function toggleActivo(int $idUsuario): bool {
        $sqlActualizar = "UPDATE usuarios SET is_active = NOT is_active WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlActualizar);
        
        return $statement->execute(['id' => $idUsuario]);
    }

    public function listarBarberos(): array {
        $sqlConsulta = "SELECT id, username, nombre 
                        FROM usuarios 
                        WHERE rol = 'barbero' AND is_active = true 
                        ORDER BY nombre ASC";
                        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarBarberosPorFecha(string $fechaConsulta): array {
        $sqlConsulta = "SELECT u.id, u.username, u.nombre 
                        FROM usuarios u
                        INNER JOIN horarios h ON u.id = h.id_usuario
                        WHERE u.rol = 'barbero' 
                          AND u.is_active = true 
                          AND h.fecha = :fecha
                        ORDER BY u.nombre ASC";
                        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute(['fecha' => $fechaConsulta]);
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstadisticasEquipo(): array {
        $inicioMes = date('Y-m-01 00:00:00');
        $finMes = date('Y-m-t 23:59:59');

        $sqlConsulta = "SELECT 
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

        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute(['inicio' => $inicioMes, 'fin' => $finMes]);
        $listaEmpleados = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($listaEmpleados as &$empleadoActual) {
            $empleadoActual['total_productos'] = 0; 
            $empleadoActual['total_mes'] = $empleadoActual['total_servicios'] + $empleadoActual['total_productos'];
        }

        return $listaEmpleados;
    }

    public function obtenerUsuarioPorId(int $idUsuario) {
        $sqlConsulta = "SELECT * FROM usuarios WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute(['id' => $idUsuario]);
        
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuarioCompleto(int $idUsuario, string $nombre, string $apellido1, ?string $apellido2, string $nombreUsuario, string $contrasena, bool $esAdmin, bool $estaActivo): bool {
        $sqlActualizar = "UPDATE usuarios SET 
                            nombre = :nombre, 
                            apellido_1 = :apellido_1, 
                            apellido_2 = :apellido_2, 
                            username = :username, 
                            password = :password, 
                            is_admin = :is_admin, 
                            is_active = :is_active 
                        WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlActualizar);
        
        return $statement->execute([
            'id'         => $idUsuario,
            'nombre'     => $nombre,
            'apellido_1' => $apellido1,
            'apellido_2' => $apellido2,
            'username'   => $nombreUsuario,
            'password'   => $contrasena,
            'is_admin'   => $esAdmin ? 1 : 0,
            'is_active'  => $estaActivo ? 1 : 0
        ]);
    }

    public function obtenerFechasLaborables(int $idUsuario, string $fechaInicio, string $fechaFin): array {
        $sqlConsulta = "SELECT fecha FROM horarios WHERE id_usuario = :id AND fecha BETWEEN :inicio AND :fin";
        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute([
            'id'     => $idUsuario, 
            'inicio' => $fechaInicio, 
            'fin'    => $fechaFin
        ]);
        
        $listaFechas = $statement->fetchAll(PDO::FETCH_COLUMN); 
        
        return array_map(function($fechaActual) {
            return date('Y-m-d', strtotime($fechaActual));
        }, $listaFechas);
    }

    public function actualizarFechasLaborables(int $idUsuario, array $fechasActivas, string $fechaInicio, string $fechaFin): bool {
        try {
            $statementEliminar = $this->databaseConnection->prepare("DELETE FROM horarios WHERE id_usuario = :id AND fecha BETWEEN :inicio AND :fin");
            $statementEliminar->execute([
                'id'     => $idUsuario, 
                'inicio' => $fechaInicio, 
                'fin'    => $fechaFin
            ]);

            if (!empty($fechasActivas)) {
                $sqlInsertar = "INSERT INTO horarios (id_usuario, fecha) VALUES (:id, :fecha)";
                $statementInsertar = $this->databaseConnection->prepare($sqlInsertar);
                
                foreach ($fechasActivas as $fechaActual) {
                    $statementInsertar->execute([
                        'id'    => $idUsuario, 
                        'fecha' => $fechaActual
                    ]);
                }
            }
            return true;
        } catch (PDOException $errorTransaccion) {
            die("Error crítico de base de datos: " . $errorTransaccion->getMessage());
        }
    }

    public function obtenerHorariosGlobales(string $fechaInicio, string $fechaFin): array {
        $sqlConsulta = "SELECT u.id as id_usuario, u.nombre, u.url_foto, h.fecha 
                        FROM usuarios u 
                        LEFT JOIN horarios h ON u.id = h.id_usuario AND h.fecha BETWEEN :inicio AND :fin 
                        WHERE u.is_active = true 
                        ORDER BY u.id, h.fecha";
        
        $statement = $this->databaseConnection->prepare($sqlConsulta);
        $statement->execute([
            'inicio' => $fechaInicio, 
            'fin'    => $fechaFin
        ]);
        
        $resultadosConsulta = $statement->fetchAll(PDO::FETCH_ASSOC);
        $matrizHorarios = [];
        
        foreach ($resultadosConsulta as $filaActual) {
            $idEmpleado = $filaActual['id_usuario'];
            
            if (!isset($matrizHorarios[$idEmpleado])) {
                $matrizHorarios[$idEmpleado] = [
                    'id'       => $idEmpleado,
                    'nombre'   => $filaActual['nombre'],
                    'url_foto' => $filaActual['url_foto'],
                    'fechas'   => []
                ];
            }
            
            if ($filaActual['fecha']) {
                $matrizHorarios[$idEmpleado]['fechas'][] = date('Y-m-d', strtotime($filaActual['fecha']));
            }
        }
        
        return $matrizHorarios;
    }
}
?>