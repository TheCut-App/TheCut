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
    $listaBarberos = $this->usuario->listarBarberosPorFecha($fecha);
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
        'totales'        => $this->cita->citasTotalesHoy($fecha),
        'mis_citas'      => $this->cita->citasHoy($_SESSION['user_id'], $fecha),        
        'barberos'       => array_map(fn($b) => strtoupper($b['nombre']), $listaBarberos),
        'barberos_datos' => $listaBarberos, 
        'citas_grid'     => $this->formatearCitasParaGrid($citasBrutas, $listaBarberos),
        'fecha_actual'   => $fecha,
        'fecha_texto'    => $fechaFormateada,
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
       // Dentro del foreach de formatearCitasParaGrid:
        $color = 'cita-verde'; 

        if ($c['estado'] === 'Pagado') {
            $color = 'cita-pagada'; // Clase CSS para ponerlo en gris
        } else {
            if ($duracionMinutos > 30 && $duracionMinutos < 60) $color = 'cita-naranja';
            if ($duracionMinutos >= 60) $color = 'cita-rojo-suave';
        }

        $formateadas[] = [
            'id'          => $c['id'], 
            'estado'      => $c['estado'],
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

        $listaBarberos = $this->usuario->listarBarberosPorFecha($fechaHoy);        
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
    $inicioTimestamp = strtotime($cita['fecha_cita']);
    $duracionMinutos = (int)$cita['duracion_total'];
    
    // Calculamos el timestamp de cuando termina la cita
    $finTimestamp = $inicioTimestamp + ($duracionMinutos * 60);
    
    // Convertimos la hora que estamos comprobando en el bucle principal a timestamp para comparar
    $horaActualBucleTimestamp = strtotime($fechaHoy . ' ' . $hora);

    if ($cita['id_usuario'] == $barbero['id']) {
        // La hora está ocupada si:
        // Es igual al inicio O está entre el inicio y el fin
        if ($horaActualBucleTimestamp >= $inicioTimestamp && $horaActualBucleTimestamp < $finTimestamp) {
            $ocupado = true;
            break;
        }
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

    public function apiHuecosDisponibles() {
    header('Content-Type: application/json');
    $id_barbero = $_GET['id_barbero'];
    $fecha = $_GET['fecha'];
    $duracionNecesaria = (int)$_GET['duracion'];

    $citas = $this->cita->citasTodosLosBarberosPorFecha($fecha);
    // Filtrar solo las de este barbero
    $ocupadas = array_filter($citas, fn($c) => $c['id_usuario'] == $id_barbero);

    $disponibles = [];
    // Generamos todas las medias horas de 09:00 a 20:30
    for ($h = 9; $h <= 20; $h++) {
        foreach (["00", "30"] as $m) {
            $horaEvaluar = sprintf("%02d:%s", $h, $m);
            $timestampEvaluar = strtotime("$fecha $horaEvaluar");
            $timestampFinNecesario = $timestampEvaluar + ($duracionNecesaria * 60);

            // COMPROBACIÓN CLAVE: ¿Este bloque choca con alguna cita?
            $libre = true;
            foreach ($ocupadas as $cita) {
                $inicioCita = strtotime($cita['fecha_cita']);
                $finCita = $inicioCita + ($cita['duracion_total'] * 60);

                // Hay solape si el bloque nuevo empieza antes de que termine una cita 
                // Y termina después de que empiece esa misma cita
                if ($timestampEvaluar < $finCita && $timestampFinNecesario > $inicioCita) {
                    $libre = false;
                    break;
                }
            }
            
            if ($libre && $timestampFinNecesario <= strtotime("$fecha 21:00")) {
                $disponibles[] = $horaEvaluar;
            }
        }
    }
    echo json_encode($disponibles);
    exit;
}

    public function guardarCita() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_cliente = $_POST['id_cliente'] ?? null;
                // Como ya nos llega el ID real, no hace falta buscar su posición
                $id_barbero_real = $_POST['id_usuario'] ?? null; 
                $fecha_completa = $_POST['fecha_cita'] ?? null;
                $servicios = $_POST['servicios'] ?? [];

                if ($id_barbero_real && $id_cliente && $fecha_completa) {
                    // Usamos la función de transacción de Cita.php para guardar todo de golpe
                    $exito = $this->cita->agendarNuevaCita($id_barbero_real, $id_cliente, $fecha_completa, $servicios);
                    
                    if ($exito) {
                        echo json_encode(['success' => true]);
                        exit;
                    }
                }
                
                // Si algo falla o faltan datos
                echo json_encode(['success' => false, 'error' => 'Error al guardar la cita en la base de datos.']);
            }
            exit;
        }
        public function mostrarPanelVentas() {
            $fechaHoy = date('Y-m-d');
            
            $datos = [
                'fecha_actual' => $fechaHoy,
                'citas_pendientes' => $this->cita->obtenerCitasPendientes($fechaHoy),
                // AÑADIMOS ESTA LÍNEA PARA CARGAR EL CATÁLOGO:
                'servicios' => $this->cita->listarServicios() 
            ];

            return $datos;
        }
        public function procesarCobro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cita = $_POST['id_cita'] ?? null;
            $metodo = $_POST['metodo_pago'] ?? 'Efectivo';
            
            if ($id_cita) {
                $this->cita->marcarComoPagada($id_cita);
                // Aquí podrías insertar en una tabla de 'ventas' si quisieras guardar el método de pago
            }
            
            header("Location: index.php?accion=venta");
            exit;
        }
    }
    public function mostrarEditarCita($id_cita) {
        $detalle = $this->cita->obtenerDetalleCita($id_cita);
        
        if (!$detalle) {
            header("Location: index.php?accion=admin");
            exit;
        }

        $datos = [
            'cita'      => $detalle,
            'barberos'  => $this->usuario->listarBarberos(),
            'servicios' => $this->cita->listarServicios() // Por si quiere añadir nuevos
        ];

        require_once 'app/views/Editar_Cita.php';
    }
    // Procesa el formulario de edición de cita
    public function actualizarCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cita = $_POST['id_cita'] ?? null;
            $id_usuario = $_POST['id_usuario'] ?? null;
            $fecha = $_POST['fecha'] ?? null;
            $hora = $_POST['hora'] ?? null;
            $notas = $_POST['notas'] ?? '';
            $servicios = $_POST['servicios'] ?? [];

            if ($id_cita && $id_usuario && $fecha && $hora) {
                $fecha_hora_exacta = $fecha . ' ' . $hora . ':00';
                $this->cita->actualizarCitaCompleta($id_cita, $id_usuario, $fecha_hora_exacta, $notas, $servicios);
            }
            
            // Volvemos al panel manteniendo la fecha que hemos editado
            header("Location: index.php?accion=admin&fecha=" . $fecha);
            exit;
        }
    }

    public function mostrarFormularioNuevoCliente() {
        require_once 'app/views/Nuevo_Cliente.php';
    }

    public function procesarNuevoCliente() {
        $esPeticionValida = $_SERVER['REQUEST_METHOD'] === 'POST';
        
        if ($esPeticionValida) {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            $datosRequeridosCompletos = !empty($nombre) && !empty($telefono);

            if ($datosRequeridosCompletos) {
                $this->cita->registrarClienteBasico($nombre, $apellido, $telefono);
            }
            
            header("Location: index.php?accion=nueva_cita");
            exit;
        }
    }

    public function mostrarGestionEquipo() {
        $datos = [
            'empleados' => $this->usuario->obtenerEstadisticasEquipo()
        ];
        
        require_once 'app/views/Gestion_Equipo.php';
    }

    public function mostrarNuevoEmpleado() {
        require_once 'app/views/Nuevo_Empleado.php';
    }

    public function guardarNuevoEmpleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($nombre) && !empty($username) && !empty($password)) {
                $this->usuario->crearUsuario($username, $password, $nombre, $apellido);
            }
            
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
    }

    public function mostrarEditarEmpleado($id) {
        $empleado = $this->usuario->obtenerUsuarioPorId($id);
        if (!$empleado) {
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
        require_once 'app/views/Editar_Empleado.php';
    }

    public function actualizarEmpleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_usuario'];
            $nombre = $_POST['nombre'];
            $apellido_1 = $_POST['apellido_1'];
            $apellido_2 = $_POST['apellido_2'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $is_admin = isset($_POST['is_admin']);
            $is_active = isset($_POST['is_active']);

            $this->usuario->actualizarUsuarioCompleto($id, $nombre, $apellido_1, $apellido_2, $username, $password, $is_admin, $is_active);
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
    }

    public function mostrarHorarioEmpleado($id) {
        $empleado = $this->usuario->obtenerUsuarioPorId($id);
        if (!$empleado) {
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
        
        // Calcular la semana seleccionada (por defecto la actual)
        $fecha_ref = $_GET['semana'] ?? date('Y-m-d');
        $dt = new DateTime($fecha_ref);
        
        // Ajustar al Lunes de esa semana
        $diaSemana = $dt->format('N'); // 1 (Lunes) a 7 (Domingo)
        $dt->modify('-' . ($diaSemana - 1) . ' days');
        $lunes = $dt->format('Y-m-d');
        
        // Calcular el Domingo
        $domingo = (clone $dt)->modify('+6 days')->format('Y-m-d');
        
        // Navegación (Semanas anterior y siguiente)
        $semanaAnterior = (clone $dt)->modify('-7 days')->format('Y-m-d');
        $semanaSiguiente = (clone $dt)->modify('+7 days')->format('Y-m-d');

        // Obtener los días que trabaja esa semana concreta
        $fechasTrabajo = $this->usuario->obtenerFechasLaborables($id, $lunes, $domingo);
        
        require_once 'app/views/Horario_Empleado.php';
    }

    public function guardarHorarioEmpleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? null;
            $fechas = $_POST['fechas'] ?? []; 
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;

            if ($id_usuario && $fecha_inicio && $fecha_fin) {
                $this->usuario->actualizarFechasLaborables($id_usuario, $fechas, $fecha_inicio, $fecha_fin);
            }
            
            // Volvemos a la misma semana
            header("Location: index.php?accion=horario_empleado&id=" . $id_usuario . "&semana=" . $fecha_inicio . "&msg=ok");
            exit;
        }
    }

    public function mostrarEditarCliente($id) {
        $cliente = $this->cita->obtenerClientePorId($id);
        if (!$cliente) {
            header("Location: index.php?accion=gestion_clientes");
            exit;
        }
        require_once 'app/views/Editar_Cliente.php';
    }

    public function guardarEdicionCliente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $notas = trim($_POST['notas'] ?? '');

            if ($id && $nombre && $telefono) {
                $this->cita->actualizarCliente($id, $nombre, $apellido, $telefono, $notas);
            }
            header("Location: index.php?accion=gestion_clientes");
            exit;
        }
    }
    public function mostrarGestionClientes() {
        // Ya no necesitamos calcular páginas, el AJAX se encarga de todo.
        require_once 'app/views/Gestion_Clientes.php';
    }

    public function apiBuscarClientes() {
        $busqueda = $_GET['s'] ?? '';
        $paginaActual = (int)($_GET['p'] ?? 1);
        if ($paginaActual < 1) $paginaActual = 1;
        $porPagina = 10;
        $offset = ($paginaActual - 1) * $porPagina;

        // Obtenemos clientes y calculamos totales
        $clientes = $this->cita->obtenerEstadisticasClientes($busqueda, $porPagina, $offset);
        $totalClientes = $this->cita->contarTotalClientes($busqueda);
        $totalPaginas = ceil($totalClientes / $porPagina) ?: 1; // Mínimo 1 página
        
        $html = "";

        // Dibujamos las filas
        foreach ($clientes as $c) {
            $gastado = number_format($c['total_gastado'], 2) . " €";
            $iniciales = strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellido_1'], 0, 1));
            $visita = $c['ultima_visita'] ? date('d M Y', strtotime($c['ultima_visita'])) : 'Nunca';
            $clase = ($c['total_gastado'] >= 300) ? 'badge-vip' : (($c['total_gastado'] > 0) ? 'badge-regular' : 'badge-nuevo');
            $texto = ($c['total_gastado'] >= 300) ? 'VIP' : (($c['total_gastado'] > 0) ? 'REGULAR' : 'NUEVO');

            $html .= "
            <div class='fila-cliente'>
                <div class='celda-perfil'>
                    <div class='avatar-circulo'>$iniciales</div>
                    <span class='nombre-cliente'>{$c['nombre']} {$c['apellido_1']}</span>
                </div>
                <div style='color: #bbb;'>{$c['telefono']}</div>
                <div style='color: #bbb;'>$visita</div>
                <div style='font-weight: bold; color: var(--dorado);'>$gastado</div>
                <div style='text-align: center;'><span class='$clase'>$texto</span></div>
                <div class='celda-acciones'>
                    <button class='btn-accion'>Historial</button>
                    <button class='btn-accion' onclick=\"window.location.href='index.php?accion=editar_cliente&id={$c['id']}'\">Editar</button>
                </div>
            </div>";
        }

        if (empty($clientes)) {
            $html .= "<div style='text-align:center; padding:20px; color:#888;'>No se encontraron clientes.</div>";
        }

        // Dibujamos la barra de Paginación y Contador
        $html .= "<div style='display: flex; justify-content: center; align-items: center; gap: 20px; margin-top: 20px; border-top: 1px solid #333; padding-top: 20px;'>";
        
        if ($paginaActual > 1) {
            $prev = $paginaActual - 1;
            // Fíjate que el botón llama a la función JS buscar()
            $html .= "<button onclick='buscar($prev)' class='btn-accion' style='padding: 8px 15px;'>Anterior</button>";
        }

        $html .= "<span style='color: #888; font-size: 0.9rem;'>Página $paginaActual de $totalPaginas ($totalClientes clientes)</span>";

        if ($paginaActual < $totalPaginas) {
            $next = $paginaActual + 1;
            $html .= "<button onclick='buscar($next)' class='btn-accion' style='padding: 8px 15px;'>Siguiente</button>";
        }

        $html .= "</div>";

        echo $html;
        exit;
    }
}
