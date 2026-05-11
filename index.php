<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'app/config/db.php';
require_once 'app/models/Usuario.php';
require_once 'app/models/Cita.php';
require_once 'app/models/Producto.php';
require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/UsuarioController.php';

$conexionBaseDatos = Database::getConnection();
$accionSolicitada = $_GET['accion'] ?? 'mostrar_login';

switch ($accionSolicitada) {
    
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controladorAutenticacion = new AuthController($conexionBaseDatos);
            $controladorAutenticacion->procesarLogin();
        }
        break;
        
    case 'logout':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Vaciamos y destruimos la sesión
        session_unset();
        session_destroy();
        // Redirigimos al login
        header("Location: index.php");
        exit;

    case 'admin':
        $controladorUsuario = new UsuarioController();
        $fechaSeleccionada = $_GET['fecha'] ?? null;
        $datos = $controladorUsuario->mostrarPanelAdmin($fechaSeleccionada);
        require_once 'app/views/Adm_Home.php';
        break;

    case 'guardar_cita':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->procesarNuevaCita();
        break;
        
    case 'agendar_cita':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->guardarCita();
        break;

    case 'venta':
        $controladorUsuario = new UsuarioController();
        $datos = $controladorUsuario->mostrarPanelVentas(); 
        require_once 'app/views/Venta.php';
        break;

    case 'procesar_cobro':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->procesarCobro();
        break;

    case 'eliminar_cita':
        if (isset($_GET['id'])) {
            $modeloCita = new Cita();
            $modeloCita->eliminarCita($_GET['id']);
        }
        header("Location: index.php?accion=admin");
        exit;

    case 'confirmar_cita':
        if (isset($_GET['id'])) {
            $modeloCita = new Cita();
            $modeloCita->confirmarCita($_GET['id']);
        }
        header("Location: index.php?accion=admin");
        exit;

    case 'editar_cita':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarEditarCita($_GET['id']);
        break;

    case 'actualizar_cita':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->actualizarCita();
        break;

    case 'gestion_clientes':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarGestionClientes();
        break;

    case 'nuevo_cliente':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarFormularioNuevoCliente();
        break;

    case 'guardar_cliente':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->procesarNuevoCliente();
        break;

    case 'editar_cliente':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarEditarCliente($_GET['id']);
        break;

    case 'guardar_edicion_cliente':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->guardarEdicionCliente();
        break;

    case 'api_buscar_clientes':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->apiBuscarClientes();
        break;

    case 'api_historial_cliente':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->apiHistorialCliente();
        break;

    case 'api_proxima_cita':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->apiProximaCita();
        break;

    case 'nueva_cita':
        $controladorUsuario = new UsuarioController();
        $datos = $controladorUsuario->mostrarPanelAdmin(); 
        require_once 'app/views/Nueva_Cita.php';
        break;

    case 'api_huecos_disponibles':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->apiHuecosDisponibles();
        break;

    case 'api_barberos_fecha':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->apiBarberosPorFecha();
        break;

    case 'invitado':
        require_once 'app/views/Inv_Home.php';
        break;

    case 'empleado':
        require_once 'app/views/Emp_Home.php';
        break;

    case 'gestion_equipo':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarGestionEquipo();
        break;

    case 'nuevo_empleado':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarNuevoEmpleado();
        break;

    case 'guardar_empleado':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->guardarNuevoEmpleado();
        break;

    case 'editar_empleado':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarEditarEmpleado($_GET['id']);
        break;

    case 'actualizar_empleado':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->actualizarEmpleado();
        break;

    case 'horario_empleado':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarHorarioEmpleado($_GET['id']);
        break;

    case 'guardar_horario':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->guardarHorarioEmpleado();
        break;

    case 'horarios_globales':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarHorariosGlobales();
        break;

    case 'inventario':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->mostrarInventario();
        break;

    case 'guardar_producto':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->guardarProducto();
        break;

    case 'ajustar_stock':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->ajustarStock();
        break;

    case 'sumar_stock':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->sumarStock();
        break;

    case 'eliminar_producto':
        $controladorUsuario = new UsuarioController();
        $controladorUsuario->eliminarProducto();
        break;

    default:
        require_once 'app/views/login.php';
        break;
}