<?php

class UsuarioController{

    private $usuario;
    private $cita;

    public function __construct() {
            $this->usuario = new Usuario();
            $this->cita = new Cita();

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

    public function mostrarPanelAdmin($fechaSeleccionada = null) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: index.php?error=Acceso+denegado");
        exit;
    }

    $fecha = $fechaSeleccionada ?? date('Y-m-d');
    $listaBarberos = $this->usuario->listarBarberos();
    $citasBrutas = $this->cita->citasTodosLosBarberosPorFecha($fecha);
    
    // Formateo de fecha para la cabecera (Ej: LUNES, 2 MAY 2026)
    // Formateo de fecha moderno (Sustituye al setlocale y strftime)

    $fechaObjeto = new DateTime($fecha);
    $diasSemana = ['DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
    $meses = [1 => 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

    $nombreDia = $diasSemana[$fechaObjeto->format('w')];
    $diaNum = $fechaObjeto->format('j');
    $nombreMes = $meses[(int)$fechaObjeto->format('n')];
    $anio = $fechaObjeto->format('Y');

    $fechaFormateada = "$nombreDia, $diaNum DE $nombreMes $anio";

    $datos = [
        'totales'        => $this->cita->citasTotalesHoy(),
        'mis_citas'      => $this->cita->citasHoy($_SESSION['user_id']),        
        'barberos'       => array_map(fn($b) => strtoupper($b['nombre']), $listaBarberos),
        'citas_grid'     => $this->formatearCitasParaGrid($citasBrutas, $listaBarberos),
        'fecha_actual'   => $fecha,
        'fecha_texto'    => $fechaFormateada,
        // Añadimos estas dos líneas nuevas:
        'clientes'       => $this->cita->listarClientes(),
        'servicios'      => $this->cita->listarServicios()
    ];

    return $datos;
}

private function formatearCitasParaGrid($citas, $listaBarberos) {
    $formateadas = [];
    $dictBarberos = [];
    
    // Mapeo dinámico: ID del barbero -> Posición en el grid (Columna)
    // El Administrador suele ser la columna 2, Luis la 3, etc.
    foreach($listaBarberos as $index => $b) { 
        $dictBarberos[$b['id']] = $index + 2; 
    }

    foreach ($citas as $c) {
        $timestampInicio = strtotime($c['fecha_cita']);
        $duracionMinutos = (int)$c['duracion_total'];
        $timestampFin = $timestampInicio + ($duracionMinutos * 60);
        
        // Cálculo de fila (09:00 = fila 2, cada 30min = +1 fila)
        $hora = (int)date('H', $timestampInicio);
        $minutos = (int)date('i', $timestampInicio);
        $filaInicio = (($hora - 9) * 2) + ($minutos >= 30 ? 1 : 0) + 2;

        // Lógica de colores según tus clases CSS
        $color = 'cita-verde'; // Por defecto
        if ($duracionMinutos > 30 && $duracionMinutos < 60) $color = 'cita-naranja';
        if ($duracionMinutos >= 60) $color = 'cita-rojo-suave';

        $formateadas[] = [
            'columna'     => $dictBarberos[$c['id_usuario']] ?? 2,
            'fila'        => $filaInicio,
            'duracion'    => ceil($duracionMinutos / 30),
            'color_clase' => $color,
            'cliente'     => strtoupper($c['cliente_nombre']),
            'servicio'    => $c['servicios_nombres'],
            'hora_inicio' => date('H:i', $timestampInicio),
            'hora_fin'    => date('H:i', $timestampFin)
        ];
    }
    return $formateadas;
}
// Algoritmo para buscar el primer hueco libre de hoy
    public function apiProximaCita() {
        header('Content-Type: application/json');
        
        // FORZAMOS LA HORA DE ESPAÑA
        date_default_timezone_set('Europe/Madrid');
        
        $fechaHoy = date('Y-m-d');
        $timestampActual = time();
        $minutosActuales = (int)date('i', $timestampActual);
        
        if ($minutosActuales > 0 && $minutosActuales <= 30) {
            $horaInicioBusqueda = date('H:30', $timestampActual);
        } else {
            $horaInicioBusqueda = date('H:00', strtotime('+1 hour', $timestampActual));
        }

        $listaBarberos = $this->usuario->listarBarberos();
        
        // (Línea de 'shuffle($listaBarberos);' eliminada)
        
        $citasHoy = $this->cita->citasTodosLosBarberosPorFecha($fechaHoy);

        $horas = [];
        for ($h = 9; $h <= 20; $h++) {
            $horas[] = sprintf("%02d:00", $h);
            $horas[] = sprintf("%02d:30", $h);
        }

        foreach ($horas as $hora) {
            if ($hora >= $horaInicioBusqueda) {
                foreach ($listaBarberos as $barbero) {
                    $ocupado = false;
                    foreach ($citasHoy as $cita) {
                        $horaCita = date('H:i', strtotime($cita['fecha_cita']));
                        if ($cita['id_usuario'] == $barbero['id'] && $horaCita == $hora) {
                            $ocupado = true;
                            break;
                        }
                    }
                    if (!$ocupado) {
                        echo json_encode([
                            'encontrado' => true, 
                            'id_barbero' => $barbero['id'],
                            'barbero' => strtoupper($barbero['nombre']), 
                            'hora' => $hora
                        ]);
                        return;
                    }
                }
            }
        }
        echo json_encode(['encontrado' => false]);
    }
// Procesa el formulario de nueva cita y recarga el calendario
    public function procesarNuevaCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $id_barbero = $_POST['id_barbero'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $fecha = $_POST['fecha_cita'] ?? null;
            $hora = $_POST['hora_cita'] ?? null;
            $servicios = $_POST['servicios'] ?? []; // Recoge el array de checkboxes

            if ($id_barbero && $id_cliente && $fecha && $hora) {
                // Unimos la fecha y la hora para PostgreSQL (Ej: 2026-05-05 17:30:00)
                $fecha_hora_exacta = $fecha . ' ' . $hora . ':00';
                
                $this->cita->agendarNuevaCita($id_barbero, $id_cliente, $fecha_hora_exacta, $servicios);
            }
            
            // Redirigimos de vuelta al panel de admin, manteniendo el mismo día
            header("Location: index.php?accion=admin&fecha=" . $fecha);
            exit;
        }
    }
}
