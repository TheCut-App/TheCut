<?php

class UsuarioController {

    private $usuarioModel;
    private $citaModel;
    private $productoModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->citaModel = new Cita();
        $this->productoModel = new Producto();

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido_1 = $_POST['apellido_1'] ?? '';
            $apellido_2 = $_POST['apellido_2'] ?? null;
            $is_admin = isset($_POST['is_admin']);
            $url_foto = $_POST['url_foto'] ?? null;

            if ($id) {
                $this->usuarioModel->actualizarUsuario($id, $nombre, $apellido_1, $apellido_2, $is_admin, $url_foto);
                return 'Usuario Actualizado';
            }
            
            $this->usuarioModel->crearUsuario($username, $password, $nombre, $apellido_1, $apellido_2, $is_admin, $url_foto);
            return 'Usuario Creado';
        }
    } 

    public function mostrarPanelAdmin($fechaSeleccionada = null) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header("Location: index.php?error=Acceso+denegado");
            exit;
        }

        $fechaActiva = $fechaSeleccionada ?? date('Y-m-d');
        $listaBarberos = $this->usuarioModel->listarBarberosPorFecha($fechaActiva);
        $citasBrutas = $this->citaModel->citasTodosLosBarberosPorFecha($fechaActiva);
        
        $fechaObjeto = new DateTime($fechaActiva);
        $diasSemana = ['DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
        $meses = [1 => 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

        $nombreDia = $diasSemana[$fechaObjeto->format('w')];
        $diaNumero = $fechaObjeto->format('j');
        $nombreMes = $meses[(int)$fechaObjeto->format('n')];
        $anioActual = $fechaObjeto->format('Y');

        $fechaFormateada = "$nombreDia, $diaNumero DE $nombreMes $anioActual";

        $datos = [
            'totales'        => $this->citaModel->citasTotalesHoy($fechaActiva),
            'mis_citas'      => $this->citaModel->citasHoy($_SESSION['user_id'], $fechaActiva),        
            'barberos'       => array_map(fn($b) => strtoupper($b['nombre']), $listaBarberos),
            'barberos_datos' => $listaBarberos, 
            'citas_grid'     => $this->formatearCitasParaGrid($citasBrutas, $listaBarberos),
            'fecha_actual'   => $fechaActiva,
            'fecha_texto'    => $fechaFormateada,
            'clientes'       => $this->citaModel->listarClientes(),
            'servicios'      => $this->citaModel->listarServicios()
        ];

        return $datos;
    }

    private function formatearCitasParaGrid($listaCitas, $listaBarberos) {
        $citasFormateadas = [];
        $diccionarioBarberos = [];
        
        foreach($listaBarberos as $indice => $barbero) { 
            $diccionarioBarberos[$barbero['id']] = $indice + 2; 
        }

        foreach ($listaCitas as $citaActual) {
            $timestampInicio = strtotime($citaActual['fecha_cita']);
            $duracionMinutos = (int)$citaActual['duracion_total'];
            $timestampFin = $timestampInicio + ($duracionMinutos * 60);
            
            $horaInicio = (int)date('H', $timestampInicio);
            $minutosInicio = (int)date('i', $timestampInicio);
            $filaGrid = (($horaInicio - 9) * 2) + ($minutosInicio >= 30 ? 1 : 0) + 2;

            $claseColor = 'cita-verde'; 

            if ($citaActual['estado'] === 'Pagado') {
                $claseColor = 'cita-pagada';
            } else {
                if ($duracionMinutos > 30 && $duracionMinutos < 60) {
                    $claseColor = 'cita-naranja';
                }
                if ($duracionMinutos >= 60) {
                    $claseColor = 'cita-rojo-suave';
                }
            }

            $citasFormateadas[] = [
                'id'          => $citaActual['id'], 
                'estado'      => $citaActual['estado'],
                'columna'     => $diccionarioBarberos[$citaActual['id_usuario']] ?? 2,
                'fila'        => $filaGrid,
                'duracion'    => ceil($duracionMinutos / 30),
                'color_clase' => $claseColor,
                'cliente'     => strtoupper($citaActual['cliente_nombre']),
                'servicio'    => $citaActual['servicios_nombres'],
                'hora_inicio' => date('H:i', $timestampInicio),
                'hora_fin'    => date('H:i', $timestampFin)
            ];
        }
        return $citasFormateadas;
    }

    public function apiProximaCita() {
        header('Content-Type: application/json');
        date_default_timezone_set('Europe/Madrid');
        
        $fechaHoy = date('Y-m-d');
        $timestampActual = time();
        $minutosActuales = (int)date('i', $timestampActual);
        
        if ($minutosActuales > 0 && $minutosActuales <= 30) {
            $horaInicioBusqueda = date('H:30', $timestampActual);
        } else {
            $horaInicioBusqueda = date('H:00', strtotime('+1 hour', $timestampActual));
        }

        $listaBarberos = $this->usuarioModel->listarBarberosPorFecha($fechaHoy);        
        $citasHoy = $this->citaModel->citasTodosLosBarberosPorFecha($fechaHoy);

        $franjasHorarias = [];
        for ($horaAEvaluar = 9; $horaAEvaluar <= 20; $horaAEvaluar++) {
            $franjasHorarias[] = sprintf("%02d:00", $horaAEvaluar);
            $franjasHorarias[] = sprintf("%02d:30", $horaAEvaluar);
        }

        foreach ($franjasHorarias as $horaActual) {
            if ($horaActual >= $horaInicioBusqueda) {
                foreach ($listaBarberos as $barberoActual) {
                    $barberoOcupado = false;
                    
                    foreach ($citasHoy as $citaActual) {
                        $inicioTimestampCita = strtotime($citaActual['fecha_cita']);
                        $duracionMinutosCita = (int)$citaActual['duracion_total'];
                        $finTimestampCita = $inicioTimestampCita + ($duracionMinutosCita * 60);
                        $horaEvaluacionTimestamp = strtotime($fechaHoy . ' ' . $horaActual);

                        if ($citaActual['id_usuario'] == $barberoActual['id']) {
                            if ($horaEvaluacionTimestamp >= $inicioTimestampCita && $horaEvaluacionTimestamp < $finTimestampCita) {
                                $barberoOcupado = true;
                                break;
                            }
                        }
                    }

                    if (!$barberoOcupado) {
                        echo json_encode([
                            'encontrado' => true, 
                            'id_barbero' => $barberoActual['id'],
                            'barbero'    => strtoupper($barberoActual['nombre']), 
                            'hora'       => $horaActual
                        ]);
                        return;
                    }
                }
            }
        }
        
        echo json_encode(['encontrado' => false]);
    }

    public function procesarNuevaCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idBarbero = $_POST['id_barbero'] ?? null;
            $idCliente = $_POST['id_cliente'] ?? null;
            $fechaCita = $_POST['fecha_cita'] ?? null;
            $horaCita = $_POST['hora_cita'] ?? null;
            $listaServicios = $_POST['servicios'] ?? []; 

            if ($idBarbero && $idCliente && $fechaCita && $horaCita) {
                $fechaHoraExacta = $fechaCita . ' ' . $horaCita . ':00';
                $this->citaModel->agendarNuevaCita($idBarbero, $idCliente, $fechaHoraExacta, $listaServicios);
            }
            
            header("Location: index.php?accion=admin&fecha=" . $fechaCita);
            exit;
        }
    }

    public function apiHuecosDisponibles() {
        header('Content-Type: application/json');
        
        $idBarbero = $_GET['id_barbero'];
        $fechaCita = $_GET['fecha'];
        $duracionNecesariaMinutos = (int)$_GET['duracion'];

        $citasTotales = $this->citaModel->citasTodosLosBarberosPorFecha($fechaCita);
        $citasBarbero = array_filter($citasTotales, fn($c) => $c['id_usuario'] == $idBarbero);

        $huecosDisponibles = [];
        
        for ($hora = 9; $hora <= 20; $hora++) {
            foreach (["00", "30"] as $minutos) {
                $horaEvaluacion = sprintf("%02d:%s", $hora, $minutos);
                $timestampInicioEvaluacion = strtotime("$fechaCita $horaEvaluacion");
                $timestampFinRequerido = $timestampInicioEvaluacion + ($duracionNecesariaMinutos * 60);

                $bloqueLibre = true;
                
                foreach ($citasBarbero as $citaActual) {
                    $inicioCitaExistente = strtotime($citaActual['fecha_cita']);
                    $finCitaExistente = $inicioCitaExistente + ($citaActual['duracion_total'] * 60);

                    if ($timestampInicioEvaluacion < $finCitaExistente && $timestampFinRequerido > $inicioCitaExistente) {
                        $bloqueLibre = false;
                        break;
                    }
                }
                
                if ($bloqueLibre && $timestampFinRequerido <= strtotime("$fechaCita 21:00")) {
                    $huecosDisponibles[] = $horaEvaluacion;
                }
            }
        }
        
        echo json_encode($huecosDisponibles);
        exit;
    }

    public function guardarCita() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCliente = $_POST['id_cliente'] ?? null;
            $idBarbero = $_POST['id_usuario'] ?? null; 
            $fechaHoraCompleta = $_POST['fecha_cita'] ?? null;
            $listaServicios = $_POST['servicios'] ?? [];

            if ($idBarbero && $idCliente && $fechaHoraCompleta) {
                $operacionExitosa = $this->citaModel->agendarNuevaCita($idBarbero, $idCliente, $fechaHoraCompleta, $listaServicios);
                
                if ($operacionExitosa) {
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
            
            echo json_encode(['success' => false, 'error' => 'Error al guardar la cita en la base de datos.']);
        }
        exit;
    }

    public function mostrarPanelVentas() {
        $fechaHoy = date('Y-m-d');
        
        $datos = [
            'fecha_actual' => $fechaHoy,
            'citas_pendientes' => $this->citaModel->obtenerCitasPendientes($fechaHoy),
            'servicios' => $this->citaModel->listarServicios() 
        ];

        return $datos;
    }

    public function procesarCobro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCita = $_POST['id_cita'] ?? null;
            $metodoPago = $_POST['metodo_pago'] ?? 'Efectivo';
            
            if ($idCita) {
                $this->citaModel->marcarComoPagada($idCita);
            }
            
            header("Location: index.php?accion=venta");
            exit;
        }
    }

    public function mostrarEditarCita($id_cita) {
        $detalle = $this->citaModel->obtenerDetalleCita($id_cita);
        
        if (!$detalle) {
            header("Location: index.php?accion=admin");
            exit;
        }

        $fechaCita = date('Y-m-d', strtotime($detalle['fecha_cita']));

        $datos = [
            'cita'      => $detalle,
            'barberos'  => $this->usuarioModel->listarBarberosPorFecha($fechaCita),
            'servicios' => $this->citaModel->listarServicios()
        ];

        require_once 'app/views/Editar_Cita.php';
    }

    public function apiBarberosPorFecha() {
        header('Content-Type: application/json');
        $fechaConsulta = $_GET['fecha'] ?? date('Y-m-d');
        $listaBarberos = $this->usuarioModel->listarBarberosPorFecha($fechaConsulta);
        echo json_encode($listaBarberos);
        exit;
    }

    public function actualizarCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCita = $_POST['id_cita'] ?? null;
            $idBarbero = $_POST['id_usuario'] ?? null;
            $fechaCita = $_POST['fecha'] ?? null;
            $horaCita = $_POST['hora'] ?? null;
            $notasAdicionales = $_POST['notas'] ?? '';
            $listaServicios = $_POST['servicios'] ?? [];

            if ($idCita && $idBarbero && $fechaCita && $horaCita) {
                $fechaHoraExacta = $fechaCita . ' ' . $horaCita . ':00';
                $this->citaModel->actualizarCitaCompleta($idCita, $idBarbero, $fechaHoraExacta, $notasAdicionales, $listaServicios);
            }
            
            header("Location: index.php?accion=admin&fecha=" . $fechaCita);
            exit;
        }
    }

    public function mostrarFormularioNuevoCliente() {
        require_once 'app/views/Nuevo_Cliente.php';
    }

    public function procesarNuevoCliente() {
        $esPeticionValida = $_SERVER['REQUEST_METHOD'] === 'POST';
        
        if ($esPeticionValida) {
            $nombreCliente = trim($_POST['nombre'] ?? '');
            $apellidoCliente = trim($_POST['apellido'] ?? '');
            $telefonoCliente = trim($_POST['telefono'] ?? '');

            $datosRequeridosCompletos = !empty($nombreCliente) && !empty($telefonoCliente);

            if ($datosRequeridosCompletos) {
                $this->citaModel->registrarClienteBasico($nombreCliente, $apellidoCliente, $telefonoCliente);
            }
            
            header("Location: index.php?accion=nueva_cita");
            exit;
        }
    }

    public function mostrarGestionEquipo() {
        $datos = [
            'empleados' => $this->usuarioModel->obtenerEstadisticasEquipo()
        ];
        
        require_once 'app/views/Gestion_Equipo.php';
    }

    public function mostrarNuevoEmpleado() {
        require_once 'app/views/Nuevo_Empleado.php';
    }

    public function guardarNuevoEmpleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreEmpleado = trim($_POST['nombre'] ?? '');
            $apellidoEmpleado = trim($_POST['apellido'] ?? '');
            $nombreUsuario = trim($_POST['username'] ?? '');
            $contrasena = trim($_POST['password'] ?? '');

            if (!empty($nombreEmpleado) && !empty($nombreUsuario) && !empty($contrasena)) {
                $this->usuarioModel->crearUsuario($nombreUsuario, $contrasena, $nombreEmpleado, $apellidoEmpleado);
            }
            
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
    }

    public function mostrarEditarEmpleado($id) {
        $empleado = $this->usuarioModel->obtenerUsuarioPorId($id);
        
        if (!$empleado) {
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
        
        require_once 'app/views/Editar_Empleado.php';
    }

    public function actualizarEmpleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idEmpleado = $_POST['id_usuario'];
            $nombreEmpleado = $_POST['nombre'];
            $apellido1 = $_POST['apellido_1'];
            $apellido2 = $_POST['apellido_2'];
            $nombreUsuario = $_POST['username'];
            $contrasena = $_POST['password'];
            $esAdmin = isset($_POST['is_admin']);
            $estaActivo = isset($_POST['is_active']);

            $this->usuarioModel->actualizarUsuarioCompleto($idEmpleado, $nombreEmpleado, $apellido1, $apellido2, $nombreUsuario, $contrasena, $esAdmin, $estaActivo);
            
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
    }

    public function mostrarHorarioEmpleado($id) {
        $empleado = $this->usuarioModel->obtenerUsuarioPorId($id);
        
        if (!$empleado) {
            header("Location: index.php?accion=gestion_equipo");
            exit;
        }
        
        $fecha_ref = $_GET['semana'] ?? date('Y-m-d');
        $dt = new DateTime($fecha_ref);
        
        $diaSemana = $dt->format('N'); 
        $dt->modify('-' . ($diaSemana - 1) . ' days');
        $lunes = $dt->format('Y-m-d');
        
        $domingo = (clone $dt)->modify('+6 days')->format('Y-m-d');
        
        $semanaAnterior = (clone $dt)->modify('-7 days')->format('Y-m-d');
        $semanaSiguiente = (clone $dt)->modify('+7 days')->format('Y-m-d');

        $fechasTrabajo = $this->usuarioModel->obtenerFechasLaborables($id, $lunes, $domingo);
        
        require_once 'app/views/Horario_Empleado.php';
    }

    public function guardarHorarioEmpleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idEmpleado = $_POST['id_usuario'] ?? null;
            $listaFechasLaborables = $_POST['fechas'] ?? []; 
            $fechaInicioSemana = $_POST['fecha_inicio'] ?? null;
            $fechaFinSemana = $_POST['fecha_fin'] ?? null;

            if ($idEmpleado && $fechaInicioSemana && $fechaFinSemana) {
                $this->usuarioModel->actualizarFechasLaborables($idEmpleado, $listaFechasLaborables, $fechaInicioSemana, $fechaFinSemana);
            }
            
            header("Location: index.php?accion=horario_empleado&id=" . $idEmpleado . "&semana=" . $fechaInicioSemana . "&msg=ok");
            exit;
        }
    }

    public function mostrarEditarCliente($id) {
        $cliente = $this->citaModel->obtenerClientePorId($id);
        
        if (!$cliente) {
            header("Location: index.php?accion=gestion_clientes");
            exit;
        }
        
        require_once 'app/views/Editar_Cliente.php';
    }

    public function mostrarHorariosGlobales() {
        $fecha_ref = $_GET['semana'] ?? date('Y-m-d');
        $dt = new DateTime($fecha_ref);

        $diaSemana = $dt->format('N');
        $dt->modify('-' . ($diaSemana - 1) . ' days');
        $lunes = $dt->format('Y-m-d');

        $domingo = (clone $dt)->modify('+6 days')->format('Y-m-d');

        $semanaAnterior = (clone $dt)->modify('-7 days')->format('Y-m-d');
        $semanaSiguiente = (clone $dt)->modify('+7 days')->format('Y-m-d');

        $equipoSemanalo = $this->usuarioModel->obtenerHorariosGlobales($lunes, $domingo);

        require_once 'app/views/Horarios_Global.php';
    }

    public function mostrarGestionClientes() {
        require_once 'app/views/Gestion_Clientes.php';
    }

    public function apiBuscarClientes() {
        $terminoBusqueda = $_GET['s'] ?? '';
        $paginaActual = (int)($_GET['p'] ?? 1);
        
        if ($paginaActual < 1) {
            $paginaActual = 1;
        }
        
        $resultadosPorPagina = 10;
        $desplazamientoOffset = ($paginaActual - 1) * $resultadosPorPagina;

        $listaClientes = $this->citaModel->obtenerEstadisticasClientes($terminoBusqueda, $resultadosPorPagina, $desplazamientoOffset);
        $totalClientesRegistrados = $this->citaModel->contarTotalClientes($terminoBusqueda);
        $totalPaginasDisponibles = ceil($totalClientesRegistrados / $resultadosPorPagina) ?: 1; 
        
        $htmlGenerado = "";

        foreach ($listaClientes as $clienteActual) {
            $totalGastadoFormateado = number_format($clienteActual['total_gastado'], 2) . " €";
            $inicialesCliente = strtoupper(substr($clienteActual['nombre'], 0, 1) . substr($clienteActual['apellido_1'], 0, 1));
            $fechaVisita = $clienteActual['ultima_visita'] ? date('d M Y', strtotime($clienteActual['ultima_visita'])) : 'Nunca';
            
            $claseVip = ($clienteActual['total_gastado'] >= 300) ? 'badge-vip' : (($clienteActual['total_gastado'] > 0) ? 'badge-regular' : 'badge-nuevo');
            $textoEtiqueta = ($clienteActual['total_gastado'] >= 300) ? 'VIP' : (($clienteActual['total_gastado'] > 0) ? 'REGULAR' : 'NUEVO');

            $htmlGenerado .= "
            <div class='fila-cliente'>
                <div class='celda-perfil'>
                    <div class='avatar-circulo'>$inicialesCliente</div>
                    <span class='nombre-cliente'>{$clienteActual['nombre']} {$clienteActual['apellido_1']}</span>
                </div>
                <div class='texto-secundario'>{$clienteActual['telefono']}</div>
                <div class='texto-secundario'>$fechaVisita</div>
                <div class='texto-dorado-bold'>$totalGastadoFormateado</div>
                <div class='celda-centrada'><span class='$claseVip'>$textoEtiqueta</span></div>
                <div class='celda-acciones'>
                    <button class='btn-accion' onclick=\"abrirHistorial({$clienteActual['id']}, '{$clienteActual['nombre']} {$clienteActual['apellido_1']}')\">Historial</button>
                    <button class='btn-accion' onclick=\"window.location.href='index.php?accion=editar_cliente&id={$clienteActual['id']}'\">Editar</button>
                </div>
            </div>";
        }

        if (empty($listaClientes)) {
            $htmlGenerado .= "<div class='mensaje-vacio-clientes'>No se encontraron clientes.</div>";
        }

        $htmlGenerado .= "<div class='paginacion-contenedor'>";
        
        if ($paginaActual > 1) {
            $paginaAnterior = $paginaActual - 1;
            $htmlGenerado .= "<button onclick='buscar($paginaAnterior)' class='btn-accion btn-paginacion'>Anterior</button>";
        }

        $htmlGenerado .= "<span class='texto-paginacion'>Página $paginaActual de $totalPaginasDisponibles ($totalClientesRegistrados clientes)</span>";

        if ($paginaActual < $totalPaginasDisponibles) {
            $paginaSiguiente = $paginaActual + 1;
            $htmlGenerado .= "<button onclick='buscar($paginaSiguiente)' class='btn-accion btn-paginacion'>Siguiente</button>";
        }

        $htmlGenerado .= "</div>";

        echo $htmlGenerado;
        exit;
    }

    public function guardarEdicionCliente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCliente = $_POST['id'] ?? null;
            $nombreCliente = trim($_POST['nombre'] ?? '');
            $apellidoCliente = trim($_POST['apellido'] ?? '');
            $telefonoCliente = trim($_POST['telefono'] ?? '');
            $notasAdicionales = trim($_POST['notas'] ?? '');

            if (!preg_match('/^[0-9]{9}$/', $telefonoCliente)) {
                header("Location: index.php?accion=editar_cliente&id=$idCliente&error=telefono");
                exit;
            }

            if ($idCliente && $nombreCliente && $telefonoCliente) {
                $this->citaModel->actualizarCliente($idCliente, $nombreCliente, $apellidoCliente, $telefonoCliente, $notasAdicionales);
            }
            
            header("Location: index.php?accion=gestion_clientes");
            exit;
        }
    }

    public function apiHistorialCliente() {
        $idCliente = $_GET['id'] ?? null;
        
        if (!$idCliente) {
            exit;
        }
        
        $historialCitas = $this->citaModel->obtenerHistorialCliente($idCliente);
        
        if (empty($historialCitas)) {
            echo "<div class='mensaje-historial-vacio'>Este cliente aún no tiene citas completadas.</div>";
            exit;
        }

        foreach ($historialCitas as $citaActual) {
            $fechaFormateada = date('d M Y', strtotime($citaActual['fecha_cita']));
            $horaFormateada = date('H:i', strtotime($citaActual['fecha_cita']));
            $totalCobrado = number_format($citaActual['total'], 2) . "€";
            $nombreBarbero = strtoupper($citaActual['barbero']);

            echo "
            <div class='tarjeta-historial'>
                <div>
                    <div class='historial-fecha'>$fechaFormateada <span class='historial-hora'>a las $horaFormateada</span></div>
                    <div class='historial-servicios'>{$citaActual['servicios']}</div>
                    <div class='historial-barbero'>Atendido por: $nombreBarbero</div>
                </div>
                <div class='historial-precio'>
                    $totalCobrado
                </div>
            </div>";
        }
        exit;
    }

    public function mostrarInventario() {
        $productos = $this->productoModel->listarProductos();

        $valor_total = 0;
        foreach ($productos as $productoActual) {
            $valor_total += ($productoActual['stock'] * $productoActual['precio']);
        }

        $datos = [
            'productos'       => $productos,
            'total_productos' => count($productos),
            'valor_total'     => $valor_total
        ];

        require_once 'app/views/Inventario.php';
    }

    public function guardarProducto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreProducto = trim($_POST['nombre'] ?? '');
            $stockInicial = (int)($_POST['stock'] ?? 0);
            $alertaStockMinimo = (int)($_POST['stock_minimo'] ?? 5);
            $precioVenta = (float)($_POST['precio'] ?? 0);
            $precioCoste = (float)($_POST['precio_coste'] ?? 0); 

            if (!empty($nombreProducto)) {
                $this->productoModel->crearProducto($nombreProducto, $stockInicial, $alertaStockMinimo, $precioVenta, $precioCoste);
            }
            
            header("Location: index.php?accion=inventario");
            exit;
        }
    }

    public function ajustarStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProducto = $_POST['id_producto'] ?? null;
            $nuevoStockExacto = (int)($_POST['nuevo_stock'] ?? 0);

            if ($idProducto) {
                $this->productoModel->actualizarStock($idProducto, $nuevoStockExacto);
            }
            
            header("Location: index.php?accion=inventario");
            exit;
        }
    }

    public function sumarStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProducto = $_POST['id_producto'] ?? null;
            $cantidadUnidades = (int)($_POST['cantidad_sumar'] ?? 0);

            if ($idProducto && $cantidadUnidades > 0) {
                $this->productoModel->sumarStock($idProducto, $cantidadUnidades);
            }
            
            header("Location: index.php?accion=inventario");
            exit;
        }
    }

    public function eliminarProducto() {
        $idProducto = $_GET['id'] ?? null;
        
        if ($idProducto) {
            $this->productoModel->eliminarProducto($idProducto);
        }
        
        header("Location: index.php?accion=inventario");
        exit;
    }
}
?>